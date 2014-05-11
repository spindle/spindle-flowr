<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class Interceptor_PassTest extends TestCase
{
    /**
     * @test
     */
    function exceptionCatching() {
        $pass = new Flowr\Interceptor\Pass;
        $thrower = function(){
            throw new \RuntimeException;
        };
        self::assertInstanceOf('\RuntimeException', $pass($thrower));
    }
}
