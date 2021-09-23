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

    protected $method;

    protected $summary;

    protected $description;

    protected $parameters = [];

    protected $content = [];

    protected $responses = [];

    protected $tags = [];

    public function method($method = null)
    {
        return $this->getOrSet('method', $method);
    }

    public function summary($summary = null)
    {
        return $this->getOrSet('summary', $summary);
    }

    public function description($description = null)
    {
        return $this->getOrSet('description', $description);
    }

    public function tags($tags = null)
    {
        return $this->getOrSet('tags', $tags);
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function addParameter($name, ?string $type = null, callable $callback = null)
    {
        $parameter = new Parameter();
        $parameter->name($name);

        if (!is_null($type)) {
            $parameter->type($type);
        }

        if (!is_null($callback)) {
            $callback($parameter);
        }

        $this->parameters[$name] = $parameter;
        return $this;
    }

    public function addContentType(string $contentType, ?string $type = null, callable $callback = null): self
    {
        $schema = new Schema();
        $schema->contentType($contentType);

        if (!is_null($type)) {
            $schema->type($type);
        }

        if (!is_null($callback)) {
            $callback($schema);
        }

        $this->content[$contentType] = $schema;
        return $this;
    }

    public function addResponse(int $statusCode, callable $callback = null)
    {
        $response = new Response();
        $response->statusCode($statusCode);

        if (!is_null($callback)) {
            $callback($response);
        }

        $this->responses[$response->statusCode()] = $response;
        return $this;
    }

    public function toArray()
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
