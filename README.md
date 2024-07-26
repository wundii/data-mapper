# data-mapper

[![PHP-Tests](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml/badge.svg)](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://phpstan.org/)

mapping xml, json and arrays into objects

## Installation
Require the bundle and its dependencies with composer:

> composer require wundii/data-mapper

## Usage
```php
use DataMapper\DataConfig;
use DataMapper\DataMapper;

$dataConfig = new DataConfig();
$dataMapper = new DataMapper($dataConfig);

$testClass = $dataMapper->xml($xml, TestClass::class);
```