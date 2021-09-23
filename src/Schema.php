<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class Schema
{
    use GetOrSet;

    const TYPE_OBJECT = 'object';

    protected string $type = 'object';

    protected string $contentType = 'application/json';

    protected array $properties = [];

    public function type(string $type = null): self|string
    {
        return $this->getOrSet('type', $type);
    }

    public function contentType(string $contentType = null): self|string
    {
        return $this->getOrSet('contentType', $contentType);
    }

    public function addProperty(string $name, string $type = null, callable $callback = null): self
    {
        if (class_exists($name)) {
            $property = $name;
        } else {
            $property = new Property();
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
        return array_filter([
            'required' => collect($this->properties)->filter(function(Property $property) {
                return $property->required();
            })->map->name()->values()->toArray(),
            'properties' => collect($this->properties)->map->toArray()->all(),
            'type' => $this->type(),
        ]);
    }
}
