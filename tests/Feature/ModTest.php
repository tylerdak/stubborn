<?php

use Dakin\Stubborn\Stub;

test('regex finds (and replaces) regular, modded, and multi-modded variables', function () {
    $stub = $this->writeTest('regex',<<<EOL
{{ VARIABLE }}{{ VARIABLE::lower }}{{ VARIABLE::lower::lower }}
EOL);

    $success = Stub::from($stub)
        ->to(__DIR__ . '/../Generated')
        ->replace('VARIABLE','test')
        ->name('result_regex')
        ->ext('php')
        ->generate();

    expect($success)->toBeTrue();
    expect($this->readResult('regex.php'))
        ->toBe('testtesttest');
});

test('camel, kebab, snake, and studly from normal spaces', function () {
    $stub = $this->writeTest('cases', "{{ VAR::camel }} {{ VAR::kebab }} {{ VAR::snake }} {{ VAR::studly }}");

    $success = Stub::from($stub)
        ->to(__DIR__ . '/../Generated')
        ->replace('VAR','My Name')
        ->name('result_cases')
        ->ext('php')
        ->generate();

    expect($success)->toBeTrue();
    expect($this->readResult('cases.php'))->toBe('myName my-name my_name MyName');
});

test('mods can be layered', function () {
    $stub = $this->writeTest('layers', "{{ VAR::camel }} {{ VAR::camel::kebab }} {{ VAR::camel::kebab::snake }} {{ VAR::camel::kebab::snake::studly }} {{ VAR::camel::kebab::snake::studly::upper }}");

    $success = Stub::from($stub)
        ->to(__DIR__ . '/../Generated')
        ->replace('VAR','My Name')
        ->name('result_layers')
        ->ext('php')
        ->generate();

    expect($success)->toBeTrue();
    expect($this->readResult('layers.php'))->toBe('myName my-name my_name MyName MYNAME');
});
