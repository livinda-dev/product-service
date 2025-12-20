<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_ci_should_fail()
    {
        $this->assertTrue(false, 'Intentional CI failure');
    }
}
