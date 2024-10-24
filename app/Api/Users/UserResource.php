<?php

namespace App\Api\Users;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'User')]
class UserResource
{
    #[OA\Property(title: 'Unique reference')]
    public string $uuid;

    #[OA\Property(title: 'User name')]
    public string $username;

    #[OA\Property(title: 'User created')]
    public string $created_at;
}
