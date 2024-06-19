<?php

use Dakin\Stubborn\Stub;

$stubsFolder = __DIR__ . '/pretend_stubs_folder';
$contextFolder = __DIR__ . '/pretend_src_folder';
$sampleStubContent = "mmm... skyscraper i love you";

test('setup', function () use ($stubsFolder, $contextFolder, $sampleStubContent) {

    if (! is_dir($stubsFolder)) {
        expect(Stub::setStubFolder($stubsFolder))
            ->toBeFalse("Folder should not be set if folder does not exist.");

        // set up root stubs folder
        expect(mkdir($stubsFolder))
            ->toBeTrue("Test folder $stubsFolder could not be created");
    }

    // set up $stubsFolder/Support to pull stubs out of
    if (! is_dir($stubsFolder . '/Support')) {
        expect(mkdir($stubsFolder . '/Support'))
            ->toBeTrue("Test folder Support could not be created");
    }

    // add file to $stubs../Support/
    expect(file_put_contents($stubsFolder . '/Support/Enums.php',$sampleStubContent))->not->toBe(0);

    // set up context folder
    if (! is_dir($contextFolder)) {
        expect(mkdir($contextFolder))
            ->toBeTrue("Test folder $contextFolder could not be created");
    }
    // set up $stubsFolder/Support to put stubs in if they come from $stubsFolder/Support
    if (! is_dir($contextFolder . '/Support')) {
        expect(mkdir($contextFolder . '/Support'))
            ->toBeTrue("Test folder context-Support could not be created");
    }

    // set up $context.../Support/Enums for the $stubs.../Support/Enums stub to target
    if (! is_dir($contextFolder . '/Support/Enums')) {
        expect(mkdir($contextFolder . '/Support/Enums'))
            ->toBeTrue("Test folder context-Support/Enums could not be created");
    }

    // set stub folder now that the folder is ready
    Stub::setStubFolder($stubsFolder);


    expect(file_put_contents($stubsFolder . '/anything.stub',$sampleStubContent))->not->toBe(0);
    expect(file_put_contents($stubsFolder . '/something',$sampleStubContent))->not->toBe(0);
    expect(file_put_contents($stubsFolder . '/Support.stub',$sampleStubContent))->not->toBe(0);
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

test('to can be omitted when context folder is set', function () use ($contextFolder, $sampleStubContent) {
    expect(Stub::setContextFolder($contextFolder));

    expect(Stub::from('Support/Enums.php')
            ->name('Color')
            ->generate()
    )->toBeTrue();

    expect(file_get_contents($contextFolder . '/Support/Enums/Color.php'))
        ->toBe($sampleStubContent);

    expect(Stub::from('Support/Enums.php')
        ->name('Color')
        ->ext('lol')
        ->generate()
    )->toBeTrue();

    expect(file_get_contents($contextFolder . '/Support/Enums/Color.lol'))
        ->toBe($sampleStubContent);
});

test('to does not infer directories that are not made', function () use ($contextFolder, $sampleStubContent) {
    // because $contextFolder/anything is not a directory, the to and extension will not be set
    expect(Stub::from('anything.stub')
        ->name('goodfile')
        ->ext('real')
        ->generate()
    )->toBeTrue();

    expect(file_get_contents($contextFolder . '/goodfile.real'))
        ->toBe($sampleStubContent);

    expect(Stub::from('Support.stub')
        ->name('Organization')
        ->generate()
    )->toBeTrue();

    expect(file_get_contents($contextFolder . '/Support/Organization.stub'))
        ->toBe($sampleStubContent);
});

test('extension is implied even if to directory isn\'t', function () use ($contextFolder, $sampleStubContent) {
    expect(Stub::from('anything.stub')
        ->name('goodfile')
        ->generate()
    )->toBeTrue();

    expect(file_get_contents($contextFolder . '/goodfile.stub'))
        ->toBe($sampleStubContent);
});

test('lack of extension is okay', function () use ($contextFolder, $sampleStubContent) {
    expect(Stub::from('something')
        ->name('goodfile')
        ->generate()
    )->toBeTrue();

    expect(file_get_contents($contextFolder . '/goodfile'))
        ->toBe($sampleStubContent);
});

test('teardown', function () use ($stubsFolder, $contextFolder) {
    expect(Stub::resetStubFolder())
        ->toBeTrue("Stub folder was not reset. Future tests may fail.");

    expect(Stub::resetContextFolder())
        ->toBeTrue("Context folder was not reset. Future tests may fail.");

    expect($this->deleteDir($stubsFolder))
        ->toBeTrue("Test folder could not be removed for cleanup.");

    expect($this->deleteDir($contextFolder))
        ->toBeTrue("Test folder could not be removed for cleanup.");
});
