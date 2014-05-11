<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class Util_AdviceStorageTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function typehinting() {
        $s = new Flowr\Util\AdviceStorage;
        $s[] = 5;
    }

    /**
     * @test
     */
    function typeok() {
        $s = new Flowr\Util\AdviceStorage;
        $s[] = function(){};
        $s[] = 'htmlspecialchars';
        self::assertCount(2, $s);
    }
}
