# Laravel Query Filter

In Laravel, it is convenient to write the query filter related to the model in a separate class!

### Installation

```
composer require oooiik/laravel-query-filter
```

### Usage:

for single use
```php
User::filter($validated)->get();
```
or create a filter
```php
$userFilter = User::createFilter(UserFilter::class);
```

get a query using a filter
```php
$userFilter->apply($validated)->query();
```
write filter on filter and get a query
```php
$userFilter->apply($validated);
$userFilter->apply($validated_2)->query();
```
filter cleaning and reuse
```php
$userFilter->resetApply($validated_3)->query();
```

In order to use a filter you have to create a new one by the command that is provided by the package:

```
php artisan make:filter UserFilter
```
This command will create a directory `Filters` and `UserFilter` class inside. To use the filter method of `User` model use the `Filterable` trait:

```php 
<?php

namespace App\Models;

use Oooiik\LaravelQueryFilter\Traits\Model\Filterable;

class User extends Model
{
    use Filterable;

```
And set the `defaultFilter` of a model by adding:

```php
protected $defaultFilter = UserFilter::class;
```
You can create a custom query by creating a new function in the `Filter` class, for example filtering books by publishing date:
```php
public function username($username)
{
    $this->builder->where('username', $username);
}
// $validated = ['username' => 'name']
```
or filter by relationship:
```php
public function role($role)
{
    $this->builder->whereHas('role', function($query) use ($role) {
        $query->where('title', $role);
    })
}
// $validated = ['role' => 'admin']

