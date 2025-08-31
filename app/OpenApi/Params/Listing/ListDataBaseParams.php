<?php

namespace App\OpenApi\Params\Listing;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\OpenApi\ApiDataBase;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Attributes as OA;


class ListDataBaseParams extends ApiDataBase
{

    #[OA\Property( title: 'Cursor')]
    public ?string $cursor =null;

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        $raw = static::stringFromCollection(collection: $col,param_name: 'cursor');

        try {
            $this->cursor = Crypt::decryptString($raw);
        } catch (EncryptException $e) {
            if ($do_validation)
            {
                throw new HexbatchNotPossibleException(__('msg.invalid_encryption_with_msg',['msg'=>$e->getMessage(),'ref'=>$raw]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::INVALID_CURSOR);
            }

        }


        if ($do_validation)
        {
            $what = Cursor::fromEncoded($this->cursor);
            if (!$what) {
                throw new HexbatchNotPossibleException(__('msg.invalid_cursor',['ref'=>$this->cursor]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::INVALID_CURSOR);
            }
        }

    }

    public  function toArray() : array  {
        $what = parent::toArray();
        if ($this->cursor) {
            $what['cursor'] = $this->cursor;
        }
        return $what;
    }

}
