<?php
/*
 ./vendor/bin/openapi   app/Enums app/OpenApi app/Http libs --version 3.1.0 -o  public/openapi.yaml

https://redocly.com/docs/cli/installation
 docker run --rm -v $PWD:/spec redocly/cli lint public/openapi.yaml

https://swagger.io/docs/open-source-tools/swagger-ui/usage/configuration/ see public/swagger-ui/index.html

https://api-platform.com/docs/schema-generator/
*/
namespace App\OpenApi;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    security: [['bearerAuth' => []]]
)]

#[OA\Info(version: "0.3.2",
    description: "Hexbatch core api",
    title: "Hexbatch core",
    termsOfService: "https://hexbatch.com/core-inspector",
    contact: new OA\Contact(name:"Hexbatch",url: "https://hexbatch.com"),
    license: new OA\License(name: "MIT License",identifier: "MIT")
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'This tests on the local host',
)]



class OpenApiStart
{

}
