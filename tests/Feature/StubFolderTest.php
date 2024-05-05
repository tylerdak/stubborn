<?php

use Dakin\Stubborn\Stub;


test('stub folder can be set', function () {
    // SETUP
    $folder = __DIR__ . '/pretend_stubs_folder';
    expect(Stub::setFolder($folder))->toBeFalse("Folder should not be set if folder does not exist.");

    expect(mkdir($folder))->toBeTrue("Test folder could not be created");

    // TEST
    expect(Stub::setFolder($folder))->toBeTrue("Folder should be set when folder exists.");

    expect(Stub::folder())->toBe($folder, 'Returned Stub folder was incorrect.');
});

test('stub folder is prepended to from path', function () {
    $folder = __DIR__ . '/pretend_stubs_folder'; // CONTEXT

    // TEST
    $stub = file_put_contents($folder . '/testfile',<<<EOL
testfile
EOL);

    $success = Stub::from('testfile')
        ->to(__DIR__ . '/../Generated')
        ->name('result_stubfolder')
        ->ext('php')
        ->generate();

    expect($success)->toBeTrue();
    expect($this->readResult('stubfolder.php'))
        ->toBe('testfile');

    // TEARDOWN
    unlink($folder . DIRECTORY_SEPARATOR . 'testfile');
    expect(rmdir($folder))->toBeTrue("Test folder could not be removed for cleanup.");

    expect(Stub::resetFolder())->toBeTrue("Stub folder was not reset. Future tests may fail.");
});

