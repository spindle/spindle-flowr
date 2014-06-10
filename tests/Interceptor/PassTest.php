<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class Interceptor_PassTest extends \PHPUnit_Framework_TestCase
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
