<?php

namespace Tests\Unit;

use IsakzhanovR\DataTransferObject\DataTransferObject;
use PHPUnit\Framework\TestCase;
use TypeError;

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

        $this->assertObjectNotHasAttribute('foo', $dto);
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
        } catch (TypeError $exception) {
            self::assertEquals('Typed property class@anonymous::$title must be string, null used', $exception->getMessage());
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
