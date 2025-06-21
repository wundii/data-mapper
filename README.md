<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/wundii/data-mapper/refs/heads/main/assets/data-mapper-dark.png">
    <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/wundii/data-mapper/refs/heads/main/assets/data-mapper-light.png">
    <img src="https://raw.githubusercontent.com/wundii/data-mapper/refs/heads/main/assets/data-mapper-light.png" alt="wundii/data-mapper" style="width: 100%; max-width: 600px; height: auto;">
  </picture>
</p>

[![PHP-Tests](https://img.shields.io/github/actions/workflow/status/wundii/data-mapper/code_quality.yml?branch=main&style=for-the-badge)](https://github.com/wundii/data-mapper/actions/workflows/code_quality.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg?style=for-the-badge)](https://phpstan.org/)
![VERSION](https://img.shields.io/packagist/v/wundii/data-mapper?style=for-the-badge)
[![PHP](https://img.shields.io/packagist/php-v/wundii/data-mapper?style=for-the-badge)](https://www.php.net/)
[![Rector](https://img.shields.io/badge/Rector-8.2-blue.svg?style=for-the-badge)](https://getrector.com)
[![ECS](https://img.shields.io/badge/ECS-check-blue.svg?style=for-the-badge)](https://tomasvotruba.com/blog/zen-config-in-ecs)
[![PHPUnit](https://img.shields.io/badge/PHP--Unit-check-blue.svg?style=for-the-badge)](https://phpunit.org)
[![codecov](https://img.shields.io/codecov/c/github/wundii/data-mapper/main?token=TNC2MM0MWS&style=for-the-badge)](https://codecov.io/github/wundii/data-mapper)
[![Downloads](https://img.shields.io/packagist/dt/wundii/data-mapper.svg?style=for-the-badge)](https://packagist.org/packages/wundii/data-mapper)

This library is an extremely fast and strictly typed object mapper built for modern PHP (8.2+). It seamlessly transforms data from formats like JSON, NEON, XML, YAML, arrays, and standard objects into well-structured PHP objects.

Ideal for developers who need reliable and efficient data mapping without sacrificing code quality or modern best practices.

## Features
- Mapping source data into objects
- Mapping source data with a list of elements into a list of objects
- Initialize object via constructor, properties or methods
- Map nested objects, arrays of objects
- Class mapping for interfaces or other classes
- Custom root element for starting with the source data
- Auto-casting for `float` types (eu to us decimal separator)
- Target alias via Attribute for properties and methods

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
optional formats are marked with an asterisk `*`
- `array`
- `json`
- `neon`*
- `object`
  - `public property`
  - `public getters`
  - `method toArray()`
  - `attribute SourceData('...')`
- `xml`
- `yaml`*

## Installation
Require the bundle and its dependencies with composer:

```bash
composer require wundii/data-mapper
```

### Installations for frameworks
- [Laravel Package](https://github.com/wundii/data-mapper-laravel-package)
- [Symfony Bundle](https://github.com/wundii/data-mapper-symfony-bundle)

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
$testClass = $dataMapper->neon($neon, TestClass::class);
$testClass = $dataMapper->xml($xml, TestClass::class);
$testClass = $dataMapper->yaml($yaml, TestClass::class);
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
$testClass = $dataMapper->neon($neon, TestClass::class);
$testClass = $dataMapper->xml($xml, TestClass::class);
$testClass = $dataMapper->yaml($yaml, TestClass::class);
```
