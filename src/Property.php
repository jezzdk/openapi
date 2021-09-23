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

    protected string $name;

    protected string $type = 'string';

    protected string $format = '';

    protected bool $required = false;

    protected string $example = '';

    protected string $default = '';

    protected array $enum = [];

    protected array $properties = [];

    protected string $itemType = '';

    protected string $itemExample = '';

    protected string $ref = '';

    public function name(string $name = null): self|string
    {
        return $this->getOrSet('name', $name);
    }

    public function type(string $type = null): self|string
    {
        return $this->getOrSet('type', $type);
    }

    public function format(string $format = null): self|string
    {
        return $this->getOrSet('format', $format);
    }

    public function required(bool $required = null): self|bool
    {
        return $this->getOrSet('required', $required);
    }

    public function example(string $example = null): self|string
    {
        return $this->getOrSet('example', $example);
    }

    public function default(string $default = null): self|string
    {
        return $this->getOrSet('default', $default);
    }

    public function enum(array $enum = null): self|array
    {
        return $this->getOrSet('enum', $enum);
    }

    public function itemType(string $itemType = null): self|string
    {
        return $this->getOrSet('itemType', $itemType);
    }

    public function itemExample(string $itemExample = null): self|string
    {
        return $this->getOrSet('itemExample', $itemExample);
    }

    public function ref(string $ref = null): self|string
    {
        return $this->getOrSet('ref', $ref);
    }

    public function addProperty(string $name, string $type = null, callable $callback = null): self
    {
        if (class_exists($name)) {
            $property = new $name;
        } else {
            $property = new static();
            $property->name($name);
        }

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
