# Wundii\Data-Mapper

[![PHP-Tests](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml/badge.svg)](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://phpstan.org/)

This mapper relies on strict data types, dependency capability and convenient processing for mapping xml, json and arrays into objects.

## Features
- Mapping source data into objects
- Initialize object via constructor, properties or methods
- Map nested objects, arrays of objects
- Class mapping for interfaces or other classes
- Psr\Log compatible

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
- `enum`

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
 * - ApproachEnum::CONSTRUCTOR - will use the constructor to map the data
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
$dataMapper = new DataMapper();

$testClass = $dataMapper->array($array, TestClass::class, $dataConfig);
$testClass = $dataMapper->json($json, TestClass::class, $dataConfig);
$testClass = $dataMapper->xml($xml, TestClass::class, $dataConfig);
```

## ToDo`s for the first release
- [ ] Psr\Log implementation
- [x] JsonSourceData implementation
- [x] Json unit test
- [x] Performance issue

### optional ToDo`s
- [ ] `ElementObjectResolver->CreateInstance(...)` private properties/methods
- [ ] XmlSourceData unit test
- [ ] JsonSourceData phpstan issues
- [ ] Smaller performance todos