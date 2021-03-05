# Laravel test parallel

<a href="https://github.styleci.io/repos/342560240"><img src="https://github.styleci.io/repos/342560240/shield?branch=master" alt="StyleCI Shield"></a>
<a href="https://packagist.org/packages/devinweb/laravel-test-parallel"><img src="https://img.shields.io/packagist/dt/devinweb/laravel-test-parallel.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/devinweb/laravel-test-parallel"><img src="https://img.shields.io/packagist/v/devinweb/laravel-test-parallel.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/devinweb/laravel-test-parallel"><img src="https://img.shields.io/packagist/l/devinweb/laravel-test-parallel.svg?style=flat-square" alt="License"></a>

As you may know laravel parallel testing is already available on [laravel v8.x](https://laravel.com/docs/8.x/testing#running-tests-in-parallel), but this package for the old version 5.x, 6.x and 7.x, to enjoy the parallel testing.

Is based on [brianium/paratest](https://github.com/paratestphp/paratest) and implements the same logic that handle the testing command used in laravel developed by [Nuno Maduro](https://github.com/nunomaduro).

## Requirement

This package requires.

```json
"phpunit/phpunit": "^9.5.1"
```

to update your phpunit package you can add this dev dependency on your `composer.json`

```json
{
    ...

    "require-dev": {
       ...,
       "phpunit/phpunit": "^9.5.1"
   },

   ...
}
```

Then remove your `composer.lock` file and tell the composer to install all the dependencies using `composer install`.

## Installation

You can install the package via composer:

```shell
composer require devinweb/laravel-test-parallel
```

This package will register itself automatically if your Laravel 5.5+, trough Package auto-discovery.

## Usage

To enjoy with the testing parallel run this command

```shell

php artisan test:parallel

```

## Phpunit.xml

If you have any error related to override your envirement during the test, you can force them by adding in each env variable on your phpunit.xml file
[`force=true`](https://github.com/sebastianbergmann/phpunit/issues/2353).
