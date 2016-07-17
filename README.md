Laravel JS Lang Converter
=======================

> Laravel Localization in JavaScript.

![Laravel 5.2](https://img.shields.io/badge/Laravel-5.2-f4645f.svg)
![Laravel 5.1](https://img.shields.io/badge/Laravel-5.1-f4645f.svg)
[![Latest Stable Version](https://poser.pugx.org/michele-angioni/laravel-js-lang-converter/v/stable.svg)](https://packagist.org/packages/michele-angioni/laravel-js-lang-converter)
[![License](https://poser.pugx.org/michele-angioni/laravel-js-lang-converter/license.svg)](https://packagist.org/packages/michele-angioni/laravel-js-lang-converter)
[![Build Status](https://travis-ci.org/micheleangioni/laravel-js-lang-converter.svg?branch=master)](https://travis-ci.org/micheleangioni/laravel-js-lang-converter)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b9c37a8d-26aa-458b-8e7e-7ccac6d1e021/small.png)](https://insight.sensiolabs.com/projects/b9c37a8d-26aa-458b-8e7e-7ccac6d1e021)

Laravel JS Lang Converter converts all your localization messages of your Laravel app to JavaScript, providing a small JavaScript library to interact with those messages in the fron end.

Most of the work has been developed in [Mariuzzo's package ](https://github.com/rmariuzzo/laravel-js-localization)

Support Laravel Laravel 5.1, 5.2 and 5.3.

Installation
------------

Add the following line to you `composer.json` file under `require`.

```json
"michele-angioni/laravel-js-lang-converter": "~2.0"
```

and run `composer update` or `composer install`.

Then you need to add the Laravel JS Lang Converter service provider in your `app/config/app.php` file 

```php
'providers' => [
    // ...
    'MicheleAngioni\LaravelJsLangConverter\LaravelJsLangConverterServiceProvider',
    // ...
],
```

In order to use some package features, you need to publish the config file through the artisan command `php artisan vendor:publish`. It will create the `laravel_js_lang.php` file in your config directory.

Now you are done!

Usage
-----

This project comes with a command that generate the JavaScript version of all your messages found in `resources/lang` directory. The resulting JavaScript file will have the whole bunch of messages and a thin library similar to Laravel's `Lang` class.

**Generating JS messages**

```shell
php artisan lang:js
```

**Specifying a custom target**

```shell
php artisan lang:js public/assets/dist/lang.dist.js
```

**Converting only some files**

If you don't want to convert ALL your lang files, you can specify the files you want to be converted into your `laravel_js_lang.php` conf file. Under the `files` array, just add the list of your source files, like so:

```php
'files' => [
    'pagination',
    'validation'
]
```

**Compressing the JS file**

```shell
php artisan lang:js -c
```

**Use [gulp](http://gulpjs.com/) to publish (optional):**

1. Install `gulp-shell` from https://github.com/sun-zheng-an/gulp-shell with `npm install --save-dev gulp-shell` .

2. Create an extension for elixir in your `gulpfile.js`:

    ```js
    var shell = require('gulp-shell');
    
    //......
    
    var Task = elixir.Task;
    
    elixir.extend('langjs', function(path, minimize) {
        new Task('langjs', function() {
            var command = "php artisan lang:js " + (path || "public/js/messages.js");
                if (minimize) {
                    command += " -c";
                }
            return gulp.src("").pipe(shell(command));
        });
    });
    
    gulp.task('langJs', shell.task('php artisan lang:js -c public/js/messages.js'));
    ```

3.  Use the new elixir task:

```js
elixir(function(mix) {
    var path = "public/js";
    var minimize = true;
    mix.langjs(path, minimize);
});
```

Documentation
-------------

This is the documentation regarding the thin JavaScript library. The library is highly inspired on Laravel's `Lang` class.

**Getting a message**

```js
Lang.get('messages.home');
```

**Getting a message with replacements**

```js
Lang.get('messages.welcome', { name: 'Joe' });
```

**Changing the locale**

```js
Lang.setLocale('es');
```

**Checking if a message key exists**

```js
Lang.has('messages.foo');
```

**Support for singular and plural message based on a count**

```js
Lang.choice('messages.apples', 10);
```

**Calling the `choice` method with replacements**

```js
Lang.choice('messages.apples', 10, { name: 'Joe' });
```

For more detailed information, take a look at the source: [Lang.js](https://github.com/michele-angioni/laravel-js-lang-converter/blob/master/js/lang.js).

How to contribute
===================

Pull requests are welcome. 

 1. Fork this repository and clone it.
 2. Create a branch from develop: `git checkout -b feature-foo`.
 3. Push your commits and create a pull request.

Setting up development environment
----------------------------------

**Prerequisites:**

You need to have installed the following softwares.

 - Composer
 - NodeJS
 - NPM
 - PHP 5.5.9+

After getting all the required software you may run the following commands to get everything ready:

1. Install PHP dependencies:

```shell
composer install
```

2. Install NPM dependences:

```shell
npm install -g jasmine-node

npm install
```

Now you are good to go! Happy coding!

Unit testing
------------

This project use Jasmine-Node and PHPUnit. All tests are stored at `tests` directory.

To run all JS tests type in you terminal:

```shell
npm test
```

To run all PHP tests type in your terminal:

```shell
phpunit
```
