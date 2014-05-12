<?php
namespace Spindle\Flowr\Util;

use Spindle\Flowr\Operation;

class OperationStorage extends \SplDoublyLinkedList
{
    private $rewind = true;
    private $commit, $rollback, $COMMIT, $ROLLBACK;

    function offsetSet($offset, $value)
    {
        if ($value instanceof Operation) {
            return parent::offsetSet($offset, $value);
        }

        throw new \InvalidArgumentException('Value must be a instance of Operation.');
    }

    function setRewind($flag) {
        $this->rewind = (bool)$flag;
    }

    function rewind() {
        if ($this->rewind) {
            return parent::rewind();
        }
    }

    function setForward() {
        $this->setIteratorMode(self::IT_MODE_FIFO | self::IT_MODE_KEEP);
    }

    function setBack() {
        $this->setIteratorMode(self::IT_MODE_LIFO | self::IT_MODE_KEEP);
    }

    function __get($name) {
        switch ($name) {
            case 'commit': case 'rollback':
            case 'COMMIT': case 'ROLLBACK':
                if ($this->$name === null) {
                    $this->$name = new AdviceStorage;
                }
                return $this->$name;
            default:
                throw new \OutOfRangeException("$name is not exists.");
        }
    }

    function __isset($name) {
        return isset($this->$name);
    }
}
