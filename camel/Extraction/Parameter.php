<?php

namespace Knuckles\Camel\Extraction;

use Knuckles\Camel\BaseDTO;

class Parameter extends BaseDTO
{
    public string $name;
    public string $description = '';
    public bool $required = false;
    public mixed $example = null;
    public ?string $type = null;
    public array $enumValues = [];
    public array $custom = [];
    public bool $exampleWasSpecified = false;
    public bool $nullable = false;

    public static function create(BaseDTO|array $data, BaseDTO|array $inheritFrom = []): static
    {
        $dataArray = is_array($data) ? $data : $data->toArray();
        $inheritFromArray = is_array($inheritFrom) ? $inheritFrom : $inheritFrom->toArray();
        $merged = array_merge($inheritFromArray, $dataArray);
        return new static($merged);
    }
}
