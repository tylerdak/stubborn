<?php

use Dakin\Stubborn\Stub;

test('stub folder can be set', function () {
    $folder = __DIR__ . '/pretend_stubs_folder';
    expect(Stub::setFolder($folder))->toBeFalse("Folder should not be set if folder does not exist.");

    expect(mkdir($folder))->toBeTrue("Test folder could not be created");

    expect(Stub::setFolder($folder))->toBeTrue("Folder should be set when folder exists.");

    expect(Stub::folder())->toBe($folder, 'Returned Stub folder was incorrect.');

    expect(rmdir($folder))->toBeTrue("Test folder could not be removed for cleanup.");

    expect(Stub::resetFolder())->toBeTrue("Stub folder was not reset. Future tests may fail.");
});
