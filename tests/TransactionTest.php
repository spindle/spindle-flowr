<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class TransactionTest extends TestCase
{
    /**
     * @test
     */
    function commit() {
        $world = array();
        $tx = new Flowr\Transaction;
        $op = new Flowr\AnyOperation(
            function() use(&$world){ $world[] = 'commit'; },
            function() use(&$world){ $world[] = 'rollback'; }
        );

        $tx[] = $op;
        $tx[] = $op;
        $tx();

        self::assertEquals(array('commit','commit'), $world);

        $world = array();
        $op2 = new Flowr\AnyOperation(
            function() use(&$world){ $world[] = 'commitNG'; return 'ng'; },
            function() use(&$world){ $world[] = 'rollbackNG'; return 'ng'; }
        );
        $tx[] = $op2;
        $tx();
        self::assertEquals(
            array('commit', 'commit', 'commitNG', 'rollback', 'rollback'),
            $world
        );
    }
}
