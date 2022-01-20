# Laravel Data Transfer Object

> Allows you to pass data in the specified typing, namely in `IsakzhanovR\ValueObject` or in a primitive type. The same type can be a DTO

<p align="center">
    <a href="https://packagist.org/packages/isakzhanov-r/laravel-dto"><img src="https://img.shields.io/packagist/dt/isakzhanov-r/laravel-dto.svg?style=flat-square" alt="Total Downloads" /></a>
    <a href="https://packagist.org/packages/isakzhanov-r/laravel-dto"><img src="https://poser.pugx.org/isakzhanov-r/laravel-dto/v/stable?format=flat-square" alt="Latest Stable Version" /></a>
    <a href="https://packagist.org/isakzhanov-r/laravel-dto"><img src="https://poser.pugx.org/isakzhanov-r/laravel-dto/v/unstable?format=flat-square" alt="Latest Unstable Version" /></a>
    <a href="LICENSE"><img src="https://poser.pugx.org/isakzhanov-r/laravel-dto/license?format=flat-square" alt="License" /></a>
</p>

## Contents

* [Installation](#installation)
* [Usage](#usage)
* [License](#license)

## Installation

To get the latest version of Laravel Data Transfer Object package, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require isakzhanov-r/laravel-dto
```

Instead, you can, of course, manually update the dependency block `require` in `composer.json` and run `composer update` if you want to:

```json
{
    "require-dev": {
        "isakzhanov-r/laravel-dto": "^1.0"
    }
}
```

## Usage

To use `DTO`, you need to create a class that will inherit from the abstract DataTransferObject class, and declare the public variables in the class with the
desired type

```php
use IsakzhanovR\DataTransferObject\DataTransferObject;
use IsakzhanovR\ValueObject\Email  // example

class UserData extends DataTransferObject 
{
    public $id;
    
    public string $name;
    
    public Email $email;
    
    /**
    * @var IsakzhanovR\DataTransferObject\FiledDTO[] - example
    */
    public array $fields;
}
```

You can also create a DTO object from an array, from a FormRequest, from an Object and from a JSON string

```php

$dto = new UserData(['id' => 1,'name' => 'Example',' email' => 'example@test.com']);

$array = ['id' => 1,'name' => 'Example',' email' => 'example@test.com'];

$dto = UserData::fromArray($array);

$object = new stdClass();
$object->id = 1;
$object->name = 'Example';
$object->email = 'example@test.com';

$dto = UserData::fromObject($object);

$dto = UserData::fromRequest($request);

$dto = UserData::fromJson(json_encode($array));

```

It also happens that you need to reverse transformation into an array , for this there is a `get` method:

```php

$dto->get()

```

or a method with recursive transformation into an array

```php

$dto->toArray()

```

## License

This package is released under the [MIT License](LICENSE).
