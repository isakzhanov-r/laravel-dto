<?php


namespace IsakzhanovR\DataTransferObject;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use IsakzhanovR\DataTransferObject\Helpers\DocBlock;
use IsakzhanovR\DataTransferObject\Services\ResolveClass;
use IsakzhanovR\DataTransferObject\Traits\StaticDTO;
use ReflectionClass;

abstract class DataTransferObject implements Arrayable
{
    use StaticDTO;

    protected array $only = [];

    protected array $except = [];

    public function __construct(array $args)
    {
        $class = new ResolveClass($this);

        foreach ($class->getProperties() as $property) {

            $value = Arr::get($args, $property->name);
            if ($docblock = $property->getDocComment()) {
                $type = $this->annotationType($docblock);

                if (Str::endsWith($type, '[]')) {
                    $type  = str_replace('[]', '', $type);
                    $value = $this->resolveValues($class, $value, $type);
                }
            }
            $class->setValue($property, $value);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        $class = new ResolveClass(static::class);

        foreach ($class->getProperties() as $property) {
            $data[$property->getName()] = $this->expandObject($property->getValue($this));
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

    protected function annotationType(string $docblock)
    {
        $parameters = DocBlock::parse($docblock);
        $parameter  = DocBlock::get('var', $parameters);

        /**
         * @var $type
         */
        extract($parameter);

        return trim($type);
    }

    protected function resolveValues(ResolveClass $class, $args, $type)
    {
        $values = [];

        foreach ($args as $value) {
            array_push($values, $class->typed($type, $value));
        }

        return $values;
    }

    protected function expandObject($value)
    {
        if (is_object($value) && (new ReflectionClass(get_class($value)))->implementsInterface(Arrayable::class)) {
            $value = $value->toArray();
        }
        if (is_array($value)) {
            return array_map(fn($item) => $this->expandObject($item), $value);
        }

        return $value;
    }
}
