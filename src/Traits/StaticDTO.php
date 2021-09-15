<?php


namespace IsakzhanovR\DataTransferObjects\Traits;


use Illuminate\Http\Request;

trait StaticDTO
{
    public static function fromRequest(Request $request)
    {
        return new static($request->validated());
    }

    public static function fromArray(array $array)
    {
        return new static($array);
    }

    public static function fromObject(object $object)
    {
        $array = get_object_vars($object);

        return new static($array);
    }

    public static function fromJSON(string $json)
    {
        return static::fromObject(json_decode($json));
    }
}
