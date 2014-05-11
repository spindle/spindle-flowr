<?php
namespace Spindle\Flowr\Interceptor;

use Spindle\Flowr\Transaction;

class Retry {
    private $cnt;

    function __construct($cnt) {
        if (! is_int($cnt)) {
            throw new \InvalidArgumentException('$cnt must be plus integer.');
        }
        if ($cnt < 1) {
            throw new \InvalidArgumentException('$cnt must be plus integer.');
        }
        $this->cnt = $cnt;
    }

    function __invoke($next, Transaction $tx=null) {
        $i = $this->cnt;
        do {
            $result = $next($tx);
            if ($result === null) {
                return $result;
            }
        } while (--$i >= 0);
        return $result;
    }
}
