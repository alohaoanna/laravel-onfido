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

But the more important is the `startVerification(Region|null $region = null, $attributes = [], $workflowId = null)` one

When user start a verification. Call this method on the model instance.
This method call a repository method `OnfidoRepository::startVerification()`.

Alternatively, you can do it by the repository like that
PS: It is recommended to pass by the trait method
```php
use OANNA\Onfido\Repositories\OnfidoRepository;
use Onfido\Region;

$repo = new OnfidoRepository($user);
$repo->startVerification(Region::EU, $user->getOnfidoAttributes());
```

The method `startVerification()` will manage onfido datas for the model instance. When its done it will return you an array of data (applicant_id, workflow_id, workflow_run_id, sdk_token).

This package have a default model to manage onfido datas. But you can disabled it by not migrate the table and override some methods of the Verifiable trait.
First you will need to override the relation.

```php
public function onfidoInstance()
{
    return $this->morphOne(OnfidoInstance::class, 'model');
}
```

Then you will need to specified to the package that you use a different model.
For this add this line to your `AppServiceProvider.php` :

```php
use OANNA\Onfido\OnfidoManager;
use App\Models\MyModel;

public function boot(): void
{
    /*...*/
    OnfidoManager::registerOnfidoModel(MyModel::class);
    /*...*/
}
```

You may need to override the `createOnfidoInstance()` method in the trait too if needed.

Be carreful if you override the model. Some methods in the trait depends on table's columns (started, verified, verified_at...) and if you don't have these in your custom one, you may have error.

## IMPORTANT

This package doesn't provide some views or stuff to manage onfido sdk (Web or others). It let you manage your way to init Onfido SDK and transfer datas across all classes.

## API

This package provides a `Portal::class` that is use to make API call. You can call the `execute()` method to make api call. This is a try catch method with a closure params.

This is a part of the code in the `createWorkflowRun()` method 
```php
$this->execute(function () use ($applicant, $workflow) {
    $workflowRunBuilder = new WorkflowRunBuilder([
        'applicant_id' => $applicant,
        'workflow_id' => $workflow,
    ]);

    $workflowRun = $this->api->createWorkflowRun($workflowRunBuilder);

    if ($workflowRun instanceof Error) {
        throw new ApiException($workflowRun->getError());
    }

    return $workflowRun;
});
```

To instantiate the `Portal::class`, you will need to set your API Token and a Region :
By default when The `Portal::class` is construct, it calls the `setApiToken()` and provide the one in the config file

You can also provide an already created onfido `Configuration::class` in the only param of the initialize static method if you have one.
```php
use OANNA\Onfido\Api\Portal;
use Onfido\Region;

$portal = Portal::initialize()
    ->setApiToken('<YOUR_API_TOKEN>') // Optional
    ->setRegion(Region::EU);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
