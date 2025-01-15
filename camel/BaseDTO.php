<?php

namespace App\Camel;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\LaravelData\Data;

class BaseDTO extends Data implements Arrayable, \ArrayAccess
{
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }

    public function toArray(): array
    {
        return $this->all();
    }
}
