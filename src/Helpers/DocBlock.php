<?php

namespace IsakzhanovR\DataTransferObject\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DocBlock
{
    public static function parse(string $docblock): array
    {
        if (preg_match_all('/@(\w+)\s+(.*)\r?\n/m', $docblock, $matches)) {
            return array_combine($matches[1], $matches[2]);
        }

        return [];
    }

    public static function get(string $name, array $parameters): array
    {
        $doc      = Arr::get($parameters, $name);
        $property = '';
        $var = explode(' ', $doc ?? '');

        $type = $var[0];
        unset($var[0]);

        if (isset($var[1]) && Str::startsWith($var[1], '$')) {
            $property .= $var[1];
            unset($var[1]);
        }

        $description = implode(' ', $var);

        return compact('type', 'property', 'description');
    }
}
