<?php
namespace Spindle\Flowr\Interceptor;

use Spindle\Flowr\Transaction;

class Pass {
    function __invoke($next, Transaction $tx=null) {
        try {
            return $next($tx);
        } catch (\Exception $e) {
            return $e;
        }
    }
}
