<?php

require __DIR__ . '/../vendor/autoload.php';

use Zentropy\Client;
use Zentropy\Exceptions\AuthException;

try {
    // Connect to Zentropy server
    $client = Client::tcp('127.0.0.1', 6383);

    // Ping server
    if ($client->ping()) {
        echo "Server is alive!\n";
    }

    // Set a key
    if ($client->set('foo', 'bar')) {
        echo "Key 'foo' set to 'bar'.\n";
    }

    // Get a key
    $value = $client->get('foo');
    echo "Value of 'foo': " . ($value ?? 'NULL') . "\n";

    // Check if a key exists
    if ($client->exists('foo')) {
        echo "'foo' exists!\n";
    }

    // Delete a key
    if ($client->delete('foo')) {
        echo "'foo' deleted.\n";
    }

    // Close connection
    $client->close();

} catch (AuthException $e) {
    echo "Authentication failed: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}