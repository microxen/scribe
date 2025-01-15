<?php

namespace Knuckles\Camel\Output;

use Knuckles\Camel\BaseDTO;

class Parameter extends BaseDTO
{
    public string $name = '';
    public string $type = 'string';
    public bool $required = false;
    public mixed $example = null;
    public ?string $description = null;
    public ?array $enumValues = [];
    public bool $exampleWasSpecified = false;
    public bool $nullable = false;
    public array $__fields = [];

    public function __construct(mixed $parameters = null)
    {
        if (is_null($parameters)) {
            return;
        }

        if ($parameters instanceof \Knuckles\Camel\Extraction\Parameter) {
            $parameters = [
                'name' => $parameters->name,
                'type' => $parameters->type,
                'required' => $parameters->required,
                'example' => $parameters->example,
                'description' => $parameters->description,
                'enumValues' => $parameters->enumValues,
                'exampleWasSpecified' => $parameters->exampleWasSpecified,
                'nullable' => $parameters->nullable,
            ];
        }

        foreach ($parameters as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    public function toArray(): array
    {
        $array = get_object_vars($this);
        unset($array['exceptKeys']);
        
        if (empty($this->__fields)) {
            unset($array['__fields']);
        }
        
        return $array;
    }
}
