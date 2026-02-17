# Laravel Setting (WebAfra)

A Laravel 12 compatible package to store custom settings in the database and cache, with auto-discovery and publishable migrations & models.

## Installation

Install via Composer:

```
composer require webafra/larasetting
```

> **Note:** Since this package supports **Laravel auto-discovery**, you **do not need to manually add the ServiceProvider or Facade**.
> If you want to override manually, the ServiceProvider and Facade are:

```php
// config/app.php
'providers' => [
    ...
    Webafra\LaraSetting\LaraSettingServiceProvider::class,
],

'aliases' => [
    ...
    'Setting' => Webafra\LaraSetting\Facade\Setting::class,
],
```

## Publishing Migrations & Models

To publish migrations and models to your project:

```
# Publish only migrations
php artisan vendor:publish --tag=migrations

# Publish only models
php artisan vendor:publish --tag=models

# Publish both migrations and models
php artisan vendor:publish --tag=all
```

> Migration files will automatically have timestamps added and duplicates will be skipped if they already exist.

## Usage

```php
use Webafra\LaraSetting\Facade\Setting;

// Set a single setting
Setting::set('site_name', 'My Website');

// Set a setting and mark as primary
Setting::set('site_name', 'My Website', true);

// Get a setting, with a default value if not set
$value = Setting::get('site_name', 'Default Site Name');

// Get all primary settings as key-value array
$primary = Setting::getPrimary();

// Store multiple settings from an array
Setting::store([
    'key1' => 'value1',
    'key2' => 'value2'
]);

// Store multiple primary settings (arrays are automatically serialized)
Setting::storePrimary([
    'main_key' => 'main_value',
    'options' => ['opt1', 'opt2']
]);

// Clear all settings cache
Setting::clean();
```
