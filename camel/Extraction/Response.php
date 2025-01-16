<?php

namespace Knuckles\Camel\Extraction;

use Knuckles\Camel\BaseDTO;

class Response extends BaseDTO
{
    public int $status;
    public string $content = '';
    public string $description = '';
    public array $headers = [];

    public static function create(BaseDTO|array $data, BaseDTO|array $inheritFrom = []): static
    {
        $dataArray = is_array($data) ? $data : $data->toArray();
        $inheritFromArray = is_array($inheritFrom) ? $inheritFrom : $inheritFrom->toArray();
        $merged = array_merge($inheritFromArray, $dataArray);
        return new static($merged);
    }
}
