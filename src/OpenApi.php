<?php

namespace Arkitechdev\OpenApi;

use Arkitechdev\OpenApi\Traits\GetOrSet;

class OpenApi
{
    use GetOrSet;

    protected $openapi = '3.0.0';

    protected $title;

    protected $version;

    protected $servers = [];

    protected $paths = [];

    protected $schemas = [];

    protected $responses = [];

    protected $parameters = [];

    protected $examples = [];

    protected $requestBodies = [];

    protected $headers = [];

    protected $securitySchemes = [];

    protected $links = [];

    protected $callbacks = [];

    protected $security = [];

    public function openapi($openapi = null)
    {
        return $this->getOrSet('openapi', $openapi);
    }

    public function title($title = null)
    {
        return $this->getOrSet('title', $title);
    }

    public function version($version = null)
    {
        return $this->getOrSet('version', $version);
    }

    public function servers($servers = null)
    {
        return $this->getOrSet('servers', $servers);
    }

    public function schemas($schemas = null)
    {
        return $this->getOrSet('schemas', $schemas);
    }

    public function responses($responses = null)
    {
        return $this->getOrSet('responses', $responses);
    }

    public function parameters($parameters = null)
    {
        return $this->getOrSet('parameters', $parameters);
    }

    public function examples($examples = null)
    {
        return $this->getOrSet('examples', $examples);
    }

    public function requestBodies($requestBodies = null)
    {
        return $this->getOrSet('requestBodies', $requestBodies);
    }

    public function headers($headers = null)
    {
        return $this->getOrSet('headers', $headers);
    }

    public function securitySchemes($securitySchemes = null)
    {
        return $this->getOrSet('securitySchemes', $securitySchemes);
    }

    public function links($links = null)
    {
        return $this->getOrSet('links', $links);
    }

    public function callbacks($callbacks = null)
    {
        return $this->getOrSet('callbacks', $callbacks);
    }

    public function security($security = null)
    {
        return $this->getOrSet('security', $security);
    }

    public function addServer($url, $description = null)
    {
        $this->servers[] = [
            'url' => $url,
            'description' => $description
        ];
        return $this;
    }

    public function get($uri, ?string $className = null, ?callable $callback = null)
    {
        $this->addPath('get', $uri, $className, $callback);
        return $this;
    }

    public function post($uri, ?string $className = null, ?callable $callback = null)
    {
        $this->addPath('post', $uri, $className, $callback);
        return $this;
    }

    public function patch($uri, ?string $className = null, ?callable $callback = null)
    {
        $this->addPath('patch', $uri, $className, $callback);
        return $this;
    }

    public function put($uri, ?string $className = null, ?callable $callback = null)
    {
        $this->addPath('put', $uri, $className, $callback);
        return $this;
    }

    public function delete($uri, ?string $className = null, ?callable $callback = null)
    {
        $this->addPath('delete', $uri, $className, $callback);
        return $this;
    }

    protected function addPath($method, $uri, ?string $className = null, ?callable $callback = null)
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

    public function toArray()
    {
        return array_filter([
            'openapi' => $this->openapi(),
            'info' => [
                'title' => $this->title(),
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

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function dd()
    {
        dd($this->toArray());
    }
}
