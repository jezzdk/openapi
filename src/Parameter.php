<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class Parameter
{
    use GetOrSet;

    const IN_PATH = 'path';
    const IN_QUERY = 'query';

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

    protected $in = 'query';

    protected $required = false;

    protected $type = 'string';

    protected $format = '';

    protected $example = '';

    protected $default = '';

    protected $enum = [];

    protected $itemType = '';

    protected $itemExample = '';

    public function name($name = null): self|string
    {
        return $this->getOrSet('name', $name);
    }

    public function in($in = null): self|string
    {
        return $this->getOrSet('in', $in);
    }

    public function required($required = null): self|bool
    {
        return $this->getOrSet('required', $required);
    }

    public function type($type = null): self|string
    {
        return $this->getOrSet('type', $type);
    }

    public function format($format = null): self|string
    {
        return $this->getOrSet('format', $format);
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

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name(),
            'in' => $this->in(),
            'required' => $this->required(),
            'schema' => array_filter([
                'type' => $this->type(),
                'example' => $this->example(),
                'default' => $this->default(),
                'enum' => $this->enum(),
                'items' => array_filter([
                    'type' => $this->itemType(),
                    'example' => $this->itemExample(),
                ]),
            ])
        ]);
    }
}
