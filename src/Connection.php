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

    public function recv()
    {  
        if (!is_resource($this->socket)) {
            throw new Exception("Cannot receive data: socket is invalid");
        }
        
        $line = fgets($this->socket, 1024);
        
        if ($line === false || $line === '') {
            throw new Exception("Failed to read Zentropy response");
        }
        $line = rtrim($line, "\r\n");
        $prefix = $line[0];
        $data = substr($line, 1);
        
        switch ($prefix) {
            case '+': // Simple String
                return $data;
                
            case '-': // Error
                throw new Exception("Zentropy error: $data");
                
            case ':': // Integer
                return (int)$data;
                
            case '$': // Bulk String
                $length = (int)$data;
                if ($length === -1) return null; // Null bulk string
                return $this->recvLength($length + 2); // +2 for CRLF
                
                
            default:
                throw new Exception("Unknown Zentropy response prefix: $prefix");
        }
    }

    public function recvLength(int $length): string
    {
        if (!is_resource($this->socket)) {
            throw new Exception("Cannot receive data: socket is invalid");
        }
        $data = '';
        $remaining = $length;
        
        while ($remaining > 0) {
            $chunk = fread($this->socket, $remaining);
            
            if ($chunk === false || $chunk === '') {
                throw new Exception("Failed to read expected length from socket");
            }
            
            $data .= $chunk;
            $remaining -= strlen($chunk);
        }
        return rtrim($data, "\r\n");
    }

    public function close(): void
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
        }
    }
}
