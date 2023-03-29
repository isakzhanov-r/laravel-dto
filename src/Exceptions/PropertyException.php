<?php

namespace IsakzhanovR\DataTransferObject\Exceptions;

use Exception;

abstract class PropertyException extends Exception
{
    public function __construct(
        protected Exception $exception,
        protected $in_dto,
        protected $type,
        protected $property,
        protected $value
    )
    {
        parent::__construct($this->setMessage(), $this->setCode());
    }

    protected abstract function setMessage();

    protected abstract function setCode();
}
