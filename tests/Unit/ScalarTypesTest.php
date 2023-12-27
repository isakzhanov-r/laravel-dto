<?php

namespace Tests\Unit;

use IsakzhanovR\DataTransferObject\DataTransferObject;
use IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException;
use PHPUnit\Framework\TestCase;

class ScalarTypesTest extends TestCase
{
    public function testFullCompletion()
    {
        $dto = $this->createDTOClass([
            'id'      => 1,
            'title'   => 'This is title',
            'content' => 'This is content',
            'meta'    => 'Any field',
        ]);

        $this->assertEquals($dto->id, 1);
        $this->assertEquals($dto->title, 'This is title');
        $this->assertEquals($dto->content, 'This is content');
        $this->assertEquals($dto->meta, 'Any field');
    }

    public function testUndeclaredData()
    {
        $dto = $this->createDTOClass([
            'id'      => 1,
            'title'   => 'This is title',
            'content' => 'This is content',
            'meta'    => 'Any field',
            'foo'     => 'is undeclared',
        ]);

        $this->assertFalse(property_exists($dto, 'foo'));
    }

    public function testDefaultValues()
    {
        $dto = $this->createDTOClass([
            'title' => 'This is title',
        ]);

        $this->assertEquals($dto->id, 1);
        $this->assertEquals($dto->content, null);
        $this->assertEquals($dto->meta, null);
    }

    public function testRequiredData()
    {
        try {
            $this->createDTOClass([
                'id'      => 1,
                'content' => 'This is content',
                'meta'    => 'Any field',
            ]);
        } catch (TypeErrorException $exception) {
            self::assertEquals('TypeError: Cannot assign null to property IsakzhanovR\DataTransferObject\DataTransferObject@anonymous::$title of type string',
                $exception->getMessage());
        }
    }

    protected function createDTOClass(array $args)
    {
        return new class($args) extends DataTransferObject {
            public int $id = 1;

            public string $title;

            public ?string $content;

            public $meta;
        };
    }
}
