<?php
namespace Spindle\Flowr;

abstract class Operation
{
    private $commit, $rollback, $COMMIT, $ROLLBACK;

    abstract function commit(Transaction $tx=null);
    abstract function rollback(Transaction $tx=null);

    //to readonly
    final function __get($name) {
        switch ($name) {
            case 'commit': case 'rollback':
            case 'COMMIT': case 'ROLLBACK':
                if ($this->$name === null) {
                    $this->$name = new Util\AdviceStorage;
                }
                return $this->$name;
            case 'operations':
                if ($this instanceof Transaction) {
                    if ($this->$name === null) {
                        $this->$name = new Util\OperationStorage;
                    }
                    return $this->$name;
                }
                // through
            default:
                throw new \OutOfRangeException("$name is not exists.");
        }
    }

    //to readonly
    final function __isset($name) {
        return isset($this->$name);
    }
}
