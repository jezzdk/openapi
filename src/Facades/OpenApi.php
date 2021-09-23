<?php

namespace Arkitechdev\OpenApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Arkitechdev\OpenApi\OpenApi openapi($openapi = null)
 * @method static \Arkitechdev\OpenApi\OpenApi title($title = null)
 * @method static \Arkitechdev\OpenApi\OpenApi version($version = null)
 * @method static \Arkitechdev\OpenApi\OpenApi servers($servers = null)
 * @method static \Arkitechdev\OpenApi\OpenApi schemas($schemas = null)
 * @method static \Arkitechdev\OpenApi\OpenApi responses($responses = null)
 * @method static \Arkitechdev\OpenApi\OpenApi parameters($parameters = null)
 * @method static \Arkitechdev\OpenApi\OpenApi examples($examples = null)
 * @method static \Arkitechdev\OpenApi\OpenApi requestBodies($requestBodies = null)
 * @method static \Arkitechdev\OpenApi\OpenApi headers($headers = null)
 * @method static \Arkitechdev\OpenApi\OpenApi securitySchemes($securitySchemes = null)
 * @method static \Arkitechdev\OpenApi\OpenApi links($links = null)
 * @method static \Arkitechdev\OpenApi\OpenApi callbacks($callbacks = null)
 * @method static \Arkitechdev\OpenApi\OpenApi security($security = null)
 * @method static \Arkitechdev\OpenApi\OpenApi addServer($url, $description = null)
 * @method static \Arkitechdev\OpenApi\OpenApi get($uri, ?string $className = null, ?callable $callback = null)
 * @method static \Arkitechdev\OpenApi\OpenApi post($uri, ?string $className = null, ?callable $callback = null)
 * @method static \Arkitechdev\OpenApi\OpenApi patch($uri, ?string $className = null, ?callable $callback = null)
 * @method static \Arkitechdev\OpenApi\OpenApi put($uri, ?string $className = null, ?callable $callback = null)
 * @method static \Arkitechdev\OpenApi\OpenApi delete($uri, ?string $className = null, ?callable $callback = null)
 * @method static array toArray()
 * @method static string toJson()
 * @method static void dd()
 *
 * @see \Arkitechdev\OpenApi\OpenApi
 */
class OpenApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'openapi';
    }
}
