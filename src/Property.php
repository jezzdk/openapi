<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class Property
{
    use GetOrSet;

    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';

    const FORMAT_INT32 = 'int32';
    const FORMAT_INT64 = 'int64';
    const FORMAT_FLOAT = 'float';
    const FORMAT_DOUBLE = 'double';
    const FORMAT_BYTE = 'byte';
    const FORMAT_BINARY = 'binary';
    const FORMAT_DATE = 'date';
    const FORMAT_DATETIME = 'date-time';
    const FORMAT_PASSWORD = 'password';

    protected $name;

    protected $type = 'string';

    protected $format = '';

    protected $required = false;

    protected $example = '';

    protected $default = '';

    protected $enum = [];

    protected $properties = [];

    protected $itemType = '';

    protected $itemExample = '';

    protected $ref = '';

    public function name($name = null): self|string
    {
        return $this->getOrSet('name', $name);
    }

    public function type($type = null): self|string
    {
        return $this->getOrSet('type', $type);
    }

    public function format($format = null): self|string
    {
        return $this->getOrSet('format', $format);
    }

    public function required($required = null): self|bool
    {
        return $this->getOrSet('required', $required);
    }

    public function example($example = null): self|string
    {
        return $this->getOrSet('example', $example);
    }

    public function default($default = null): self|string
    {
        return $this->getOrSet('default', $default);
    }

    public function enum($enum = null): self|array
    {
        return $this->getOrSet('enum', $enum);
    }

    public function itemType($itemType = null): self|string
    {
        return $this->getOrSet('itemType', $itemType);
    }

    public function itemExample($itemExample = null): self|string
    {
        return $this->getOrSet('itemExample', $itemExample);
    }

    public function ref($ref = null): self|string
    {
        return $this->getOrSet('ref', $ref);
    }

    public function addProperty($name, ?string $type = null, callable $callback = null): self
    {
        $property = new static();
        $property->name($name);

        if (!is_null($type)) {
            $property->type($type);
        }

        if (!is_null($callback)) {
            $callback($property);
        }

        $this->properties[$property->name()] = $property;
        return $this;
    }

    public function toArray(): array
    {
        if ($this->type() === 'array') {
            if ($this->ref()) {
                return array_filter([
                    'type' => 'array',
                    'items' => [
                        '$ref' => '#/components/schemas/' . class_basename($this->ref()),
                    ],
                ]);
            } else {
                return array_filter([
                    'type' => 'array',
                    'items' => array_filter([
                        'type' => $this->itemType(),
                        'example' => $this->itemExample(),
                    ]),
                ]);
            }
        }

        if ($this->ref()) {
            return array_filter([
                '$ref' => '#/components/schemas/' . class_basename($this->ref()),
            ]);
        }

        return array_filter([
            'type' => $this->type(),
            'format' => $this->format(),
            'enum' => $this->enum(),
            'example' => $this->example(),
            'default' => $this->default(),
            'properties' => collect($this->properties)->map->toArray()->all(),
            '$ref' => $this->ref(),
        ]);
    }
}
