# Basic Examples

## Source Data
```CSV
brand,model,year,price
Toyota,Corolla,2021,20000.00
Honda,Civic,2020,18000.00
```

```JSON
[
  {
    "brand": "Toyota",
    "model": "Corolla",
    "year": 2021,
    "price": 20000.00
  },
  {
    "brand": "Toyota",
    "model": "Corolla",
    "year": 2022,
    "price": 22000.00
  }
]
```

```NEON
- string: Toyota
  model: Corolla
  year: 2021
  price: 20000.00
- string: Toyota
  model: Corolla
  year: 2022
  price: 22000.00
```

```XML
<root>
    <car>
        <brand>Toyota</brand>
        <model>Yaris</model>
        <year>2021</year>
        <price>20000.00</price>
    </car>
    <car>
        <brand>Toyota</brand>
        <model>Yaris</model>
        <year>2022</year>
        <price>22000.00</price>
    </car>
</root>
```

```YAML
- string: Toyota
  model: Corolla
  year: 2021
  price: 20000.00
- string: Toyota
  model: Corolla
  year: 2022
  price: 22000.00
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
$listOfCars = $dataMapper->array($array, Car::class);
$listOfCars = $dataMapper->csv($csvFileOrContent, Car::class);
$listOfCars = $dataMapper->json($json, Car::class);
$listOfCars = $dataMapper->neon($neon, Car::class);
$listOfCars = $dataMapper->xml($xml, Car::class);
$listOfCars = $dataMapper->yaml($yaml, Car::class);
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
$listOfCars = $dataMapper->array($array, Car::class);
$listOfCars = $dataMapper->csv($csvFileOrContent, Car::class);
$listOfCars = $dataMapper->json($json, Car::class);
$listOfCars = $dataMapper->neon($neon, Car::class);
$listOfCars = $dataMapper->xml($xml, Car::class);
$listOfCars = $dataMapper->yaml($yaml, Car::class);
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
$listOfCars = $dataMapper->array($array, Car::class);
$listOfCars = $dataMapper->csv($csvFileOrContent, Car::class);
$listOfCars = $dataMapper->json($json, Car::class);
$listOfCars = $dataMapper->neon($neon, Car::class);
$listOfCars = $dataMapper->xml($xml, Car::class);
$listOfCars = $dataMapper->yaml($yaml, Car::class);
```