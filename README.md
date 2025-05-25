# Wundii\Data-Mapper

[![PHP-Tests](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml/badge.svg)](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://phpstan.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg?style=flat)](https://www.php.net/)
[![Rector](https://img.shields.io/badge/Rector-8.2-blue.svg?style=flat)](https://getrector.com)
[![ECS](https://img.shields.io/badge/ECS-check-blue.svg?style=flat)](https://tomasvotruba.com/blog/zen-config-in-ecs)
[![PHPUnit](https://img.shields.io/badge/PHP--Unit-check-blue.svg?style=flat)](https://phpunit.org)
[![Downloads](https://img.shields.io/packagist/dt/wundii/data-mapper.svg?style=flat)](https://packagist.org/packages/wundii/data-mapper)

This is a modern php 8.2+ mapper relies on strict data types, dependency capability and convenient processing for mapping xml, json and arrays into objects.

## Features
- Mapping source data into objects
- Mapping source data with a list of elements into a list of objects
- Initialize object via constructor, properties or methods
- Map nested objects, arrays of objects
- Class mapping for interfaces or other classes
- Custom root element for starting with the source data
- Auto-casting for `float` types (eu to us decimal separator)

## Supported Types
- `null`
- `bool`|`?bool`
- `int`|`?int`
- `float`|`?float`
- `string`|`?string`
- `array`
  - `int[]`
  - `float[]`
  - `string[]`
  - `object[]`
- `object`|`?object`
- `enum`|`?enum`

## Supported Formats
- `array`
- `json`
- `xml`

## Installation
Require the bundle and its dependencies with composer:

> composer require wundii/data-mapper

## Usage
### Minimal usage
```php
use Wundii\DataMapper\DataMapper;

/**
 * DataConfig default settings
 * - ApproachEnum::SETTER - will use the constructor to map the data
 * - AccessibleEnum::PUBLIC - will use only public properties/methods
 * - classMap = [] - will not map any classes 
 */

$dataMapper = new DataMapper();

$testClass = $dataMapper->array($array, TestClass::class);
$testClass = $dataMapper->json($json, TestClass::class);
$testClass = $dataMapper->xml($xml, TestClass::class);
```

### Usage with custom configuration
```php
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

$dataConfig = new DataConfig(
        approachEnum: ApproachEnum::PROPERTY,
        classMap: [
            DateTimeInterface::class => DateTime::class,
        ],
    );
$dataMapper = new DataMapper($dataConfig);

$testClass = $dataMapper->array($array, TestClass::class);
$testClass = $dataMapper->json($json, TestClass::class);
$testClass = $dataMapper->xml($xml, TestClass::class);
```

### ToDo`s
- [x] `ElementObjectResolver->CreateInstance(...)` private properties/methods
- [ ] JsonSourceData phpstan issues
- [x] Smaller performance todos