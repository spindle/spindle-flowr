<?php
namespace Spindle\Flowr\Util;

use Spindle\Flowr;

final class OperationInvoker
{
    private $op, $type;

    function __construct(Flowr\Operation $op, $type)
    {
        if ($type !== 'commit' && $type !== 'rollback') {
            throw new \InvalidArgumentException('$type must be "commit" or "rollback".');
        }
        $this->op = $op;
        $this->type = $type;
    }

    function __invoke(Flowr\Transaction $tx)
    {
        $op = $this->op;
        return $op->{$this->type}($tx);
    }
}
