# Basic Examples

## Source Data
```JSON
{
    "brand": "Toyota",
    "model": "Corolla",
    "year": 2021,
    "price": 20000.00
}
```

```NEON
brand: Toyota
model: Corolla
year: 2021
price: 20000.00
```

```XML
<car>
    <brand>Toyota</brand>
    <model>Corolla</model>
    <year>2021</year>
    <price>20000.00</price>
</car>
```

```YAML
brand: Toyota
model: Corolla
year: 2021
price: 20000.00
```

## Approaches

### Constructor
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

$dataMapper = new DataMapper($dataConfig);
$car = $dataMapper->json($json, Car::class);
$car = $dataMapper->xml($xml, Car::class);
```

### Property
```php
<?php

class Car
{
    public string $brand;
    public string $model;
    public int $year;
    public float $price;
}
```

```php
<?php

use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;

/** DataConfig is optional, because approach construct is the default setting */
$dataConfig = new DataConfig(
    approachEnum: ApproachEnum::PROPERTY,
);

$dataMapper = new DataMapper($dataConfig);
$car = $dataMapper->array($array, Car::class);
$car = $dataMapper->json($json, Car::class);
$car = $dataMapper->neon($neon, Car::class);
$car = $dataMapper->xml($xml, Car::class);
$car = $dataMapper->yaml($yaml, Car::class);
```

### Setter
```php
<?php

class Car
{
    private string $brand;
    private string $model;
    private int $year;
    private float $price;
    
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }
    
    public function setModel(string $model): void
    {
        $this->model = $model;
    }
    
    public function setYear(int $year): void
    {
        $this->year = $year;
    }
    
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
```

```php
<?php

use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;

/** DataConfig is optional, because approach construct is the default setting */
$dataConfig = new DataConfig(
    approachEnum: ApproachEnum::SETTER,
);

$dataMapper = new DataMapper($dataConfig);
$car = $dataMapper->array($array, Car::class);
$car = $dataMapper->json($json, Car::class);
$car = $dataMapper->neon($neon, Car::class);
$car = $dataMapper->xml($xml, Car::class);
$car = $dataMapper->yaml($yaml, Car::class);
```