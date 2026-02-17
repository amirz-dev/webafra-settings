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
> Available default groups: Setting::GROUP_SITE, Setting::GROUP_EMAIL, Setting::GROUP_PAYMENT, Setting::GROUP_CUSTOM

## Usage

```php
use Webafra\LaraSetting\Facade\Setting;

// Set a setting in a specific group
Setting::set('site_name', 'My Website', false, Setting::GROUP_SITE);

// Store multiple settings in a group
Setting::store([
    'key1' => 'value1',
    'key2' => 'value2'
], Setting::GROUP_CUSTOM);

// Get all settings by group
$siteSettings = Setting::getByGroup(Setting::GROUP_SITE);

// Store multiple primary settings (arrays are automatically serialized)
Setting::storePrimary([
    'main_key' => 'main_value',
    'options' => ['opt1', 'opt2']
]);

// Clear all settings cache
Setting::clean();
```
