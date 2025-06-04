# A laravel wrapper for Onfido php SDK

---
## Support us

## Installation

You can install the package via composer:

```bash
composer require oanna/laravel-onfido
```

Configure the package by calling the install command

```bash
php artisan onfido:install
```

## Usage

This package gives you access to a trait and a model.

First the model `OnfidoInstance::class` is the model related to the `onfido_instances` table.
This model store the onfido's related datas of the model (morph).

Then there is the trait `Verifiable::class` that is destinate for the model that need to be verified, for exemple `User::class`.

```php
class User extends \Illuminate\Database\Eloquent\Model
{
    use \OANNA\Onfido\Traits\Verifiable; // Use it here to be able to verify your model
    
    /*...*/
}
```

This will give you access to a lot of methods provided in the trait.

But the more important is the `startVerification(Region $region, $attributes = [], $workflowId = null)` one

When user start a verification. Call this method on the model instance.

```php

public function startVerification()
{
    $this->user->startVerification(\Onfido\Region::EU, ['first_name' => 'John', 'last_name' => 'Doe']);
}

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
