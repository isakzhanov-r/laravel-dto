<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use IsakzhanovR\DataTransferObject\DataTransferObject;
use stdClass;
use Tests\TestCase;
use Tests\ValueObject\FieldDTO;
use Tests\ValueObject\TitleValueObject;

class ClassTypesTest extends TestCase
{
    public function testCreatDTOArray()
    {
        $array = [
            'id'    => 1,
            'title' => 'Title as string',
            'value' => 'Value',
        ];

        $dto = FieldDTO::fromArray($array);

        $this->assertEquals($dto->title, new TitleValueObject('Title as string'));
    }

    public function testCreateDTOJson()
    {
        $json = '{"id":1,"title":"Title as string","value":"Value"}';

        $dto = FieldDTO::fromJSON($json);

        $this->assertEquals($dto->title, new TitleValueObject('Title as string'));
    }

    public function testCreateDTOObject()
    {
        $std        = new stdClass();
        $std->id    = 1;
        $std->title = 'Title as string';
        $std->value = 'Value';

        $dto = FieldDTO::fromObject($std);

        $this->assertEquals($dto->title, new TitleValueObject('Title as string'));
    }

    public function testArrayValues()
    {
        $data = [
            'collection' => ['qwerty', 'tests', 'title'],
        ];

        $dto = $this->createDTOClass($data);

        $this->assertEquals($dto->collection->random() instanceof TitleValueObject, true);
    }

    public function testToArray()
    {
        $data = [
            'id'         => 1,
            'title'      => 'Title as string',
            'collection' => ['qwerty', 'tests', 'title'],
        ];

        $dto = $this->createDTOClass($data)->only('collection')->toArray();
        $this->assertEquals($dto, ["collection" => ["qwerty", "tests", "title"]]);
    }

    protected function createDTOClass(array $args)
    {
        return new class($args) extends DataTransferObject {
            public int $id = 0;

            public ?string $string;

            /**
             * @var \Tests\ValueObject\TitleValueObject[] $collection
             */
            public Collection $collection;
        };
    }
}
