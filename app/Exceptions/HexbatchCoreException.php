<?php

namespace App\Exceptions;

use Throwable;

class HexbatchCoreException extends \RuntimeException {


    protected ?int $ref_code = null;
    public function __construct(string $message,int $http_code = 0,string|null|int $ref_code = null,?Throwable $prev = null)
    {
        parent::__construct($message,$http_code,$prev);
        $this->ref_code = $ref_code;
    }

    public function getRefCode(): int|string|null
    {
        return $this->ref_code;
    }

    public function getRefCodeUrl(): ?string
    {
        return (RefCodes::URLS[$this->ref_code]??null);
    }


}
