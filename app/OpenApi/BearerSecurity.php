<?php
/*
./vendor/bin/openapi app --version 3.1.0 -o openapi.yaml
 docker run --rm -v $PWD:/spec redocly/cli lint openapi.yaml
*/
namespace App\OpenApi;
use OpenApi\Attributes as OA;

#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    description: 'The user logs in to get a token, uses that as bearer authentication in the header',
    scheme: 'bearer'
)]
class BearerSecurity
{

}
