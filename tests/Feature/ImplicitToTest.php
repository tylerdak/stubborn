<?php

use Dakin\Stubborn\Stub;

$stubsFolder = __DIR__ . '/pretend_stubs_folder';
$contextFolder = __DIR__ . '/pretend_src_folder';

test('setup', function () use ($stubsFolder) {
    if (! is_dir($stubsFolder)) {
        expect(Stub::setStubFolder($stubsFolder))
            ->toBeFalse("Folder should not be set if folder does not exist.");

        expect(mkdir($stubsFolder))
            ->toBeTrue("Test folder $stubsFolder could not be created");
    }

    if (! is_dir($stubsFolder . '/Support')) {
        expect(mkdir($stubsFolder . '/Support'))
            ->toBeTrue("Test folder Support could not be created");
    }

    expect(file_put_contents($stubsFolder . '/Support/Enums.php'))
        ->toBeTrue('Stub could not be created.');

    if (! is_dir($contextFolder)) {
        expect(mkdir($contextFolder))
            ->toBeTrue("Test folder $contextFolder could not be created");
    }
    if (! is_dir($contextFolder . '/Support')) {
        expect(mkdir($contextFolder . '/Support'))
            ->toBeTrue("Test folder context-Support could not be created");
    }
    if (! is_dir($contextFolder . '/Support/Enums')) {
        expect(mkdir($contextFolder . '/Support/Enums'))
            ->toBeTrue("Test folder context-Support/Enums could not be created");
    }

    Stub::setStubFolder($stubsFolder);
});

test('implicit-to not used without context folder', function () {
    expect(function () {
        Stub::from('Support/Enums.php')
            ->name('Color')
            ->generate();
    })->toThrow(RuntimeException::class);

    expect(function () {
        Stub::from('Support/Enums.php')
            ->name('Color')
            ->ext('lol')
            ->generate();
    })->toThrow(RuntimeException::class);
});

test('to can be omitted when context folder is set', function () use ($contextFolder) {
    // expect(Stub::setSourceFolder());

    Stub::from('Support/Enums.php')
        ->name('Color')
        ->generate();

    Stub::from('Support/Enums.php')
        ->name('Color')
        ->ext('lol')
        ->generate();
});

test('teardown', function () use ($stubsFolder) {
    expect(rmdir($stubsFolder))
        ->toBeTrue("Test folder could not be removed for cleanup.");

    expect(Stub::resetStubFolder())
        ->toBeTrue("Stub folder was not reset. Future tests may fail.");
});
