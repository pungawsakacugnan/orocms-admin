# Overview
OroCMS Admin is a Laravel package that provides control panel functionality to your Laravel application.
Inspired from PingPong admin for L5 (https://github.com/pingpong-labs/admin) but with batteries added :)


## Installation
To install through composer, set the repository entry as below:
```
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/rudenyl/orocms-admin.git"
        }
    ]
}
```

Then in your ```composer.json```, add:
```
{
    "require": {
        "rudenyl/orocms-admin": "*"
    },
    "minimum-stability": "dev"
}
```


After the package has been installed, add the following service provider in your ```config/app.php```
```
'providers' => [
    OroCMS\Admin\AdminServiceProvider::class,
]
```

## User Model
In your ```config/auth.php```, update the ```model``` value with:
```
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => OroCMS\Admin\Entities\User::class,
    ],
],

```

If you want to use the existing ```App\User``` model, you can extend it with the Model that comes with the admin package
```
// app\User.php
namespace App;

class User extends OroCMS\Admin\Entities\User ...
```


## Publishing
To publish the package's config, views and assets, run the following command in you terminal:
```
$ php artisan vendor:publish --provider="OroCMS\Admin\AdminServiceProvider"
```

Alternatively, if you want to publish everything and run through migration and data seeding, invoke the command:
```
$ php artisan admin:install
```

## Assets
The admin package comes with existing gulpfile.js, bower and node module config files.
To ensure you will be able to compile the assets needed to run the admin package, you need to install the required node modules and bower components.

Install bower components with:
```
$ bower install
```

For the required node modules, run
```
$ npm install
```

Compile your assets with
```
$ gulp
```


## Running the Application
In your terminal, run
```
$ php artisan serve
```

Point your browser to ```http://localhost:8000/admin```

All set!


### License

This package is open-sourced software licensed under [The BSD 3-Clause License](http://opensource.org/licenses/BSD-3-Clause)
