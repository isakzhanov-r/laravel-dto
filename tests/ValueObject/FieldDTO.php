<?php

namespace Tests\ValueObject;

use IsakzhanovR\DataTransferObject\DataTransferObject;

class FieldDTO extends DataTransferObject
{
    public int $id;

    public TitleValueObject $title;

    public string $value;
}
