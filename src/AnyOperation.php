<?php
/**
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Flowr;

/**
 * Operation defnition by callback functions
 * @example
 * new Spindle\Flowr\Operation\Any(
 *  function(){ echo 'commit'; },
 *  function(){ echo 'rollback'; }
 * );
 */
class AnyOperation extends Operation
{
    protected
        $commitFunc
      , $rollbackFunc
      ;

    function __construct($commit, $rollback)
    {
        if (!is_callable($commit) || !is_callable($rollback)) {
            throw new \InvalidArgumentException('commit & rollback must be callable.');
        }

        $this->commitFunc = $commit;
        $this->rollbackFunc = $rollback;
    }

    function commit(Transaction $tx=null)
    {
        return call_user_func($this->commitFunc, $tx, $this);
    }

    function rollback(Transaction $tx=null)
    {
        return call_user_func($this->rollbackFunc, $tx, $this);
    }
}
