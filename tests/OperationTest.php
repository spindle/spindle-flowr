<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class OperationMock extends Flowr\Operation
{
    var $commitCnt = 0;
    var $rollbackCnt = 0;
    var $falsy = false;

    function commit(Flowr\Transaction $tx=null) {
        $this->commitCnt++;
        $this->status = 'commit';
        if ($this->falsy) {
            return false;
        }
    }

    function rollback(Flowr\Transaction $tx=null) {
        $this->rollbackCnt++;
        $this->status = 'rollback';
        if ($this->falsy) {
            return false;
        }
    }
}

class OperationTest extends TestCase
{
    /**
     * @test
     */
    function commit() {
        $op = new OperationMock;
        $result = $op->commit();
        self::assertEmpty($result);
        self::assertEquals('commit', $op->status);

        $result = $op->rollback();
        self::assertEmpty($result);
        self::assertEquals('rollback', $op->status);
    }

    /**
     * @test
     */
    function aop() {
        $op = new OperationMock;
        $op->falsy = true;
        self::assertFalse(isset($op->commit));
        self::assertFalse(isset($op->COMMIT));
        self::assertFalse(isset($op->rollback));
        self::assertFalse(isset($op->ROLLBACK));
        $op->commit[] = new Flowr\Interceptor\Retry(2);
        $op->COMMIT[] = new Flowr\Interceptor\Retry(2);
        $op->rollback[] = new Flowr\Interceptor\Retry(2);
        $op->ROLLBACK[] = new Flowr\Interceptor\Retry(2);

        self::assertCount(1, $op->commit);
        self::assertCount(1, $op->COMMIT);
        self::assertCount(1, $op->rollback);
        self::assertCount(1, $op->ROLLBACK);
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     */
    function invalidProperty() {
        $op = new OperationMock;
        $p = $op->invalidproperty;
    }
}
