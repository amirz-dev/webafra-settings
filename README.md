# Laravel Setting (WebAfra)

A Laravel 12/13 compatible package to store custom settings in the database and cache, with auto-discovery, publishable migrations & models, and group-based organization.

## Requirements

| Package version | Laravel | PHP  |
|----------------|---------|------|
| `^2.0`         | 12, 13  | 8.2+ |
| `^1.0`         | 12      | 8.1+ |

## Installation

```bash
composer require webafra/larasetting
```

> **Note:** Since this package supports **Laravel auto-discovery**, you do not need to manually register the ServiceProvider or Facade.
> If you prefer to register manually, add the following to `config/app.php`:

```php
'providers' => [
    Webafra\LaraSetting\LaraSettingServiceProvider::class,
],

'aliases' => [
    'Setting' => Webafra\LaraSetting\Facade\Setting::class,
],
```

## Publishing Migrations & Models

```bash
# Publish only migrations
php artisan vendor:publish --tag=migrations

# Publish only models
php artisan vendor:publish --tag=models

# Publish both
php artisan vendor:publish --tag=all
```

Then run the migration:

```bash
php artisan migrate
```

## Usage

### Basic get / set

```php
use Webafra\LaraSetting\Facade\Setting;

// Store a value
Setting::set('site_name', 'My Website');

// Retrieve a value (with optional default)
$name = Setting::get('site_name', 'Default Name');

// Check if a setting exists
if (Setting::has('site_name')) {
    // ...
}

// Delete a setting
Setting::delete('site_name');
```

### Groups

Settings can be organized into named groups (default group is `general`):

```php
// Store in a specific group
Setting::set('color', '#ffffff', false, 'theme');
Setting::set('font', 'Inter', false, 'theme');

// Retrieve from a group
$color = Setting::get('color', '#000000', 'theme');

// Get all settings in a group as key-value array
$theme = Setting::getGroup('theme');
// ['color' => '#ffffff', 'font' => 'Inter']
```

### Primary settings

Primary settings are a special subset (e.g. site-wide configuration) that can be loaded all at once:

```php
// Mark as primary when storing
Setting::set('app_name', 'My App', true);

// Store multiple primaries (arrays are auto JSON-encoded)
Setting::storePrimary([
    'app_name' => 'My App',
    'social_links' => ['twitter' => 'https://...'],
]);

// Retrieve all primary settings as key-value array
$primaries = Setting::getPrimary();
```

### Bulk store

```php
// Store multiple settings at once (optionally in a group)
Setting::store([
    'key1' => 'value1',
    'key2' => 'value2',
], 'my_group');
```

### Cache management

All values are cached indefinitely via `Cache::forever`. To invalidate all cached settings:

```php
Setting::clean();
```

## API Reference

### `Setting::set()`

```php
Setting::set(string $key, mixed $value, bool $is_primary = false, string $group = 'general'): mixed
```

Stores or updates a setting. Arrays are automatically JSON-encoded. Returns the stored value.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$key` | `string` | — | Setting key |
| `$value` | `mixed` | — | Value to store (arrays are JSON-encoded automatically) |
| `$is_primary` | `bool` | `false` | Mark as a primary setting |
| `$group` | `string` | `'general'` | Group namespace |

---

### `Setting::get()`

```php
Setting::get(string $key, mixed $default = null, string $group = 'general'): mixed
```

Retrieves a setting value. Result is cached indefinitely.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$key` | `string` | — | Setting key |
| `$default` | `mixed` | `null` | Fallback value if key does not exist |
| `$group` | `string` | `'general'` | Group namespace |

---

### `Setting::has()`

```php
Setting::has(string $key, string $group = 'general'): bool
```

Returns `true` if the setting exists in the database (does not hit cache).

---

### `Setting::delete()`

```php
Setting::delete(string $key, string $group = 'general'): bool
```

Deletes a setting from the database and clears its cache entry. Returns `true` on success.

---

### `Setting::store()`

```php
Setting::store(array $settings, string $group = 'general'): int
```

Stores multiple settings at once. Returns the number of settings saved.

```php
Setting::store([
    'key1' => 'value1',
    'key2' => ['a', 'b'],  // arrays are auto JSON-encoded
], 'my_group');
```

---

### `Setting::storePrimary()`

```php
Setting::storePrimary(array $settings, string $group = 'general'): int
```

Same as `store()` but marks every entry as primary (`is_primary = true`). Returns the number of settings saved.

---

### `Setting::getPrimary()`

```php
Setting::getPrimary(mixed $default = null): mixed
```

Returns all primary settings across all groups as a flat `['key' => 'value']` array. Result is cached indefinitely.

---

### `Setting::getGroup()`

```php
Setting::getGroup(string $group): array
```

Returns all settings within a group as a `['key' => 'value']` array. Result is cached indefinitely.

---

### `Setting::clean()`

```php
Setting::clean(): void
```

Clears the cache for every setting, every group, and the primary collection. Does not delete database records.

---

### Quick reference

| Method | DB read | DB write | Cache read | Cache write | Returns |
|--------|:-------:|:--------:|:----------:|:-----------:|---------|
| `set()` | — | ✓ | — | ✓ | `mixed` |
| `get()` | on miss | — | ✓ | on miss | `mixed` |
| `has()` | ✓ | — | — | — | `bool` |
| `delete()` | — | ✓ | — | clears | `bool` |
| `store()` | — | ✓ | — | ✓ | `int` |
| `storePrimary()` | — | ✓ | — | ✓ | `int` |
| `getPrimary()` | on miss | — | ✓ | on miss | `array` |
| `getGroup()` | on miss | — | ✓ | on miss | `array` |
| `clean()` | — | — | — | clears all | `void` |

---

## Upgrading from v1.x

Version 2.0 introduced a `group` column and a composite unique key on `(group, key)`. After upgrading the package, publish and run a new migration:

```bash
php artisan vendor:publish --tag=migrations
php artisan migrate
```

> Existing rows will receive the default group value `general`, so all existing `Setting::get()` and `Setting::set()` calls remain fully compatible without any code changes.

## License

MIT
