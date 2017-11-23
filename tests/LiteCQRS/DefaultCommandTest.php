<?php

namespace LiteCQRS;

use PHPUnit\Framework\TestCase;

class DefaultCommandTest extends TestCase
{
    public function testCreateArrayMapsToPublicProperties()
    {
        $cmd = new TestCommand(Array("test" => "value"));

        $this->assertEquals("value", $cmd->test);
    }

    public function testCreateThrowsExceptionWhenUnknownPropertySet()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Property "unknown" is not a valid property on command "Test".');
        $cmd = new TestCommand(Array("unknown" => "value"));
    }
}

class TestCommand extends DefaultCommand
{
    public $test;
}
