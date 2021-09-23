<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class Response
{
    use GetOrSet;

    const TYPE_JSON = 'application/json';
    const TYPE_XML = 'application/xml';
    const TYPE_FORMDATA = 'application/x-www-form-urlencoded';
    const TYPE_PLAIN = 'text/plain';

    protected int $statusCode = 200;

    protected string $description = '';

    protected array $content = [];

    public function statusCode(int $statusCode = null): self|int
    {
        return $this->getOrSet('statusCode', $statusCode);
    }

    public function description(string $description = null): self|string
    {
        return $this->getOrSet('description', $description);
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

    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description(),
            'content' => collect($this->content)->mapWithKeys(function(Schema $schema, $contentType){
                return [
                    $contentType => [
                        'schema' => $schema->toArray()
                    ]
                ];
            })->all()
        ]);
    }
}
