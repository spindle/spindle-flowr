<?php
namespace Spindle\Flowr\Util;

class AdviceStorage extends \SplFixedArray
{
    function offsetSet($offset, $value)
    {
        if (is_callable($value)) {
            if ($offset === null) {
                $cnt = count($this);
                $this->setSize($cnt + 1);
                $offset = $cnt;
            }
            parent::offsetSet($offset, $value);
        } else {
            throw new \InvalidArgumentException('Value must be callable.');
        }
    }
}
