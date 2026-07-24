<?php

namespace App\Sys\Build;

class MapEntry implements \JsonSerializable
{
    public function __construct(
     public ?string $full_class_name,
     public ?string $name,
     public string $uuid,
    ) {

    }

    public function toArray() :array  {
        return [
            'class'=> $this->full_class_name,
            'name'=> $this->name,
            'uuid'=> $this->uuid,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
