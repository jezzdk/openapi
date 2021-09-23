# OpenApi docs generator

## Requirements

* PHP 7.4 | PHP 8.0
* Laravel 8

## Installation

To install from composer:

`composer require arkitechdev/openapi`

You can generate the docs by running the following artisan command:

`php artisan openapi:generate`

The command will create a config file for you if you wish, but you can also publish it with:

`php artisan vendor:publish`

## Configuration

The config file looks like this:

```php
<?php

return [

    /**
     * The default path to the file containing the docs.
     */
    'docs_path' => base_path('docs.php'),

    /**
     * The path to the file where the generated documentation will be stored.
     * The generator supports both .json and .yaml files.
     */
    'output_path' => base_path('docs.json'),

];

```

The `docs_path` contains the path to your doc specification file.

The `output_path` is where you want the generated API docs to be stored. If the filename has a .json extension, it will be created as a JSON file. If the filename has a .yaml extension, it will be created as a YAML file. It's magic.

## Methods

Most of the methods in the classes are somewhat self-explanatory and provides that sweet autocompletion. There is a few things that deserves to be mentioned here though.

**Getters and setters**

Many of the methods either sets a property value and returns itself, or returns the value of the property in question, if the given parameter is not set or is null.

Examples:

```php
OpenApi::title('A title')
```

This sets the `title` property to `A title` and returns the OpenApi object. This allows for chaining, such as:

```php
OpenApi::title('A title')->description('Whatever')->version('1.0.0')
```

Very handy. Meanwhile, if no parameter is set, then the value is returned:

```php
$title = OpenApi::title() // $title = 'A title'
```

**Callbacks**

Some method supply callbacks. When setting child objects, such as parameters or properties, the method supplies an optional callback for further definition of the object. Depending on the type you are adding, the first and only parameter of the callback is the added object.

Consider the following example:

```php
Request::method('get')->addParameter('page', null, function (Parameter $parameter) {
    $parameter->in(Parameter::IN_QUERY)->type(Parameter::TYPE_INTEGER)->required(true);
});
```

Since we add a parameter, the parameter of the callback is a `Parameter` object. What a great example. Here we add a `query` param called 'page' of the type `integer` and sets it as `required`.

You can structure your whole API docs like this, although I wouldn't recommend it. Consider this rather verbose example.

```php
OpenApi::title('API example')
    ->version('1.0.0')
    ->addPath('patch', '/posts/{id}', null, function (Request $request) {
        $request->tags([
                'Posts'
            ])
            ->addParameter('id', null, function (Parameter $parameter) {
                $parameter->in(Parameter::IN_PATH)
                    ->type(Parameter::TYPE_INTEGER)
                    ->required(true);
            })
            ->addContentType(Request::TYPE_JSON, null, function (Schema $schema) {
                $schema->type(Schema::TYPE_OBJECT)
                    ->addProperty('title', null, function (Property $property) {
                        $property->type(Property::TYPE_STRING)
                            ->required(true);
                    })
                    ->addProperty('subject', null, function (Property $property) {
                        $property->type(Property::TYPE_STRING)
                            ->format(Property::FORMAT_DATETIME);
                    })
                    ->addProperty('content', null, function (Property $property) {
                        $property->type(Property::TYPE_STRING);
                    });
            })
            ->addResponse(200, function (Response $response) {
                $response->addContentType(Response::TYPE_JSON, null, function (Schema $schema) {
                    $schema->type(Schema::TYPE_OBJECT)
                        ->addProperty('title', null, function (Property $property) {
                            $property->type(Property::TYPE_STRING);
                        })
                        ->addProperty('subject', null, function (Property $property) {
                            $property->type(Property::TYPE_STRING)
                                ->format(Property::FORMAT_DATETIME);
                        })
                        ->addProperty('content', null, function (Property $property) {
                            $property->type(Property::TYPE_STRING);
                        });
                });
            });
    });
```

It can get unmanageable real quick, which is why this library supports splitting everything into classes. I'll go over this in the next chapter.

**Method defaults**

If you just want to create parameters and properties without all the fuzz, then I've got your back. Notice those `null` parameters in the example above? That's where the type goes.

Examples:

```php
$request->addParameter('title', Parameter::TYPE_STRING);
$request->addParameter('page', Parameter::TYPE_INTEGER);
// or if you hate constants for some reason:
$request->addParameter('title', 'string');
$request->addParameter('page', 'integer');
```

Actually, `string` is the default for both parameters and properties, so it can be boiled down even further for that type:

```php
$request->addParameter('title');
$schema->addProperty('content');
```

You get the idea.

Other types have other defaults. Feel free to code dive.

## Class based API docs

Now, the big example above is only a single endpoint. Imagine having 100 endpoints. That would make it harder to maintain. Ain't nobody got time for that!

As such, if the first parameter of the `add...()` methods are a class, then you can hide everything inside there. The syntax looks likes this:

```php
$property->addProperty(Property::class);
$request->addContentType(Schema::class);
$request->addParameter(Parameter::class);
$request->addResponse(Response::class);
$response->addContentType(Schema::class);
$schema->addProperty(Property::class);
```

You can create and use any class you want as long as it extends the relevant class from the list above. Inside that class you can then use the class properties and inherited methods to define it.

The requests works great that way. You can do something like this in your docs file:

*docs.php:*
```php
OpenApi::get('/projects', ProjectIndex::class);
```

And the custom class would then look something like this:

*ProjectIndex.php:*
```php
<?php

namespace App\Requests;

use App\Schemas\PaginationLinks as PaginationLinksSchema;
use App\Schemas\PaginationMeta as PaginationMetaSchema;
use App\Schemas\Project as ProjectSchema;
use Arkitechdev\OpenApi\Parameter;
use Arkitechdev\OpenApi\Property;
use Arkitechdev\OpenApi\Request;
use Arkitechdev\OpenApi\Response;
use Arkitechdev\OpenApi\Schema;

class ProjectIndex extends Request
{
    protected string $method = 'get';

    protected string $description = 'The description goes here';

    protected string $summary = 'The summary goes here';

    protected array $tags = [
        'Projects'
    ];

    public function __construct()
    {
        $this->addParameter('searchQuery', null, function (Parameter $parameter) {
            $parameter->in(Parameter::IN_QUERY)->required(false);
        });

        $this->addParameter('page', Parameter::TYPE_INTEGER, function (Parameter $parameter) {
            $parameter->in(Parameter::IN_QUERY)->required(false)->example(1)->default(1);
        });

        $this->addParameter('per_page', Parameter::TYPE_INTEGER, function (Parameter $parameter) {
            $parameter->in(Parameter::IN_QUERY)->required(false)->example(15)->default(15);
        });

        $this->addResponse(200, function (Response $response) {
            $response->description('Returns the list of projects')
                ->addContentType(Response::TYPE_JSON, null, function (Schema $schema) {
                    $schema->addProperty('data', Property::TYPE_ARRAY, function (Property $property) {
                        $property->ref(ProjectSchema::class);
                    })->addProperty('links', null, function (Property $property) {
                        $property->ref(PaginationLinksSchema::class);
                    })->addProperty('meta', null, function (Property $property) {
                        $property->ref(PaginationMetaSchema::class);
                    });
                });
        });
    }
}
```

Straight out of Laraville. In this example we have not created Parameter, Response or Property classes, but you could totally go even further. You can do that as many levels deep as you want.

We do have created Schema classes though. That way we can use them as references (more on that in a moment).

I know you're curious, so here it is, the `Project` schema:

*Project.php:*
```php
<?php

namespace App\Schemas;

use Arkitechdev\OpenApi\Property;
use Arkitechdev\OpenApi\Schema;

class Project extends Schema
{
    protected string $type = 'object';

    public function __construct()
    {
        $this->addProperty('id', Property::TYPE_INTEGER, function (Property $property) {
            $property->example(1)->required(true);
        });

        $this->addProperty('name', null, function (Property $property) {
            $property->example('My cool project')->required(true);
        });

        $this->addProperty('description', null, function (Property $property) {
            $property->example('Nice long description')->required(true);
        });

        $this->addProperty('created', null, function (Property $property) {
            $property->format(Property::FORMAT_DATETIME);
        });
    }
}
```

Note: You can still use the callbacks, if you need to overwrite something inside your custom classes. Very nice!

## Refs

If you plan to use references to schemas in properties (`$property->ref(Project::class)`), you have to make sure to add them to the schemas array on the OpenApi object too:

*docs.php:*
```php
OpenApi::schemas([
    Project::class,
]);
```

## Validator

There's a cool [online editor with builtin validator](https://editor.swagger.io/) that you can use.

When you have generated your JSON/YAML file, you can paste its contents into the editor and check that it looks fine and dandy.
