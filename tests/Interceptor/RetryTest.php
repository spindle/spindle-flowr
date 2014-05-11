<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr\Interceptor\Retry;

class Interceptor_RetryTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function constructor() {
        $retry = new Retry('abc');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function constructor2() {
        $retry = new Retry(-1);
    }

    /**
     * @test
     */
    function retryNumber() {
        $retryCnt = 0;
        $retry = new Retry(1);

        $fn = function() use(&$retryCnt) {
            $retryCnt++;
            return 'error';
        };
        $result = $retry($fn);
        self::assertEquals('error', $result);
        self::assertEquals(2, $retryCnt);

        $retry = new Retry(5);
        $retryCnt = 0;
        $result = $retry($fn);
        self::assertEquals('error', $result);
        self::assertEquals(6, $retryCnt);

        $retry = new Retry(1);
        $retryCnt = 0;
        $fn = function() use(&$retryCnt) {
            $retryCnt++;
        };
        $result = $retry($fn);
        self::assertNull($result);
        self::assertEquals(1, $retryCnt);
    }
}
