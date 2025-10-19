<?php

namespace Zentropy;

use Exception;

class Connection
{
    private $socket;

    public function __construct(?string $host = '127.0.0.1', ?int $port = 6383, ?string $unixSocket = null)
    {
        if ($unixSocket) {
            $this->socket = @stream_socket_client("unix://$unixSocket", $errno, $errstr, 5);
        } else {
            $this->socket = @stream_socket_client("tcp://$host:$port", $errno, $errstr, 5);
        }

        if (!$this->socket) {
            throw new Exception("Connection failed: $errstr ($errno)");
        }
    }

    public function send(string $data): void
    {
        if (!is_resource($this->socket)) {
            throw new Exception("Cannot send data: socket is invalid");
        }
        fwrite($this->socket, $data);
    }

    public function recv(): string
    {
        if (!is_resource($this->socket)) {
            throw new Exception("Cannot receive data: socket is invalid");
        }
        return fgets($this->socket, 1024);
    }

    public function close(): void
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
        }
    }
}
