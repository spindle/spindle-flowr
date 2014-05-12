<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class Util_OperationInvokerTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function invalidType()
    {
        $f = function(){};
        $ivk = new Flowr\Util\OperationInvoker(
            new Flowr\AnyOperation($f, $f),
            'hogehoge'
        );
    }
}
