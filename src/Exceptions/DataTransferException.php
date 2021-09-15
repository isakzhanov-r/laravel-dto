<?php

namespace IsakzhanovR\DataTransferObject\Exceptions;

use Exception;
use Illuminate\Support\Arr;

class DataTransferException extends Exception
{
    public $type;

    public $property;

    public $value;

    public $messages;

    public function __construct($class, $type, $property, $value, ...$messages)
    {
        $class = is_string($class) ? $class : get_class($class);

        $this->messages = Arr::flatten($messages);
        $this->type     = $type;
        $this->property = $property;
        $this->value    = $value;

        $message = sprintf('Invalid value: "%s" in class "%s" , property "%s" type "%s" has errors: "%s"', json_encode($value, JSON_UNESCAPED_UNICODE), $class,
            $property, $type,
            implode(PHP_EOL, $this->messages));

        parent::__construct($message, 422);
    }
}
