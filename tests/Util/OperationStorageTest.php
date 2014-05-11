<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class HogeOperationMock extends Flowr\Operation
{
    function commit(Flowr\Transaction $tx=null) {
        echo 'commit';
    }

    function rollback(Flowr\Transaction $tx=null) {
        echo 'rollback';
    }
}

class Util_OperationStorageTest extends TestCase
{
    private $target;

    function setup() {
        $this->target = new Flowr\Util\OperationStorage;
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function testValidation() {
        $s = $this->target;
        $s[] = 'foo';
    }

    /**
     * @test
     */
    function iteration() {
        $s = $this->target;
        $s[] = new OperationMock;
        $s[] = new OperationMock;

        $keys = array();
        foreach ($s as $key => $val) {
            self::assertInstanceOf(__NAMESPACE__ . '\\OperationMock', $val);
            $keys[] = $key;
        }

        self::assertEquals(array(0, 1), $keys);
    }

    /**
     * @test
     */
    function reverseIteration() {
        $s = $this->target;
        for ($i=0; $i<10; $i++) {
            $s[] = new OperationMock;
        }

        $iterates = array();
        foreach ($s as $key => $val) {
            $iterates[] = $key;
            if ($key == 5) {
                break;
            }
        }
        self::assertEquals(array(0,1,2,3,4,5), $iterates);

        $s->setRewind(false);
        $s->setBack();

        $iterates = array();
        foreach ($s as $key => $val) {
            $iterates[] = $key;
        }
        self::assertEquals(array(5,4,3,2,1,0), $iterates);

        $s->setForward();
        $s->setRewind(true);
        $iterates = array();
        foreach ($s as $key => $val) {
            $iterates[] = $key;
        }
        self::assertEquals(array(0,1,2,3,4,5,6,7,8,9), $iterates);
    }
}
