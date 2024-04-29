<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set up.
     */
    protected function setUp(): void
    {
        parent::setUp();

        file_put_contents(__DIR__ . '/Feature/test.stub', <<<EOL
<?php

namespace {{ NAMESPACE }};

class {{ CLASS }}
{
    use Illuminate\Database\Eloquent\Factories\{{ TRAIT }};
}
EOL
        );

        file_put_contents(__DIR__ . '/Feature/test_regex.stub', <<<EOL
<?php

namespace {{ VARIABLE }};

class {{ VARIABLE:fakemod-test.the_punctuation!::upper }}
{
    use Illuminate\Database\Eloquent\Factories\{{ VARIABLE::modifier::lower }};
}
EOL
        );
    }

    protected function tearDown(): void
    {
        /* die(); */
        unlink(__DIR__ . '/Feature/test.stub');
        array_map('unlink', glob(__DIR__ . '/Feature/test*.stub'));
    }

    protected function bulkCompare($callback,$arr) {
        foreach($arr as $pre => $val) {
            $args = [];
            if (is_array($val)) {
                $post = $val[0] ?? null;
                $args = array_slice($val,1);
            }
            else {
                $post = $val;
            }
            expect($callback($pre,...$args))->toBe($post);
        }
    }
}
