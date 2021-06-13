# App & Models Settings for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smartisan/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/smartisan/laravel-settings)
[![GitHub Tests Action Status](https://github.com/iamohd/laravel-settings/workflows/run-tests/badge.svg)](https://github.com/iamohd/laravel-settings/actions?query=workflow%3Arun-tests)
[![Total Downloads](https://img.shields.io/packagist/dt/smartisan/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/smartisan/laravel-settings)

This package allows you to store application wide and model specific Laravel settings. Settings values
are type-cast and stored properly. You can also define your own casts and store repository.

## Installation

Install the package via composer:

```bash
composer require smartisan/laravel-settings
```

Publish the config file with:

```bash
php artisan vendor:publish --provider="Smartisan\Settings\SettingsServiceProvider" --tag="config"
```

Publish the migration file with:

```bash
php artisan vendor:publish --provider="Smartisan\Settings\SettingsServiceProvider" --tag="migrations"
```

And finally run the migration with:

```bash
php artisan migrate
```

## Usage

The package provides various APIs to access the settings manager. You can access it with Settings facade, settings()
helper method and via HasSettings trait on your models.

### Store Settings

You can set single entry, or multiple entries of settings. The values of objects will be cast according to the rules
defined in settings.php config file.

* One Entry

```php
Settings::set('key', 'value');
```

* Multiple Entries

You can set multiple settings entries by passing an associative array of keys and values. Casts will be applied on all
the payload, even on nested arrays.

```php
Settings::set([
    'k1' => Carbon::now(),
    'k2' => [
        'k3' => $user,
        'k4' => [
            'k5' => $model
        ],  
    ]
]);
```

* Specify Settings Group

It's possible to categorize your settings into groups by calling group method.

```php
Settings::group('name')->set('key', 'value');

Settings::group('name')->set([
    'k1' => 'v1',
    'k2' => 'v2',
]);
```

* Model Specific Settings

It's also possible to set settings for a specific model by calling for method

```php
Settings::for($model)->set('key', 'value');

Settings::for($model)->set([
    'k1' => 'v1',
    'k2' => 'v2',
]);
```

* Mixing Filters

You can mix all filters together like this:

```php
Settings::for($model)->group('name')->set('key', 'value');
```

### Retrieve Settings

You can retrieve one or multiple entries and specify the default value if not exist.

* One Entry

```php
Settings::get('k1', 'default');
```

* Multiple Entries

If the entry key does not exist, the default value will be placed instead

```php
Settings::get(['k1', 'k2', 'k3'], 'default');
```

* All Entries

If you want to retrieve all entries, you simply call all method. You can also specify the model or group. Also to
excempt some specific keys.

**Note:** Remember that retrieving all entries without specifying the group or model, will retrieve all entries that has
no group or model set. You can consider these as (global app settings).

```php
Settings::all();

Settings::for($model)->all();

Settings::for($model)->group('name')->all();

Settings::except('k1', 'k2')->for($model)->group('name')->all();

Settings::except('k1')->for($model)->group('name')->all();
```

### Forget Entry

You can remove entries by calling forget method.

```php
Settings::forget('key');

Settings::for($model)->group('name')->forget('key');
```

### Determine Existance

You can determine whether the given settings entry key exists or not

```php
Settings::exist('key');

Settings::for($model)->exist('key');

Settings::for($model)->group('name')->exist('key');
```

### Helper Method

The package also ships with a settings helper method, you can use it instead of using Settings Facade

```php
settings()->set('key', 'value');
...
```

### HasSettings Trait

You can use HasSettings trait on your Eloquent models as well

1. First prepare your model

```php
use Smartisan\Settings\HasSettings;

class User extends Model
{
    use HasSettings;    
}
```

2. Now you can call settings() method on that model. This is equivelant to ```Settings::for($model)```

```php
$user->settings()->set('k1', 'v1');
...
```

## Custom Repositories

If you don't want to use the database repository, you can easily create your own settings repository. To do that

1. Create a class of your repository that implements Repository interface and implement the following methods

```php
use Smartisan\Settings\Contracts\Repository;

class FileRepository implements Repository 
{
    public function get($key,$default = null) {
        //
    }
    
    public function set($key,$value = null) {
        //
    }
    
    public function forget($key) {
        //
    }
    
    public function all() {
        //
    }
}
```

2. In settings configuration file, add your own repository config to repositories attribute

```php
    'repositories' => [
        ...
        
        'file' => [
            ...
        ]
    ],
```

3. Change the default repository in settings config file to your own repository implementation

## Custom Casts

The package allows you to easily create your own casting rules of any object type.

1. Create your own cast class that implements Castable interface.

**Note:** The set method is called when the value of the entries is being stored to the repository, and the get method is called
when the value is being retrieved from the repository.

```php
use Smartisan\Settings\Contracts\Castable;

class CustomCast implements Castable
{
    public function set($payload) {
        //
    }
    
    public function get($payload) {
        //
    }
}
```

2. Add the casts to the array of casts in settings config file

```php
'casts' => [
    ...
    CustomDataType::class => CustomCast::class
]
```

**Note:** If you want to pass a parameter to your cast, you can set an object of the cast instead of cast class name

```php
'casts' => [
    ...
    CustomDataType::class => new CustomCast('param')
]
```

## Settings Cache

You can easily enable or disable caching settings in settings.php config file. You can also specify which caching store to use

```php
'cache' => [
    'enabled' => env('SETTINGS_CACHE_ENABLED', false),
    'store' => null,
    'prefix' => null,
],
```

## Testing

To run the package's tests, simply call the following command

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Mohammed Isa](https://github.com/iamohd)
- [All Contributors](../../contributors)

## Alternatives

* [spatie/laravel-settings](https://github.com/spatie/laravel-settings)
* [akaunting/laravel-setting](https://github.com/akaunting/laravel-setting)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
