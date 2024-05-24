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
    }

    protected function tearDown(): void
    {
        /* die(); */
        unlink(__DIR__ . '/Feature/test.stub');
        array_map('unlink', glob(__DIR__ . '/Feature/test*.stub'));
        array_map('unlink', glob(__DIR__ . '/Generated/test*'));
        array_map('unlink', glob(__DIR__ . '/Generated/result*'));
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
    protected function readResult(?string $id = null): ?string {
        if ($result = file_get_contents(__DIR__ . '/Generated/result_' . $id)) {
            return $result;
        }
        return null;
    }
    protected function writeTest(?string $id = null, string $contents = ""): ?string {
        $filepath = __DIR__ . '/Generated/test_' . $id;
        if (file_put_contents($filepath, $contents)) {
            return $filepath;
        }
        return null;
    }
}
