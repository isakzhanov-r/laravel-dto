<?php

namespace IsakzhanovR\DataTransferObject\Services;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use IsakzhanovR\DataTransferObject\Exceptions\DataTransferException;
use IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException;
use IsakzhanovR\DataTransferObject\Helpers\Types;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use TypeError;

class ResolveClass
{
    protected $class;

    protected ReflectionClass $reflection;

    /**
     * @throws \ReflectionException
     */
    public function __construct($class)
    {
        $this->class      = $class;
        $this->reflection = new ReflectionClass($class);
    }

    /**
     * @param string $name
     *
     * @return \ReflectionProperty
     * @throws \ReflectionException
     */
    public function getProperty(string $name): ReflectionProperty
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
     * @return void
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\DataTransferException
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\PropertyException
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException
     */
    public function setValue(ReflectionProperty $property, $value, string $throw = ""): void
    {
        try {
            $method = 'set' . Str::ucfirst($property->name);
            $type   = $property->hasType() ? $this->propertyType($property) : '';

            switch (true) {
                case method_exists($this->class, $method):
                    $reflectionMethod = $this->reflection->getMethod($method);
                    $property->setValue($this->class, $reflectionMethod->invoke($this->class, $value));
                    break;
                default:
                    $property->setValue($this->class, $this->typed($type, $value));
                    break;
            }

        } catch (TypeError $exception) {
            throw new TypeErrorException($exception->getMessage());
        } catch (TypeErrorException|ValidationException $exception) {
            if ($throw) {
                throw new $throw($exception, $this->class::class, $type, $property->getName(), $value);
            }
            throw $exception;
        } catch (Exception $exception) {
            if ($throw) {
                throw new $throw($exception, $this->class::class, $type, $property->getName(), $value);
            }
            throw new DataTransferException($this, $type, $property->getName(), $value, $exception->getMessage());
        }
    }

    /**
     * @param string|array $type
     * @param $value
     *
     * @return mixed|object|void
     * @throws \ReflectionException
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException
     */
    public function typed(string|array $type, $value)
    {
        if (!$type || is_array($type)) {
            return $value;
        }

        if (is_string($type)) {
            return $this->resolveValue($type, $value);
        }
    }

    /**
     * @param string $type
     * @param $value
     *
     * @return mixed|object|null
     * @throws \IsakzhanovR\DataTransferObject\Exceptions\TypeErrorException
     * @throws \ReflectionException
     */
    private function resolveValue(string $type, $value)
    {
        if (Types::has($type)) {
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

        throw new TypeErrorException('Undefined type');
    }

    /**
     * @param \ReflectionProperty $property
     *
     * @return string|array
     * @throws \ReflectionException
     */
    private function propertyType(ReflectionProperty $property): string|array
    {
        if ($property->getType() instanceof ReflectionNamedType) {
            return $property->getType()->getName();
        }

        if ($property->getType() instanceof ReflectionUnionType) {
            return array_map(function (ReflectionNamedType $type) {
                return $type->getName();
            }, $property->getType()->getTypes());
        }

        throw new ReflectionException('Not supported property type');
    }
}
