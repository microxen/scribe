<?php

namespace Knuckles\Camel;

use Illuminate\Contracts\Support\Arrayable;

class BaseDTO implements Arrayable, \ArrayAccess
{
    public array $custom = [];

    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $property => $value) {
            $this->$property = $value;
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->$offset);
    }
}
