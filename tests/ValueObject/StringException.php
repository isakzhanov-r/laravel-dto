<?php

namespace Tests\ValueObject;

use Exception;
use Illuminate\Validation\ValidationException;
use IsakzhanovR\DataTransferObject\Exceptions\DTOThrowable;
use IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException;
use Throwable;

class StringException extends Exception implements DTOThrowable
{
    public function __construct(Throwable $exception, $class = null, $type = null, $property = null, $value = null)
    {
        $message = $exception->getMessage();

        if ($exception instanceof TypeErrorException) {
            $message = 'Type error';
        }

        if ($exception instanceof ValidationException) {
            $message = 'Validation';
        }

        parent::__construct('Error is ' . $message);
    }
}
