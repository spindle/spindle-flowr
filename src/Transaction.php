<?php
/**
 * spindle/flowr
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Flowr;

class Transaction extends Operation implements
    \ArrayAccess,
    \IteratorAggregate,
    \Countable
{
    //MEMO: cannot to be private. reference by Operation
    protected $operations;

    private $autoRollback = true;
    private $store = array();
    private $history = array();

    function commit(Transaction $tx=null)
    {
        if ($this->operations === null) {
            $this->operations = new Util\OperationStorage;
        }
        $ops = $this->operations;
        $this->history = array();
        $ops->setRewind(true);
        $ops->setForward();

        foreach ($ops as $key => $op) {
            $interceptors = $this->extractInterceptors($op, __FUNCTION__);
            if ($interceptors) {
                $fn = Util\Lambda::nest($interceptors, $this, new Util\OperationInvoker($op, __FUNCTION__));
            } else {
                $fn = new Util\OperationInvoker($op, __FUNCTION__);
            }
            $result = $fn($this);
            $this->addHistory(new History($key, __FUNCTION__, get_class($op), $result));
            if ($result !== null) {
                $ops->setBack();
                $ops->setRewind(false);
                if ($this->autoRollback) {
                    return $this->rollback($tx);
                } else {
                    return $result;
                }
            }
        }
    }

    function rollback(Transaction $tx=null)
    {
        $fatal = null;
        $ops = $this->operations;
        $ops->next();
        foreach ($ops as $key => $op) {
            $interceptors = $this->extractInterceptors($op, __FUNCTION__);
            if ($interceptors) {
                $fn = Util\Lambda::nest($interceptors, $this, new Util\OperationInvoker($op, __FUNCTION__));
            } else {
                $fn = new Util\OperationInvoker($op, __FUNCTION__);
            }
            $result = $fn($this);
            $this->addHistory(new History($key, __FUNCTION__, get_class($op), $result));
            if ($result !== null) {
                $fatal = $result;
            }
        }
        return $fatal;
    }

    private function extractInterceptors(Operation $op, $type)
    {
        assert($type === 'commit' || $type === 'rollback');
        //op->COMMIT
        //tx->operations->COMMIT
        //op->commit
        //tx->operations->commit
        $TYPE = strtoupper($type);
        $interceptors = array();
        if (isset($op->$TYPE)) {
            foreach ($op->$TYPE as $icp) {
                $interceptors[] = $icp;
            }
        }
        if (isset($this->operations->$TYPE)){
            foreach ($this->operations->$TYPE as $icp) {
                $interceptors[] = $icp;
            }
        }
        if (isset($op->$type)) {
            foreach ($op->$type as $icp) {
                $interceptors[] = $icp;
            }
        }
        if (isset($this->operations->$type)) {
            foreach ($this->operations->$type as $icp) {
                $interceptors[] = $icp;
            }
        }

        return $interceptors;
    }

    function setAutoRollback($flag)
    {
        $this->autoRollback = (bool) $flag;
    }

    function getIterator()
    {
        return new \ArrayIterator($this->operations);
    }

    function count()
    {
        return count($this->operations);
    }

    function offsetExists($offset)
    {
        return isset($this->operations[$offset]);
    }

    function offsetGet($offset)
    {
        if (isset($this->operations[$offset])) {
            return $this->operations[$offset];
        } else {
            throw new \OutOfBoundsException("$offset is not exists");
        }
    }

    function offsetSet($offset, $value)
    {
        if ($value instanceof Operation) {
            if ($this->operations === null) {
                $this->operations = new Util\OperationStorage;
            }
            $this->operations[$offset] = $value;
        } else {
            throw new \InvalidArgumentException('Value must be a instance of Operation.');
        }
    }

    function offsetUnset($offset)
    {
        unset($this->operations[$offset]);
    }

    function __invoke(Transaction $tx=null) {
        return $this->commit($tx);
    }

    function set($label, $value) {
        $this->store[$label] = $value;
    }

    function get($label) {
        return $this->store[$label];
    }

    function addHistory(History $h) {
        $this->history[] = $h;
    }

    function getHistory() {
        return $this->history;
    }
}
