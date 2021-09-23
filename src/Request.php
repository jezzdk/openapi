<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class Request
{
    use GetOrSet;

    const TYPE_JSON = 'application/json';
    const TYPE_XML = 'application/xml';
    const TYPE_FORMDATA = 'application/x-www-form-urlencoded';
    const TYPE_PLAIN = 'text/plain';

    protected string $method = '';

    protected string $summary = '';

    protected string $description = '';

    protected array $parameters = [];

    protected array $content = [];

    protected array $responses = [];

    protected array $tags = [];

    public function method(string $method = null): self|string
    {
        return $this->getOrSet('method', $method);
    }

    public function summary(string $summary = null): self|string
    {
        return $this->getOrSet('summary', $summary);
    }

    public function description(string $description = null): self|string
    {
        return $this->getOrSet('description', $description);
    }

    public function tags(array $tags = null): self|array
    {
        return $this->getOrSet('tags', $tags);
    }

    public function addTag(array $tag): self
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function addParameter(string $name, string $type = null, callable $callback = null): self
    {
        if (class_exists($name)) {
            $parameter = new $name;
        } else {
            $parameter = new Parameter();
            $parameter->name($name);
        }

        if (!is_null($type)) {
            $parameter->type($type);
        }

        if (!is_null($callback)) {
            $callback($parameter);
        }

        $this->parameters[$name] = $parameter;
        return $this;
    }

    public function addContentType(string $contentType, string $type = null, callable $callback = null): self
    {
        if (class_exists($contentType)) {
            $schema = new $contentType;
        } else {
            $schema = new Schema();
            $schema->contentType($contentType);
        }

        if (!is_null($type)) {
            $schema->type($type);
        }

        if (!is_null($callback)) {
            $callback($schema);
        }

        $this->content[$contentType] = $schema;
        return $this;
    }

    public function addResponse(int|string $statusCode, callable $callback = null): self
    {
        if (is_string($statusCode) && class_exists($statusCode)) {
            $response = new $statusCode;
        } else {
            $response = new Response();
            $response->statusCode($statusCode);
        }

        if (!is_null($callback)) {
            $callback($response);
        }

        $this->responses[$response->statusCode()] = $response;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'tags' => $this->tags(),
            'summary' => $this->summary(),
            'description' => $this->description(),
            'parameters' => collect($this->parameters)->map->toArray()->values()->toArray(),
            'requestBody' => array_filter([
                'content' => collect($this->content)->mapWithKeys(function (Schema $schema, $contentType) {
                    return [
                        $contentType => [
                            'schema' => $schema->toArray()
                        ]
                    ];
                })->all(),
            ]),
            'responses' => collect($this->responses)->map->toArray()->all(),
        ]);
    }
}
