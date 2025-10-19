# Zentropy PHP Client

[![Packagist Version](https://img.shields.io/packagist/v/mailmug/zentropy-php)](https://packagist.org/packages/mailmug/zentropy-php)
[![License](https://img.shields.io/packagist/l/mailmug/zentropy-php)](LICENSE)

A simple and professional PHP client for **Zentropy server**. Supports **TCP connections with authentication** and **Unix socket connections**.

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