<?php

namespace IsakzhanovR\DataTransferObjects\Helpers;

class Types
{
    private static $types = [
        'date'     => 'date',
        'datetime' => 'date',
        'bool'     => 'boolean',
        'boolean'  => 'boolean',
        'digits'   => 'float',
        'float'    => 'float',
        'int'      => 'integer',
        'integer'  => 'integer',
        'numeric'  => 'integer',
        'str'      => 'string',
        'string'   => 'string',
        'array'    => 'array',
        'object'   => 'object',
        'json'     => 'array',
    ];

    /**
     * Helper for getting a primitive data type
     *
     * @param string|null $type
     *
     * @return string
     */
    public static function get(string $type = null): string
    {
        return static::$types[$type] ?? 'string';
    }

    /**
     * Helper for checking the primitive type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function has(string $type): bool
    {
        return in_array($type, array_keys(static::$types));
    }
}
