<?php


namespace IsakzhanovR\DataTransferObjects\Exceptions;


use Exception;

class UnknownTypePropertyException extends Exception
{
    public function __construct($class, $type, $property, ...$can_be)
    {
        $class = is_string($class) ? $class : get_class($class);

        $message = sprintf('Unknown type "%s" of property "%s" in class %s ', $type, $property, $class);

        if ($can_be) {
            $message .= sprintf('the type must be primitive or inherited from one of the "%s"', implode(', ', $can_be));
        }

        parent::__construct($message);
    }
}
