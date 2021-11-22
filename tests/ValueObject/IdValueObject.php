<?php

namespace Tests\ValueObject;

use IsakzhanovR\ValueObject\ValueObject;

class IdValueObject extends ValueObject
{
    protected function transformInput($value)
    {
        return (int) $value;
    }

    protected function rules(): array
    {
        return [
            $this->key => ['required', 'numeric'],
        ];
    }
}
