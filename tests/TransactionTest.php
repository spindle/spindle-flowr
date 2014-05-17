<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class TransactionTest extends TestCase
{
    private $tx, $op1, $op2, $world;

    function setup()
    {
        $this->world = $world = new \ArrayObject;
        $this->tx = new Flowr\Transaction;
        $this->op1 = new Flowr\AnyOperation(
            function() use(&$world){ $world[] = 'commit'; },
            function() use(&$world){ $world[] = 'rollback'; }
        );

        $this->op2 = new Flowr\AnyOperation(
            function() use(&$world){ $world[] = 'commitNG'; return 'ng'; },
            function() use(&$world){ $world[] = 'rollbackNG'; return 'ng'; }
        );
    }

    /**
     * @test
     * @cover Spindle\Flowr\Transaction::commit
     */
    function commitSimple()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op1;
        $tx();

        self::assertEquals(array('commit','commit'), (array)$world);
    }

    /**
     * @test
     */
    function commitAndAutoRollback()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op1;
        $tx[] = $op2;
        $tx();
        self::assertEquals(
            array('commit', 'commit', 'commitNG', 'rollback', 'rollback'),
            (array)$world
        );
        self::assertInternalType('array', $tx->getHistory());
    }

    /**
     * @test
     */
    function commitAndManualRollback()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op1;
        $tx[] = $op2;
        $tx->setAutoRollback(false);
        $tx();
        self::assertEquals(
            array('commit', 'commit', 'commitNG'),
            (array)$world
        );

        $tx->rollback();
        self::assertEquals(
            array('commit', 'commit', 'commitNG', 'rollback', 'rollback'),
            (array)$world
        );
    }

    /**
     * @test
     */
    function aspect1()
    {
        extract(get_object_vars($this));

    }

    /**
     * @test
     */
    function txMustBeIteratable()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op1;

        foreach ($tx as $op) {
            self::assertInstanceOf('Spindle\Flowr\Operation', $op);
        }

        self::assertCount(2, $tx);
    }

    /**
     * @test
     */
    function arrayOverload1()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op1;

        self::assertArrayHasKey(0, $tx);
        self::assertEquals($op1, $tx[0]);
        self::assertEquals($op1, $tx[1]);

        unset($tx[1]);
        self::assertArrayNotHasKey(1, $tx);
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     */
    function arrayOverload2()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op1;

        $op = $tx[2];
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function arrayOverload3()
    {
        $this->tx[] = 'string';
    }

    /**
     * @test
     */
    function setAndGet()
    {
        $this->tx->set('a', 1);
        self::assertSame(1, $this->tx->get('a'));
    }
}
