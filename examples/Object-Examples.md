# Object Examples

## Source Data with toArray Method
```php
<?php

class Vehicle
{
    public function __construct(
        private string $manufacturer,
        private string $model,
        private int $year,
        private float $cost,
    ) {
    }
    
    public function toArray(): array
    {
        return [;
            'brand' => $this->manufacturer,
            'model' => $this->model,
            'year' => $this->year,
            'price' => $this->cost,
        ];
    }
}
```

## Source Data with public properties
```php
<?php

class Vehicle
{
    public function __construct(
        public string $brand,
        public string $model,
        public int $year,
        public float $price,
    ) {
    }
}
```

## Source Data with public getters
```php
<?php

class Vehicle
{
    public function __construct(
        private string $brand,
        private string $model,
        private int $year,
        private float $price,
    ) {
    }
    
    public function getBrand(): string
    {
        return $this->brand;
    }
    
    public function getModel(): string
    {
        return $this->model;
    }
    
    public function getYear(): int
    {
        return $this->year;
    }
    
    public function getPrice(): float
    {
        return $this->price;
    }
}
```

## Target Class with Constructor
```php
<?php

class Car
{
    public function __construct(
        private string $brand,
        private string $model,
        private int $year,
        private float $price,
    ) {
    }
    
    public function getBrand(): string
    {
        return $this->brand;
    }
    
    public function getModel(): string
    {
        return $this->model;
    }
    
    public function getYear(): int
    {
        return $this->year;
    }
    
    public function getPrice(): float
    {
        return $this->price;
    }
}
```

```php
<?php

use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;

/** DataConfig is optional, because approach construct is the default setting */
$dataConfig = new DataConfig(
    approachEnum: ApproachEnum::CONSTRUCTOR,
);
$vehicle = new Vehicle('Toyota', 'Corolla', 2020, 20000.0);

$dataMapper = new DataMapper($dataConfig);
$car = $dataMapper->object($vehicle, Car::class);
```
