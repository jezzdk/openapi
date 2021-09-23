<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class OpenApi
{
    use GetOrSet;

    protected string $openapi = '3.0.0';

    protected string $title = '';

    protected string $description = '';

    protected string $version = '';

    protected array $servers = [];

    protected array $paths = [];

    protected array $schemas = [];

    protected array $responses = [];

    protected array $parameters = [];

    protected array $examples = [];

    protected array $requestBodies = [];

    protected array $headers = [];

    protected array $securitySchemes = [];

    protected array $links = [];

    protected array $callbacks = [];

    protected array $security = [];

    public function openapi(string $openapi = null): self|string
    {
        return $this->getOrSet('openapi', $openapi);
    }

    public function title(string $title = null): self|string
    {
        return $this->getOrSet('title', $title);
    }

    public function description(string $description = null): self|string
    {
        return $this->getOrSet('description', $description);
    }

    public function version(string $version = null): self|string
    {
        return $this->getOrSet('version', $version);
    }

    public function servers(array $servers = null): self|array
    {
        return $this->getOrSet('servers', $servers);
    }

    public function schemas(array $schemas = null): self|array
    {
        return $this->getOrSet('schemas', $schemas);
    }

    public function responses(array $responses = null): self|array
    {
        return $this->getOrSet('responses', $responses);
    }

    public function parameters(array $parameters = null): self|array
    {
        return $this->getOrSet('parameters', $parameters);
    }

    public function examples(array $examples = null): self|array
    {
        return $this->getOrSet('examples', $examples);
    }

    public function requestBodies(array $requestBodies = null): self|array
    {
        return $this->getOrSet('requestBodies', $requestBodies);
    }

    public function headers(array $headers = null): self|array
    {
        return $this->getOrSet('headers', $headers);
    }

    public function securitySchemes(array $securitySchemes = null): self|array
    {
        return $this->getOrSet('securitySchemes', $securitySchemes);
    }

    public function links(array $links = null): self|array
    {
        return $this->getOrSet('links', $links);
    }

    public function callbacks(array $callbacks = null): self|array
    {
        return $this->getOrSet('callbacks', $callbacks);
    }

    public function security(array $security = null): self|array
    {
        return $this->getOrSet('security', $security);
    }

    public function addServer(string $url, string $description = null): self
    {
        $this->servers[] = [
            'url' => $url,
            'description' => $description
        ];
        return $this;
    }

    public function get(string $uri, string $className = null, callable $callback = null): self
    {
        $this->addPath('get', $uri, $className, $callback);
        return $this;
    }

    public function post(string $uri, string $className = null, callable $callback = null): self
    {
        $this->addPath('post', $uri, $className, $callback);
        return $this;
    }

    public function patch(string $uri, string $className = null, callable $callback = null): self
    {
        $this->addPath('patch', $uri, $className, $callback);
        return $this;
    }

    public function put(string $uri, string $className = null, callable $callback = null): self
    {
        $this->addPath('put', $uri, $className, $callback);
        return $this;
    }

    public function delete(string $uri, string $className = null, callable $callback = null): self
    {
        $this->addPath('delete', $uri, $className, $callback);
        return $this;
    }

    protected function addPath(string $method, string $uri, string $className = null, callable $callback = null): self
    {
        if (isset($this->paths[$uri])) {
            $path = $this->paths[$uri];
        } else {
            $path = new Path();
            $path->path($uri);
        }

        $request = $className ? new $className : new Request;

        $path->addRequest($request->method($method));

        if ($callback) {
            $callback($request);
        }

        $this->paths[$path->path()] = $path;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'openapi' => $this->openapi(),
            'info' => [
                'title' => $this->title(),
                'description' => $this->description(),
                'version' => $this->version()
            ],
            'servers' => $this->servers(),
            'paths' => collect($this->paths)->map(function(Path $path){
                return $path->toArray();
            })->toArray(),
            'components' => array_filter([
                'schemas' => collect($this->schemas)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'responses' => collect($this->responses)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'parameters' => collect($this->parameters)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'examples' => collect($this->examples)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'requestBodies' => collect($this->requestBodies)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'headers' => collect($this->headers)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'securitySchemes' => collect($this->securitySchemes)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'links' => collect($this->links)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
                'callbacks' => collect($this->callbacks)->mapWithKeys(function ($class) {
                    return [class_basename($class) => (new $class)->toArray()];
                })->all(),
            ]),
            'security' => $this->security()
        ]);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function dd(): void
    {
        dd($this->toArray());
    }
}
