# Expanded Examples

## Source Data
```JSON
{
    "name": "John Doe",
    "gender": "male", 
    "birthday": "1990-01-01",
    "todoList": [
        "Buy milk",
        "Pay bills",
        "Call mom"
    ],
    "currentCar": {
        "brand": "Toyota",
        "model": "Corolla",
        "year": 2021,
        "price": 20000.00
    },
    "previousCars": 
    [
        {
            "brand": "Toyota",
            "model": "Corolla",
            "year": 2020,
            "price": 18000.00
        },
        {
            "brand": "Toyota",
            "model": "Corolla",
            "year": 2019,
            "price": 16000.00
        }
    ]
}
```

```XML
<user>
    <name>John Doe</name>
    <gender>male</gender>
    <birthday>1990-01-01</birthday>
    <todoList>
        <item>Buy milk</item>
        <item>Pay bills</item>
        <item>Call mom</item>
    </todoList>
    <currentCar>
        <brand>Toyota</brand>
        <model>Corolla</model>
        <year>2021</year>
        <price>20000.00</price>
    </currentCar>
    <previousCars>
        <car>
            <brand>Toyota</brand>
            <model>Corolla</model>
            <year>2020</year>
            <price>18000.00</price>
        </car>
        <car>
            <brand>Toyota</brand>
            <model>Corolla</model>
            <year>2019</year>
            <price>16000.00</price>
        </car>
    </previousCars>
</user>
```

## Objects
```php
<?php

enum GenderEnum: string
{
    case MA = 'male';
    case FE = 'female';
    case OT = 'other';
}

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

class User
{
    /**
     * @param string[] $todoList
     * @param Car[] $previousCars
     */
    public function __construct(
        private string $name,
        private GenderEnum $gender,
        private DateTimeInterface $birthday,
        private array $todoList,
        private Car $currentCar,
        private array $previousCars,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGender: GenderEnum
    {
        return $this->gender;
    }

    public function getBirthday(): DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @return string[]
     */
    public function getTodoList(): array
    {
        return $this->todoList;
    }

    public function getCurrentCar(): Car
    {
        return $this->currentCar;
    }

    /**
    * @return Car[]
     */
    public function getPreviousCars(): array
    {
        return $this->previousCars;
    }
}
```

## Usage
```php
<?php

use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;

$dataConfig = new DataConfig(
    approachEnum: ApproachEnum::CONSTRUCTOR,
    classMap: [
        DateTimeInterface::class => DateTime
    ]
);
$dataMapper = new DataMapper();

$user = $dataMapper->json($json, User::class, $dataConfig);
$user = $dataMapper->xml($xml, User::class, $dataConfig);
```