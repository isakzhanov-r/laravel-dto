<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use IsakzhanovR\DataTransferObject\DataTransferObject;
use Tests\TestCase;
use Tests\ValueObject\FieldDTO;
use Tests\ValueObject\IdValueObject;
use Tests\ValueObject\TitleValueObject;

class CompoundTypesTest extends TestCase
{
    public function testCompoundTypes()
    {
        $data = [
            'id'         => 1,
            'title'      => 'Title',
            'fields'     => [
                ['id' => 1, 'title' => 'Title as string', 'value' => 'Value 1'],
                ['id' => 2, 'title' => 'Title as string', 'value' => 'Value 2'],
                ['id' => 3, 'title' => 'Title as string', 'value' => 'Value 3'],

            ],
            'collection' => [
                ['id' => 1, 'title' => 'Title as string', 'value' => 'Value 1'],
                ['id' => 2, 'title' => 'Title as string', 'value' => 'Value 2'],
                ['id' => 3, 'title' => 'Title as string', 'value' => 'Value 3'],
            ],
            'collect'    => 'string',
        ];

        $dto = $this->createDTOClass($data);

        $this->assertIsArray($dto->fields);
        $this->assertEquals($dto->collection->first(), new FieldDTO(['id' => 1, 'title' => 'Title as string', 'value' => 'Value 1']));
    }

    public function testDefaultData()
    {
        $data = [
            'id'         => 1,
            'fields'     => [
                ['id' => 1, 'title' => 'Title as string', 'value' => 'Value 1'],
                ['id' => 2, 'title' => 'Title as string', 'value' => 'Value 2'],
                ['id' => 3, 'title' => 'Title as string', 'value' => 'Value 3'],

            ],
            'collection' => [
                ['id' => 1, 'title' => 'Title as string', 'value' => 'Value 1'],
                ['id' => 2, 'title' => 'Title as string', 'value' => 'Value 2'],
                ['id' => 3, 'title' => 'Title as string', 'value' => 'Value 3'],
            ],
            'collect'    => 'string',
        ];

        $dto = $this->createDTOClass($data);

        $this->assertEquals($dto->title, null);
    }

    protected function createDTOClass(array $args)
    {
        return new class($args) extends DataTransferObject {
            public IdValueObject $id;

            public ?TitleValueObject $title;

            /**
             * @var \Tests\ValueObject\FieldDTO[] $fields
             */
            public array $fields;

            /**
             * @var \Tests\ValueObject\FieldDTO[] $collection
             */
            public Collection $collection;

            public Collection $collect;
        };
    }
}
