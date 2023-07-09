<?php

namespace IsakzhanovR\DataTransferObject\Exceptions;

use Throwable;

interface DTOThrowable extends Throwable
{
    public function __construct(Throwable $exception, $class = null, $type = null, $property = null, $value = null);
}
