<?php
/**
 * create nested interceptor.
 */
namespace Spindle\Flowr\Util;

use Spindle\Flowr\Transaction;

class Lambda {
    static function nest(array $funcs, Transaction $tx, OperationInvoker $origin)
    {
        static $oldPHP;
        if ($oldPHP === null) $oldPHP = version_compare(phpversion(), '5.4.0', '>=');

        $f = $origin;
        foreach ($funcs as $fn) {
            if (! is_callable($fn)) {
                throw new \InvalidArgumentException('$funcs must be array<callable>.');
            }
            //closure normalize
            if ($oldPHP && is_array($fn)) {
                list($obj, $method) = $fn;
                if (is_string($obj)) {
                    $fn = "$obj::$method";
                } else {
                    $fn = function($tx) use($obj, $method) {
                        return $obj->$method($tx);
                    };
                }
            }
            $f = function($tx) use($fn, $f) {
                return $fn($f, $tx);
            };
        }

        return $f;
    }
}
