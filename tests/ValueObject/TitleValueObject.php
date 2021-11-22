<?php

namespace Tests\ValueObject;

use IsakzhanovR\ValueObject\ValueObject;

class TitleValueObject extends ValueObject
{
    protected function transformInput($value)
    {
        return $value;
    }

    protected function rules(): array
    {
        return [
            $this->key => ['nullable', 'string'],
        ];
    }
}
