<?php

namespace Zentropy;

use Zentropy\Exceptions\AuthException;

class Client
{
    private Connection $conn;

    private function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * Create a client using TCP connection with optional password
     */
    public static function tcp(string $host = '127.0.0.1', int $port = 6383, ?string $password = null): self
    {
        $conn = new Connection($host, $port);
        $client = new self($conn);

        if ($password !== null) {
            $client->auth($password);
        }

        return $client;
    }

    /**
     * Create a client using Unix socket (no auth required)
     */
    public static function unixSocket(string $path): self
    {
        $conn = new Connection(null, null, $path);
        return new self($conn);
    }

    private function _send(string $cmd): string
    {
        $this->conn->send($cmd . "\n");
        return trim($this->conn->recv());
    }

    public function auth(string $password): bool
    {
        $resp = $this->_send("AUTH $password");
        if ($resp !== '+OK') {
            throw new AuthException("Authentication failed: $resp");
        }
        return true;
    }

    public function set(string $key, string $value): bool
    {
        return $this->_send("SET $key '$value'") === '+OK';
    }

    public function get(string $key): ?string
    {
        $resp = $this->_send("GET $key");
        return $resp === 'NONE' ? null : $resp;
    }

    public function delete(string $key): bool
    {
        return $this->_send("DELETE $key") === '+OK';
    }

    public function exists(string $key): bool
    {
        return $this->_send("EXISTS $key") === '1';
    }

    public function ping(): bool
    {
        return $this->_send("PING") === '+PONG';
    }

    public function close(): void
    {
        $this->conn->close();
    }

    public function __destruct()
    {
        $this->close();
    }
}
