<?php
/**
 * Transaction History Object
 *
 */
namespace Spindle\Flowr;

/**
 * @property string $label
 * @property string $type    'commit' or 'rollback'
 * @property string $class
 * @property mixed  $result
 */
final class History {
    private $label;
    private $type;
    private $class;
    private $result;

    /**
     * @param string|int $label
     * @param string $type
     * @param string $class
     * @param mixed  $result
     */
    function __construct($label, $type, $class, $result)
    {
        if (is_string($label) || is_int($label)) {
            $this->label = $label;
        } else {
            throw new \InvalidArgumentException('$label is invalid.');
        }
        if ($type === 'commit' || $type === 'rollback') {
            $this->type = $type;
        } else {
            throw new \InvalidArgumentException('$type is invalid.');
        }
        if (is_string($class)) {
            $this->class = $class;
        } else {
            throw new \InvalidArgumentException('$class is invalid.');
        }
        $this->result = $result;
    }

    function __get($name) {
        switch ($name) {
        case 'label': case 'type':
        case 'class': case 'result':
            return $this->$name;
        }

        throw new \OutOfRangeException("$name is undefined.");
    }

    function __set($name, $value) {
        throw new \OutOfRangeException("Spindle\\Flowr\\History->__set($name, $value) is not allowed.");
    }
}
