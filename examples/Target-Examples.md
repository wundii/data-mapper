# Target with an alias Examples
You can use the `TargetData` attribute to map data from various formats to your PHP classes. 
This is useful when you want to ensure that the data is correctly assigned to the properties or methods of your class.

## Source Data
```JSON
{
    "manufacture": "Toyota",
    "model": "Corolla",
    "produced": 2021,
    "costs": 20000.00
}
```

```NEON
manufacture: Toyota
model: Corolla
produced: 2021
costs: 20000.00
```

```XML
<car>
    <manufacture>Toyota</manufacture>
    <model>Corolla</model>
    <produced>2021</produced>
    <costs>20000.00</costs>
</car>
```

```YAML
manufacture: Toyota
model: Corolla
produced: 2021
costs: 20000.00
```

## Approaches

### Constructor
```php
<?php

use Wundii\DataMapper\Attribute\TargetData;

class Car
{
    public function __construct(
        #[TargetData('manufacture')]
        private string $brand,
        private string $model,
        #[TargetData('produced')]
        private int $year,
        #[TargetData('costs')]
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

use Wundii\DataMapper\Attribute\TargetData;

class Car
{
    #[TargetData('manufacture')]
    public string $brand;
    public string $model;
    #[TargetData('produced')]
    public int $year;
    #[TargetData('costs')]
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

use Wundii\DataMapper\Attribute\TargetData;

class Car
{
    private string $brand;
    private string $model;
    private int $year;
    private float $price;
    
    #[TargetData('manufacture')]
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }
    
    public function setModel(string $model): void
    {
        $this->model = $model;
    }
    
    #[TargetData('produced')]
    public function setYear(int $year): void
    {
        $this->year = $year;
    }
    
    #[TargetData('costs')]
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