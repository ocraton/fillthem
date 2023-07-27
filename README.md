![Laravel Fillthem]

# fillthem

[![Laravel][ico-laravel]][link-laravel]
[![Software License][ico-license]](LICENSE.md)

ocraton/fillthem is a package for Laravel that provide an aratisan commad to define a model name with fillable fields. Based on that model name it will create a migration and seeder with those fields.


- [Installation](#installation)



## Installation

Run the following command to install the latest applicable version of the package:

```bash
composer require ocraton/laravel-fillthem
```


### Laravel

In your app config, add the Service Provider to the `$providers`:

 ```php
'providers' => [
    ...
    Ocraton\Fillthem\Providers\FillthemServiceProvider::class,
],
```


That's it!



[ico-laravel]: https://img.shields.io/static/v1?label=laravel&message=%E2%89%A56.0&color=ff2d20&logo=laravel&style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-laravel]: https://laravel.com