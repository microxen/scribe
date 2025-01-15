<?php

namespace App\Camel;

use Spatie\LaravelData\DataCollection;

/**
 * @template T of \Spatie\LaravelData\Data
 * @extends DataCollection<T>
 */
class BaseDTOCollection extends DataCollection
{
    public function offsetGet(mixed $offset): mixed
    {
        return is_numeric($offset) || is_string($offset) 
            ? ($this->items[$offset] ?? null) 
            : null;
    }
}
