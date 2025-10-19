# Zentropy PHP Client

<a href="https://packagist.org/packages/mailmug/zentropy-php"><img src="https://img.shields.io/packagist/v/mailmug/zentropy-php" alt="Latest Stable Version"></a><a href="LICENSE"><img src="https://badgen.net/github/license/mailmug/zentropy" /></a>


A simple and professional PHP client for **[Zentropy](https://github.com/mailmug/zentropy)**. Supports **TCP connections with authentication** and **Unix socket connections**.

---

## Features

- Connect to Zentropy server over TCP or Unix socket.
- Optional password authentication for TCP connections.
- Common commands: `SET`, `GET`, `DELETE`, `EXISTS`, `PING`.
- Easy to integrate in **any PHP project**, including **Laravel**.
- Minimal dependencies, PSR-4 autoloading.

---

## Installation

Use Composer to install:

```bash
composer require mailmug/zentropy-php
```

## Usage

### TCP Connection (with optional password)

```php
<?php

require 'vendor/autoload.php';

use Zentropy\Client;

$client = Client::tcp('127.0.0.1', 6383, 'pass@123');

$client->set('foo', 'bar');
echo $client->get('foo'); // Outputs: bar
$client->close();

```

### Unix Socket Connection

```php
<?php

require 'vendor/autoload.php';

use Zentropy\Client;

$client = Client::unixSocket('/tmp/zentropy.sock');

$client->set('foo', 'bar');
echo $client->get('foo'); // Outputs: bar
$client->close();
```

## API Reference
| Method                                 | Description                                                  |
| -------------------------------------- | ------------------------------------------------------------ |
| `Client::tcp($host, $port, $password)` | Create a TCP client with optional password.                  |
| `Client::unixSocket($path)`            | Create a client using a Unix socket.                         |
| `set(string $key, string $value)`      | Set a key-value pair.                                        |
| `get(string $key)`                     | Get the value of a key. Returns `null` if key doesn't exist. |
| `delete(string $key)`                  | Delete a key. Returns `true` if successful.                  |
| `exists(string $key)`                  | Check if a key exists.                                       |
| `ping()`                               | Ping the server. Returns `true` if alive.                    |
| `close()`                              | Close the connection.                                        |
| `auth(string $password)`               | Authenticate TCP connection (internal for TCP only).         |


## Running Examples


## Contributing

1. Fork the repository.

2. Run composer install.

3. Add tests in tests/ and examples in examples/.

4. Submit a pull request.
