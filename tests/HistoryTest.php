<?php
namespace Spindle\Flowr\Tests;

use Spindle\Flowr;

class HistoryTest extends \PHPUnit_Framework_TestCase
{
    function setup() {
        $this->setExpectedException('\InvalidArgumentException');
    }

    function testConstructor1() {
        new Flowr\History(null, 'commit', 'Hoge', null);
    }

    function testConstructor2() {
        new Flowr\History(1.1, 'commit', 'Hoge', null);
    }

    function testConstructor3() {
        new Flowr\History(1, null, 'Hoge', null);
    }

    function testConstructor4() {
        new Flowr\History(1, 'commit', null, null);
    }

    function testConstructor5() {
        $this->setExpectedException(null); //unset
        new Flowr\History(1, 'commit', 'Hoge', null);
        $history = new Flowr\History(1, 'rollback', 'Hoge', null);

        self::assertEquals(1, $history->label);
        self::assertEquals('rollback', $history->type);
        self::assertEquals('Hoge', $history->class);
        self::assertNull($history->result);

        $this->setExpectedException('\OutOfRangeException');
        $notdefined = $history->notdefined;
    }

    function testSet() {
        $this->setExpectedException('\OutOfRangeException');
        $history = new Flowr\History(1, 'rollback', 'Hoge', null);
        $history->notdefined = 5;
    }
}
