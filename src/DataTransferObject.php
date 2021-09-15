<?php


namespace IsakzhanovR\DataTransferObjects;


use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use IsakzhanovR\DataTransferObjects\Exceptions\DataTransferException;
use IsakzhanovR\DataTransferObjects\Exceptions\TypeErrorException;
use IsakzhanovR\DataTransferObjects\Exceptions\UnknownTypePropertyException;
use IsakzhanovR\DataTransferObjects\Helpers\Types;
use IsakzhanovR\DataTransferObjects\Traits\StaticDTO;
use IsakzhanovR\ValueObject\ValueObject;
use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject implements Arrayable
{
    use StaticDTO;

    protected array $only = [];

    protected array $except = [];

    public function __construct(array $args)
    {
        $reflection = new ReflectionClass($this);

        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->setValue($reflection->getProperty($key), $value);
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $this->properties($class);

        foreach ($properties as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }

        if (count($this->only)) {
            $data = Arr::only($data, $this->only);
        } else {
            $data = Arr::except($data, $this->except);
        }

        return $data;
    }

    public function only(string ...$keys): self
    {
        $clone = clone $this;

        $clone->only = [...$this->only, ...$keys];

        return $clone;
    }

    public function except(string ...$keys)
    {
        $clone = clone $this;

        $clone->except = [...$this->except, ...$keys];

        return $clone;
    }

    /**
     * @param \ReflectionProperty $property
     * @param $value
     *
     * @return object
     * @throws \IsakzhanovR\DataTransferObjects\Exceptions\UnknownTypePropertyException
     */
    private function typed(ReflectionProperty $property, $value)
    {
        if (!$property->hasType() || Types::has($property->getType()->getName())) {
            return $value;
        }

        if (class_exists($class = $property->getType()->getName())) {
            $class = new ReflectionClass($class);

            if ($class->isSubclassOf(ValueObject::class)) {
                return $class->newInstance($value, $property->getName());
            }

            if ($class->isSubclassOf(DataTransferObject::class)) {
                $value = $this->valueToDTO($value);

                return $class->newInstance($value);
            }
        }

        throw new UnknownTypePropertyException($this, $property->getType()->getName(), $property->getName(), ValueObject::class, DataTransferObject::class);

    }

    private function valueToDTO($value)
    {
        if (is_object($value)) {
            return get_object_vars($value);
        } elseif (is_array($value)) {
            return $value;
        }

        throw new TypeErrorException('Argument for DTO mast be array');
    }

    private function properties(ReflectionClass $reflection)
    {
        return array_filter(
            $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
            fn(ReflectionProperty $property) => !$property->isStatic()
        );
    }

    private function setValue(ReflectionProperty $property, $value)
    {
        try {
            $property->setAccessible(true);
            $property->setValue($this, $this->typed($property, $value));
        } catch (UnknownTypePropertyException $exception) {
            throw $exception;
        } catch (ValidationException $exception) {
            $messages = Arr::flatten(array_values($exception->errors()));
            throw new DataTransferException($this, $property->getType()->getName(), $property->getName(), $value, $messages);
        } catch (Exception $exception) {
            throw new DataTransferException($this, $property->getType()->getName(), $property->getName(), $value, $exception->getMessage());
        }
    }
}
