## Stubborn for PHP

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Create a stub file](#create-a-stub-file)
    - [How to use Stubborn](#how-using-stubborn)
    - [`from`](#from)
    - [`to`](#to)
    - [`name`](#name)
    - [`ext`](#ext)
    - [`replace`](#replace)
    - [`replaces`](#replaces)
    - [`download`](#download)
    - [`generate`](#generate)
- [Contributors](#contributors)
- [Security](#security)
- [Changelog](#changelog)
- [License](#license)

<a name="introduction"></a>
## Introduction

The Stubborn package enhances the PHP development workflow by providing a set of customizable stubs. Stubs are templates used to scaffold code snippets for various components like models, controllers, and migrations. With Stubborn, developers can easily tailor these stubs to match their project's coding standards and conventions. This package aims to streamline the code generation process, fostering consistency and efficiency in PHP projects. Explore the customization options and boost your development speed with Stubborn.

<a name="requirements"></a>
## Requirements

***
- ```PHP >= 8.0```


<a name="installation"></a>
## Installation

You can install the package with Composer, but make sure to add this repository (as type git) in your composer.json. When this package is available on packagist, you can skip this step (and it won't be mentioned here).
Once the repository is added, run:
```bash
composer require tylerdak/stubborn
```

You don't need to publish anything.

<a name="usage"></a>
## Usage

<a name="create-a-stub-file"></a>
### Create a stub file
First of all, create a stub file called `model.stub`:

```bash
touch model.stub
```

Add some code to that, like this:

```php
<?php

namespace {{ NAMESPACE }};

class {{ CLASS }}
{
    
}
```

<a name="how-using-stubborn"></a>
### How to Use Stubborn

In order to use Stubborn, you need to import the `Stubborn\Stub` class:

```php
use Dakin\Stubborn\Stub;

Stub::class;
```

<a name="from"></a>
### `from`

First thing, you need to use the `from` method to give the stub path:

```php
Stub::from(__DIR__ . 'model.stub');
```

<a name="to"></a>
### `to`

Next, you'll want to specify the destination directory of the stub file:

```php
Stub::from(__DIR__ . 'model.stub')
    ->to(__DIR__ . '/App');
```

<a name="name"></a>
### `name`

You can set the stub file with this, but make sure to **leave out the stub extension**:

```php
Stub::from(__DIR__ . 'model.stub')
    ->to(__DIR__ . '/App')
    ->name('new-model');
```

<a name="ext"></a>
### `ext`

You can determine the stub extension:

```php
Stub::from(__DIR__ . 'model.stub')
    ->to(__DIR__ . '/App')
    ->name('new-model')
    ->ext('php');
```

<a name="replace"></a>
### `replace`

The `replace` method takes two parameters, the first one is the key (variable) and the second one is the value. The value will be replaced with the variable:

```php
Stub::from(__DIR__ . 'model.stub')
    ->to(__DIR__ . '/App')
    ->name('new-model')
    ->ext('php')
    ->replace('NAMESPACE', 'App');
```

<a name="replaces"></a>
### `replaces`

The `replaces` method takes an array. If you want to replace multiple variables you can use this method:

```php
Stub::from(__DIR__ . 'model.stub')
    ->to(__DIR__ . '/App')
    ->name('new-model')
    ->ext('php')
    ->replaces([
        'NAMESPACE' => 'App',
        'CLASS' => 'MyClass'
    ]);
```

<a name="generate"></a>
### `generate`

To generate the stub file, you need to use the `generate` method at the end of the chain to generate stub file:

```php
Stub::from(__DIR__ . 'model.stub')
    ->to(__DIR__ . '/App')
    ->name('new-model')
    ->ext('php')
    ->replaces([
        'NAMESPACE' => 'App',
        'CLASS' => 'MyClass'
    ])
    ->generate();
```

If you're following along, the final file should be named `new-model.php` and look something like this:

```php
<?php

namespace App;

class MyClass
{
    
}
```

<a name="modifiers"></a>
### Modifiers

Stubborn adds support for modifiers. Modifiers let you use a single replace string in multiple forms.
Here's an example... Let's say you have a class stub that can introduce itself:
```php
<?php

namespace App;

class {{ NAME }}
{
    public function introduce(): string {
        return "I am a {{ NAME }}";
    }
}
```

This is fine, but maybe I want the class to shout its name in ALL CAPS. Instead of declaring that behavior from my Stub generation code, I can put that intention directly in the stub file.
> Mind you, this particular use case would benefit more from not having the class name statically declared twice, but for demo purposes...

```php
<?php

namespace App;

class {{ NAME::studly }}
{
    public function introduce(): string {
        return "I am a {{ NAME::upper }}!";
    }
}
```
There's a new Str class that's been adopted from the [Laravel framework](https://github.com/laravel/framework) to offer several helpers as modifiers. Most of the single parameter methods from that class can be used as modifiers by adding `::methodName` after the variable name.
These modifiers can also be chained together: `{{ NAME::upper::trim }}`.

In addition, you can add your own modifiers by editing the Stub::modFunctions associative array. You can pass in the modifier name as the key and a function accepting a string as the value:
```php
use Dakin\Stubborn\Stub;

Stub::modFunctions['repeat'] = fn ($theString) => $theString . $theString;
```
Now, stubs with `{{ VAR_NAME::repeat }}` will repeat the replace value once.

<a name="contributors"></a>
## Contributors
The crew at [Binafy](https://github.com/binafy) did most of the heavy lifting on this with their [Laravel-embedded version](https://github.com/binafy/laravel-stub) of this package.

<a name="security"></a>
## Security

If you discover any security-related issues, please email `tyler@dakin.one` instead of using the issue tracker.

<a name="license"></a>
## License

The MIT License (MIT). Please see [License File](https://github.com/tylerdak/stubborn/blob/1.x/LICENSE) for more information.
