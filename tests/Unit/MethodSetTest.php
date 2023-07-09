<?php

namespace Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use IsakzhanovR\DataTransferObject\DataTransferObject;
use Tests\TestCase;
use Tests\ValueObject\StringException;
use Tests\ValueObject\TitleValueObject;

class MethodSetTest extends TestCase
{
    public function testCreateDTO()
    {
        $dto = $this->createDTOClass([
                'id'         => 2,
                'string'     => 'Title as string',
                'collection' => ['qwerty', 'tests', 'title']]
        );

        $this->assertEquals($dto->string, new TitleValueObject('Title as string'));
    }

    public function testStringExceptionDTO()
    {
        $this->expectException(StringException::class);

        $this->createDTOClass([
                'id'         => 2,
                'string'     => 'Title',
                'collection' => ['qwerty', 'tests', 'title']]
        );
    }

    public function testValidationExceptionDTO()
    {
        $this->expectException(ValidationException::class);

        $this->createDTOClass([
                'id'         => null,
                'string'     => 'Title as string',
                'collection' => ['qwerty', 'tests', 'title']]
        );
    }

    protected function createDTOClass(array $args)
    {
        return new class($args) extends DataTransferObject {
            public int $id;

            /**
             * @throws \Tests\ValueObject\StringException
             * @var \Tests\ValueObject\TitleValueObject
             */
            public TitleValueObject $string;

            /**
             * @var \Tests\ValueObject\TitleValueObject[] $collection
             */
            public Collection $collection;

            private function setId($id)
            {
                $validator = Validator::make(
                    compact('id'),
                    ['id' => ['required', 'integer',]]
                )->validate();

                return Arr::get($validator, 'id');
            }

            private function setString($string)
            {
                $validator = Validator::make(
                    compact('string'),
                    ['string' => ['required', 'string', 'min:8']]
                )->validate();

                return new TitleValueObject(Arr::get($validator, 'string'));
            }
        };
    }
}
