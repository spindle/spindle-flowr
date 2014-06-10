<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class Util_LambdaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function nest() {
        $world = array();
        $op = new Flowr\AnyOperation(
            function(){},
            function(){}
        );
        $a = function($next, $tx) use(&$world) {
            $world[] = '<a>';
            $result = $next($tx);
            $world[] = '</a>';
            return $result;
        };
        $b = function($next, $tx) use(&$world) {
            $world[] = '<b>';
            $result = $next($tx);
            $world[] = '</b>';
            return $result;
        };
        $tx = new Flowr\Transaction;
        $nested = Flowr\Util\Lambda::nest(
            array($a, $b),
            new Flowr\Util\OperationInvoker($op, 'commit')
        );
        $nested($tx);

        self::assertEquals(array('<b>','<a>','</a>','</b>'), $world);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function notCallable()
    {
        $op = new Flowr\AnyOperation(
            function(){},
            function(){}
        );

        $tx = new Flowr\Transaction;

        $nested = Flowr\Util\Lambda::nest(
            array('not callable'),
            new Flowr\Util\OperationInvoker($op, 'commit')
        );
        $nested($tx);
    }
}
