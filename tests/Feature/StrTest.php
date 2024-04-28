<?php

use Dakin\Stubborn\Support\Str;

describe('Str', function () {
    test('camel', function () {
        $pre = "wordtime";
        $post = "wordtime";
        expect(Str::camel($pre))->toBe($post);

        $pre = "word time";
        $post = "wordTime";
        expect(Str::camel($pre))->toBe($post);

        $pre = "word-time";
        $post = "wordTime";
        expect(Str::camel($pre))->toBe($post);

        $pre = "word_time";
        $post = "wordTime";
        expect(Str::camel($pre))->toBe($post);

        expect(Str::camel('Stubborn_p_h_p_package'))->toBe('stubbornPHPPackage');
        expect(Str::camel('Stubborn_php_package'))->toBe('stubbornPhpPackage');
        expect(Str::camel('Stubborn-phP-package'))->toBe('stubbornPhPPackage');
        expect(Str::camel('Stubborn  -_-  php   -_-   package   '))->toBe('stubbornPhpPackage');

        expect(Str::camel('FooBar'))->toBe('fooBar');
        expect(Str::camel('foo_bar'))->toBe('fooBar');
        expect(Str::camel('foo_bar'))->toBe('fooBar'); // duplicated to test the cache
        expect(Str::camel('Foo-barBaz'))->toBe('fooBarBaz');
        expect(Str::camel('foo-bar_baz'))->toBe('fooBarBaz');
    });

    test('kebab', function () {
        $pre = "wordtime";
        $post = "wordtime";
        expect(Str::kebab($pre))->toBe($post);

        $pre = "word time";
        $post = "word-time";
        expect(Str::kebab($pre))->toBe($post);

        $pre = "wordTime";
        $post = "word-time";
        expect(Str::kebab($pre))->toBe($post);

        $pre = "word_time";
        $post = "word-time";
        expect(Str::kebab($pre))->toBe($post);

        $pre = "StubbornPhpPackage";
        $post = "stubborn-php-package";
        expect(Str::kebab($pre))->toBe($post);
    });
});
