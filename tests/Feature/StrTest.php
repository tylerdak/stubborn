<?php

use Dakin\Stubborn\Support\Str;

describe('Str', function () {
    test('camel', function () {
        $testLib = [
            'wordtime' => 'wordtime',
            "word time" => "wordTime",
            "word-time" => "wordTime",
            "word_time" => "wordTime",
            'Stubborn_p_h_p_package' => 'stubbornPHPPackage',
            'Stubborn_php_package' => 'stubbornPhpPackage',
            'Stubborn-phP-package' => 'stubbornPhPPackage',
            'Stubborn  -_-  php   -_-   package   ' => 'stubbornPhpPackage',
            'FooBar' => 'fooBar',
            'foo_bar' => 'fooBar',
            'foo_bar' => 'fooBar',
            'Foo-barBaz' => 'fooBarBaz',
            'foo-bar_baz' => 'fooBarBaz',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::camel($pre))->toBe($post);
        }

    });

    test('kebab', function () {
        $testLib = [
            "wordtime" => "wordtime",
            "word time" => "word-time",
            "wordTime" => "word-time",
            "word_time" => "word-time",
            "StubbornPhpPackage" => "stubborn-php-package",
            'Stubborn_php_package' => 'stubborn-php-package',
            'Stubborn_php_PAckage' => 'stubborn-php-p-ackage',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::kebab($pre))->toBe($post);
        }
    });

    test('length', function () {
        $testLib = [
            '' => 0,
            ' ' => 1,
            '  _ test -hm' => 12,
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::length($pre))->toBe($post);
        }
    });

    test('lower', function () {
        $testLib = [
            'YOU COULD BE A PLUMBER' => 'you could be a plumber',
            'yOu CoUlD bE a PlUmBeR' => 'you could be a plumber',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::lower($pre))->toBe($post);
        }
    });

    test('numbers', function () {
        $testLib = [
            '2#zN43%u!rAAZ%c5!6M%P3e9YoM1^kU!J7AisSVuB!9k$39#99dO027' => '24356391793999027',
            'there shouldn\'t be any numbers here _ :(' => '',
            '42069' => '42069',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::numbers($pre))->toBe($post);
        }
    });

    test('reverse', function () {
        $testLib = [
            'reverse' => 'esrever',
            'racecar' => 'racecar',
            '%90e$3*Jhh' => 'hhJ*3$e09%',
            '' => '',
            "  \t\nokay" => "yako\n\t  ",
            'Teniszütő' => 'őtüzsineT',
            '❤MultiByte☆' => '☆etyBitluM❤',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::reverse($pre))->toBe($post);
        }
    });

    test('upper', function () {
        $testLib = [
            'you could be a plumber' => 'YOU COULD BE A PLUMBER',
            'yOu CoUlD bE a PlUmBeR' => 'YOU COULD BE A PLUMBER',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::upper($pre))->toBe($post);
        }
    });

    test('title', function () {
        $testLib = [
            'i need this in tITle case' => 'I Need This In Title Case',

            'jefferson costella' => 'Jefferson Costella',
            'jefFErson coSTella' => 'Jefferson Costella',
            '' => '',
            '123 stubborn' => '123 Stubborn',
            '❤stubborn' => '❤Stubborn',
            'stubborn ❤' => 'Stubborn ❤',
            'stubborn123' => 'Stubborn123',
            'Stubborn123' => 'Stubborn123',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::title($pre))->toBe($post);
        }
    });

    test('headline', function () {
        $testLib = [
            'i need this in tITle case' => 'I Need This In Title Case',
            'i-love-title-case' => 'I Love Title Case',
            'i_love_title_case' => 'I Love Title Case',
            'iLoveTitleCase' => 'I Love Title Case',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::headline($pre))->toBe($post);
        }
    });

    test('apa', function () {
        $testLib = [
            'i need this in aPA case' => 'I Need This in Apa Case',

            'tom and jerry' => 'Tom and Jerry',
            'TOM AND JERRY' => 'Tom and Jerry',
            'Tom And Jerry' => 'Tom and Jerry',

            'back to the future' => 'Back to the Future',
            'BACK TO THE FUTURE' => 'Back to the Future',
            'Back To The Future' => 'Back to the Future',

            'this, then that' => 'This, Then That',
            'THIS, THEN THAT' => 'This, Then That',
            'This, Then That' => 'This, Then That',

            'bond. james bond.' => 'Bond. James Bond.',
            'BOND. JAMES BOND.' => 'Bond. James Bond.',
            'Bond. James Bond.' => 'Bond. James Bond.',

            'self-report' => 'Self-Report',
            'Self-report' => 'Self-Report',
            'SELF-REPORT' => 'Self-Report',

            'as the world turns, so are the days of our lives' => 'As the World Turns, So Are the Days of Our Lives',
            'AS THE WORLD TURNS, SO ARE THE DAYS OF OUR LIVES' => 'As the World Turns, So Are the Days of Our Lives',
            'As The World Turns, So Are The Days Of Our Lives' => 'As the World Turns, So Are the Days of Our Lives',

            'to kill a mockingbird' => 'To Kill a Mockingbird',
            'TO KILL A MOCKINGBIRD' => 'To Kill a Mockingbird',
            'To Kill A Mockingbird' => 'To Kill a Mockingbird',
        ];

        foreach ($testLib as $pre => $post) {
            expect(Str::apa($pre))->toBe($post);
        }
    });

});
