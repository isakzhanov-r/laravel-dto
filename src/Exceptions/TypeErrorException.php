<?php


namespace IsakzhanovR\DataTransferObject\Exceptions;


use Exception;
use function preg_replace;
use function sprintf;

class TypeErrorException extends Exception
{
    public function __construct($message = "", $code = 0)
    {
        $message = preg_replace('/, called in .*?: eval\\(\\)\'d code/', '', $message);

        parent::__construct(sprintf('TypeError: %s', $message), $code);
    }
}
