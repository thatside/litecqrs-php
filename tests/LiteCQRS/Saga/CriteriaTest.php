<?php

namespace LiteCQRS;

use LiteCQRS\Saga\State\Criteria;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    public function testCriteria()
    {
        $comparisons = array('test1' => 'test1', 'test2' => 'test2');
        $criteria = new Criteria($comparisons);

        $this->assertEquals($comparisons, $criteria->getComparisons());
    }
}