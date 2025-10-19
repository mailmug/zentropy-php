<?php

namespace Zentropy\Tests;

use PHPUnit\Framework\TestCase;
use Zentropy\Client;
use Zentropy\Connection;
use Zentropy\Exceptions\AuthException;

class ClientTest extends TestCase
{
    private $mockConn;

    protected function setUp(): void
    {
        // Create a mock for the Connection class
        $this->mockConn = $this->getMockBuilder(Connection::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['send', 'recv', 'close'])
                               ->getMock();
    }

    private function injectMockConnection(Client $client): void
    {
        $reflection = new \ReflectionClass($client);
        $prop = $reflection->getProperty('conn');
        $prop->setValue($client, $this->mockConn);
    }

    public function testAuthSuccess(): void
    {
        $this->mockConn->expects($this->once())
                       ->method('send')
                       ->with("AUTH pass@123\n");
        $this->mockConn->expects($this->once())
                       ->method('recv')
                       ->willReturn("+OK\n");

        $client = Client::tcp('127.0.0.1', 6383, 'pass@123');
        $this->injectMockConnection($client);

        $this->assertTrue($client->auth('pass@123'));
    }

    public function testAuthFailure(): void
    {
        $this->mockConn->method('send');
        $this->mockConn->method('recv')->willReturn("-ERR\n");

        $client = Client::tcp('127.0.0.1', 6383);
        $this->injectMockConnection($client);

        $this->expectException(AuthException::class);
        $client->auth('wrongpass');
    }

    public function testSetAndGet(): void
    {
        $this->mockConn->method('send');
        $this->mockConn->method('recv')
                       ->willReturnOnConsecutiveCalls("+OK\n", "value123\n");

        $client = Client::tcp('127.0.0.1', 6383);
        $this->injectMockConnection($client);

        $this->assertTrue($client->set('key1', 'value123'));
        $this->assertSame('value123', $client->get('key1'));
    }

    public function testDeleteAndExists(): void
    {
        $this->mockConn->method('send');
        $this->mockConn->method('recv')
                       ->willReturnOnConsecutiveCalls("+OK\n", "1\n", "0\n");

        $client = Client::tcp('127.0.0.1', 6383);
        $this->injectMockConnection($client);

        $this->assertTrue($client->delete('key1'));
        $this->assertTrue($client->exists('key1'));
        $this->assertFalse($client->exists('key2'));
    }

    public function testPing(): void
    {
        $this->mockConn->method('send');
        $this->mockConn->method('recv')->willReturn("+PONG\n");

        $client = Client::tcp('127.0.0.1', 6383);
        $this->injectMockConnection($client);

        $this->assertTrue($client->ping());
    }
}
