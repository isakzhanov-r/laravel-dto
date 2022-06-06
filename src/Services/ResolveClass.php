<?php

namespace IsakzhanovR\DataTransferObject\Services;

use Exception;
use IsakzhanovR\DataTransferObject\Exceptions\DataTransferException;
use IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException;
use IsakzhanovR\DataTransferObject\Helpers\Types;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

class ResolveClass
{
    protected $class;

    protected $reflection;

    public function __construct($class)
    {
        $this->class      = $class;
        $this->reflection = new ReflectionClass($class);
    }

    public function getProperty(string $name)
    {
        return $this->reflection->getProperty($name);
    }

    /**
     * @return \ReflectionProperty[]
     */
    public function getProperties()
    {
        return array_filter(
            $this->reflection->getProperties(ReflectionProperty::IS_PUBLIC),
            fn(ReflectionProperty $property) => !$property->isStatic()
        );
    }

    /**
     * @param \ReflectionProperty $property
     * @param $value
     *
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\DataTransferException
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException
     */
    public function setValue(ReflectionProperty $property, $value)
    {
        try {

            $property->setAccessible(true);

            $type = $property->hasType() ? $this->propertyType($property) : '';

            $property->setValue($this->class, $this->typed($type, $value));

        } catch (TypeErrorException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new DataTransferException($this, $type, $property->getName(), $value, $exception->getMessage());
        }
    }

    /**
     * @param string $type
     * @param $value
     *
     * @return mixed|object|void
     * @throws \ReflectionException
     */
    public function typed(string $type, $value)
    {
        if (Types::has($type) || !$type) {
            return $value;
        }

        if (class_exists($type)) {
            $class = new ReflectionClass($type);

            if (is_object($value) && $class->isInstance($value)) {
                return $value;
            }
            if (is_null($value)) {
                return null;
            }

            return $class->newInstance($value);
        }
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return string
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException
     */
    private function propertyType(ReflectionProperty $property): string
    {
        if ($property->getType() instanceof ReflectionNamedType) {
            return $property->getType()->getName();
        }

        throw new TypeErrorException('The variable type must be one');
    }
}
