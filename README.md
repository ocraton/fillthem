# Fillthem

[![Laravel][ico-laravel]][link-laravel]
[![Software License][ico-license]](LICENSE)

ocraton/fillthem is a Laravel package that provide an artisan command to define a model name with fillable fields. Based on that model name, it will create a migration and seeder with those fields.


- [Installation](#installation)
- [Usage](#usage)


## Installation

Run the following command to install the latest applicable version of the package:

```bash
composer require --dev ocraton/fillthem
```


### Laravel

In your app config, add the Service Provider to the `$providers`:

 ```php
'providers' => [
    ...
    Ocraton\Fillthem\Providers\FillthemServiceProvider::class,
],
```

## Usage

Run the following command to create a Car model, migration and seeder with Laravel migration types column:

```bash
php artisan make:fillthem Car --fillable=integer:n_wheels,string:model
```

That's it!



[ico-laravel]: https://img.shields.io/static/v1?label=laravel&message=%E2%89%A56.0&color=ff2d20&logo=laravel&style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-laravel]: https://laravel.com