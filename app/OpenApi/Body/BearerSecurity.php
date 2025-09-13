<?php

namespace App\OpenApi\Body;
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
