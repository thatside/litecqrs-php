<?php

namespace LiteCQRS;

use LiteCQRS\Saga\Metadata\Metadata;
use LiteCQRS\Saga\State\Criteria;
use PHPUnit\Framework\TestCase;

class MetadataTest extends TestCase
{
    private $criteria;
    private $metadata;

    public function setUp()
    {
        $this->criteria = new Criteria(array('123' => 123));
        $this->metadata = new Metadata(
            array(
                'ExampleEvent' => function (ExampleEvent $event) {
                    return $this->criteria;
                }
            )
        );
    }

    public function testMetadataHandlesEvent()
    {
        $this->assertEquals(true, $this->metadata->handles(new ExampleEvent()));
    }

    public function testMetadataReturnsCriteria()
    {
        $actual = $this->metadata->criteria(new ExampleEvent());
        $this->assertEquals($this->criteria, $actual);
    }
}

class ExampleEvent extends DefaultDomainEvent
{
}