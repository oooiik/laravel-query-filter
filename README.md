# Laravel Query Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oooiik/laravel-query-filter.svg?style=flat-square)](https://packagist.org/packages/oooiik/laravel-query-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/oooiik/laravel-query-filter.svg?style=flat-square)](https://packagist.org/packages/oooiik/laravel-query-filter)
[![PHP Version](https://img.shields.io/packagist/php-v/oooiik/laravel-query-filter.svg?style=flat-square)](https://packagist.org/packages/oooiik/laravel-query-filter)
[![License](https://img.shields.io/packagist/l/oooiik/laravel-query-filter.svg?style=flat-square)](LICENSE)

A clean, convention-based way to extract Eloquent query filters into dedicated filter classes. Keep your controllers thin, your scopes focused, and your filtering logic testable.

```php
// Before — filtering logic leaking into the controller
$users = User::query()
    ->when($request->username, fn($q, $v) => $q->where('username', $v))
    ->when($request->role, fn($q, $v) => $q->whereHas('role', fn($r) => $r->where('title', $v)))
    ->when($request->created_after, fn($q, $v) => $q->where('created_at', '>=', $v))
    ->paginate();

// After — one line, all filtering in UserFilter
$users = User::filter($request->validated())->paginate();
```

## Features

- 🎯 **Convention over configuration** — each public method on your filter class becomes a filter key. No registration, no metadata.
- 🪶 **Single trait + base class** — add `Filterable` to a model, point it at a filter class, done.
- 🛠 **Artisan generator** — `php artisan make:filter UserFilter` scaffolds the class for you.
- 🔁 **Composable** — apply multiple parameter sets to the same filter instance and chain into the query.
- ⚙️ **Defaults & fallbacks** — provide default parameter values and fallback handlers for missing keys.
- 🧩 **Laravel 6 → 12** — broad compatibility, PHP 7.3+ through 8.x.

## Installation

```bash
composer require oooiik/laravel-query-filter
```

The service provider is auto-registered via Laravel's package discovery.

## Quick Start

### 1. Generate a filter

```bash
php artisan make:filter UserFilter
```

This creates `app/Filters/UserFilter.php`.

### 2. Define your filter methods

Each public method becomes a filter key matching its name:

```php
namespace App\Filters;

use Oooiik\LaravelQueryFilter\Filters\QueryFilter;

class UserFilter extends QueryFilter
{
    public function username($username)
    {
        $this->builder->where('username', $username);
    }

    public function role($role)
    {
        $this->builder->whereHas('role', function ($query) use ($role) {
            $query->where('title', $role);
        });
    }

    public function createdAfter($date)
    {
        $this->builder->where('created_at', '>=', $date);
    }
}
```

### 3. Attach the filter to your model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Oooiik\LaravelQueryFilter\Traits\Model\Filterable;
use App\Filters\UserFilter;

class User extends Model
{
    use Filterable;

    protected $defaultFilter = UserFilter::class;
}
```

### 4. Use it

```php
// Controller
public function index(Request $request)
{
    $validated = $request->validate([
        'username'      => 'nullable|string',
        'role'          => 'nullable|string',
        'createdAfter'  => 'nullable|date',
    ]);

    return User::filter($validated)->paginate();
}
```

Missing keys are silently ignored — only the filter methods that match input parameters run.

## Advanced Usage

### Default parameters

Use the `$default` property to pre-fill values when a key is missing from the input:

```php
class UserFilter extends QueryFilter
{
    public $default = [
        'status' => 'active',
        'sort'   => 'created_at',
    ];

    public function status($status)
    {
        $this->builder->where('status', $status);
    }

    public function sort($column)
    {
        $this->builder->orderBy($column, 'desc');
    }
}
```

Calling `User::filter([])` will still apply `status = active` and `sort by created_at desc`.

### Fallback methods

Use `$fallback` to redirect missing input keys to a different method:

```php
class UserFilter extends QueryFilter
{
    public $fallback = [
        'search' => 'searchByName',
    ];

    public function searchByName($value)
    {
        $this->builder->where('name', 'like', "%{$value}%");
    }
}
```

If the `search` key is missing from input, `searchByName` runs with whatever value was provided as the fallback source.

### Standalone filter instance (chaining)

Apply multiple parameter sets to the same filter:

```php
$filter = User::createFilter(UserFilter::class);

$filter->apply(['role' => 'admin']);
$filter->apply(['status' => 'active']);

$query = $filter->query();
// Both filter sets are now applied to the builder
```

### Accessing all parameters

Filter methods receive the full parameter array as a second argument:

```php
public function username($username, $allParams)
{
    if (! empty($allParams['exact_match'])) {
        $this->builder->where('username', $username);
    } else {
        $this->builder->where('username', 'like', "%{$username}%");
    }
}
```

## Comparison with `spatie/laravel-query-builder`

| | `laravel-query-filter` | `spatie/laravel-query-builder` |
|---|---|---|
| **Approach** | Convention-based — method = filter key | Declarative — register allowed filters explicitly |
| **Per-model class** | Yes, dedicated filter class | Optional, often inline |
| **Custom filter logic** | Plain PHP method | `AllowedFilter::callback()` |
| **Best for** | Complex filtering with reusable logic | API endpoints with simple filtering needs |

Both are great — choose `laravel-query-filter` when you want a dedicated class per model with reusable, testable filter logic.

## Requirements

- PHP 7.3 or higher
- Laravel 6.x — 12.x

## Compatibility Matrix

| Laravel | PHP | Status |
|---|---|---|
| 12.x | 8.2+ | ✅ Supported |
| 11.x | 8.2+ | ✅ Supported |
| 10.x | 8.1+ | ✅ Supported |
| 9.x  | 8.0+ | ✅ Supported |
| 8.x  | 7.3+ | ✅ Supported |
| 7.x  | 7.3+ | ✅ Supported |
| 6.x  | 7.3+ | ✅ Supported |

## Contributing

Pull requests are welcome. For substantial changes, please open an issue first to discuss the direction.

Bug reports and feature ideas → [GitHub Issues](https://github.com/oooiik/laravel-query-filter/issues).

## Credits

- [Obidjon Toshev](https://oooiik.com) — author & maintainer
- All [contributors](https://github.com/oooiik/laravel-query-filter/graphs/contributors)

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.