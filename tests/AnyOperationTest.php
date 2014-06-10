<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class AnyOperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function commitCallbackMustBeCallable() {
        $op = new Flowr\AnyOperation(1,function(){});
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function rollbackCallbackMustBeCallable() {
        $op = new Flowr\AnyOperation(function(){}, 1);
    }

    /**
     * @test
     */
    function doCommit() {
        $op = new Flowr\AnyOperation(
            function(){ return 'commit'; },
            function(){ return 'rollback'; }
        );

        self::assertSame('commit', $op->commit());
        self::assertSame('rollback', $op->rollback());
    }
}
