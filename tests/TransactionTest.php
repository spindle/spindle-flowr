<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class TransactionTest extends TestCase
{
    private $tx, $op1, $op2, $op3, $world;

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

        $this->op3 = new Flowr\AnyOperation(
            function() use(&$world){ $world[] = 'commit'; },
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

        self::assertInstanceOf('Spindle\Flowr\Util\OperationStorage', $tx->operations);

        $tx[] = $op1;
        $tx[] = $op1;
        $tx();

        self::assertEquals(array('commit','commit'), (array)$world);
    }

    /**
     * Txに何も登録されていない状態でのトランザクション実行
     * @test
     */
    function commitImmediate()
    {
        extract(get_object_vars($this));

        self::assertNull($tx());
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
    function rollbackError()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op3;
        $tx[] = $op2;
        $tx();
        self::assertEquals(
            array('commit', 'commit', 'commitNG', 'rollbackNG', 'rollback'),
            (array)$world
        );
    }

    /**
     * commitに対してAOPを織り込む例
     * @test
     */
    function aspect1()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op2;
        $arr = array();

        $op1->COMMIT[] = function($next, $tx=null) use(&$arr) {
            $arr[] = 3;
            return $next($tx);
        };

        $tx->operations->COMMIT[] = function($next, $tx=null) use(&$arr){
            $arr[] = 2;
            return $next($tx);
        };

        $op1->commit[] = function($next, $tx=null) use(&$arr) {
            $arr[] = 1;
            return $next($tx);
        };

        $tx->operations->commit[] = function($next, $tx=null) use(&$arr){
            $arr[] = 0;
            return $next($tx);
        };

        $tx();
        self::assertEquals(array(0,1,2,3,0,2), $arr);
    }

    /**
     * rollbackに対してAOPを織り込む例
     * @test
     */
    function aspect2()
    {
        extract(get_object_vars($this));

        $tx[] = $op1;
        $tx[] = $op2;
        $arr = array();

        $op1->ROLLBACK[] = function($next, $tx=null) use(&$arr) {
            $arr[] = 3;
            return $next($tx);
        };

        $tx->operations->ROLLBACK[] = function($next, $tx=null) use(&$arr){
            $arr[] = 2;
            return $next($tx);
        };

        $op1->rollback[] = function($next, $tx=null) use(&$arr) {
            $arr[] = 1;
            return $next($tx);
        };

        $tx->operations->rollback[] = function($next, $tx=null) use(&$arr){
            $arr[] = 0;
            return $next($tx);
        };

        $tx();
        self::assertEquals(array(0,1,2,3), $arr);
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
