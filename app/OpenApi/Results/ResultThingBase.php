<?php

namespace App\OpenApi\Results;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\OpenApi\ApiThingBase;
use Hexbatch\Things\Models\Thing;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Attributes as OA;


class ResultThingBase extends ApiThingBase
{

    #[OA\Property( title: 'Cursor')]
    public ?string $cursor =null;

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    protected function setCursor($list) {
        $this->cursor = null;
        if ($list instanceof AbstractCursorPaginator) {
            $the_cursor = $list->cursor();
            if ($the_cursor) {
                $cursor_string = $the_cursor->encode();
                $this->cursor = Crypt::encryptString($cursor_string);
            }
        }

    }

    public function __construct($list,?Thing $thing = null)
    {
        parent::__construct(thing: $thing);
        $this->setCursor($list);
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
