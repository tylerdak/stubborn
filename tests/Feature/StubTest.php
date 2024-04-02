<?php

use Dakin\Stubborn\Stub;

test('generate stub successfully with all options', function () {
    $stub = __DIR__ . '/test.stub';

    $generate = Stub::from($stub)
        ->to(__DIR__ . '/../App')
        ->replaces([
            'CLASS' => 'Dakin',
            'NAMESPACE' => 'App\Models'
        ])
        ->replace('TRAIT', 'HasFactory')
        ->name('new-test')
        ->ext('php')
        ->generate();

    \PHPUnit\Framework\assertTrue($generate);
    \PHPUnit\Framework\assertFileExists(__DIR__ . '/../App/new-test.php');
    \PHPUnit\Framework\assertFileDoesNotExist(__DIR__ . '/../App/test.stub');
});

test('throw exception when stub path is invalid', function () {
    $generate = Stub::from('test.stub')
        ->to(__DIR__ . '/../App')
        ->name('new-test')
        ->ext('php')
        ->generate();

    \PHPUnit\Framework\assertFileDoesNotExist(__DIR__ . '/../App/new-test.php');
    \PHPUnit\Framework\assertFileExists(__DIR__ . '/../App/test.stub');
})->expectExceptionMessage('The stub file does not exist, please enter a valid path.');

test('throw exception when destination path is invalid', function () {
    $generate = Stub::from(__DIR__ . '/test.stub')
        ->to('App')
        ->name('new-test')
        ->ext('php')
        ->generate();

    \PHPUnit\Framework\assertFileDoesNotExist(__DIR__ . '/../App/new-test.php');
    \PHPUnit\Framework\assertFileExists(__DIR__ . '/../App/test.stub');
})->expectExceptionMessage('The given folder path is not valid.');
