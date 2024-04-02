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
        unlink(__DIR__ . '/Feature/test.stub');
    }
}
