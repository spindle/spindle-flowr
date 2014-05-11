<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class Util_LambdaTest extends TestCase
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
            $tx,
            new Flowr\Util\OperationInvoker($op, 'commit')
        );
        $nested($tx);

        self::assertEquals(array('<b>','<a>','</a>','</b>'), $world);
    }
}
