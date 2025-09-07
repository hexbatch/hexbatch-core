<?php

namespace App\OpenApi;


use App\Exceptions\HexbatchCoreException;
use App\Exceptions\RefCodes;
use App\OpenApi\Results\Users\MeResponse;
use Carbon\Carbon;
use Hexbatch\Things\Models\ThingCallback;
use Illuminate\Support\Facades\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as CodeOf;
use Throwable;

/**
 * All errors will have this format
 */
#[OA\Schema(schema: 'ErrorResponse',title: "Error")]

class ErrorResponse extends ApiCollectionBase
{
    #[OA\Property(  title: 'Type of error ', description: 'Help with the error', format: 'url')]
    protected ?string $type = null;


    #[OA\Property(  title: 'Error message ',description: 'Describes the problem', example: 'You have not been assigned to this project')]
    protected string $message;

    #[OA\Property(  title: 'Path',description: 'The path or route this happened on', )]
    protected ?string $path;

    #[OA\Property(  title: 'Error type code ',description: 'App specific code', example: 628)]
    protected null|string|int $instance = null;

    #[OA\Property(  title: 'Status of the error ',description: 'This is normally a http code', example: 400)]
    protected int $status;

    #[OA\Property( title: 'Error time',description: "Iso 8601 datetime string for when this happened", format: 'datetime',example: "2025-01-25T15:00:59-06:00")]
    public ?string $timestamp = null;

    #[OA\Property( title:"Errors",description: 'Additional errors',items: new OA\Items(type: 'string'),nullable: true)]
    /** @var string[] $other_errors */
    protected array $other_errors = [];

    public static function fromException(Throwable $e): ErrorResponse
    {
        $ret = new ErrorResponse();

        if ($e instanceof HexbatchCoreException) {
            // Default response of 400
            $ret->status = $e->getCode();
            if (empty($ret->status)) {
                $ret->status = 400;
            }
            if ($ret->status < 100 || $ret->status >= 600) {
                $ret->status = 400;
            }

            $ret->type = $e->getRefCodeUrl();
            $ret->message = $e->getMessage();
            $ret->instance = $e->getRefCode();


            $other = $e->getPrevious();
            while ($other) {
                $ret->other_errors[] = $other->getMessage();
                $other = $other->getPrevious();
            }
        }

        else if ($e instanceof \Illuminate\Validation\ValidationException)
        {

            $ret->status = $e->status;
            $ret->message = $e->getMessage();
            $ret->instance = RefCodes::VALIDATION;

            $other = $e->getPrevious();
            while ($other) {
                $ret->other_errors[] = $other->getMessage();
                $other = $other->getPrevious();
            }

        }
        else if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $ret->status = ($e->getCode()?: CodeOf::HTTP_NOT_FOUND);
            $ret->message = $e->getMessage();
        }
        else if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $ret->status = ($e->getCode()?: CodeOf::HTTP_NOT_FOUND);
            $ret->message = $e->getMessage();
        }
        else {
            $ret->status = CodeOf::HTTP_BAD_REQUEST;
            $ret->instance = $e->getCode()?:null;
            $ret->message = $e->getMessage();

            $other = $e->getPrevious();
            while ($other) {
                $ret->other_errors[] = $other->getMessage();
                $other = $other->getPrevious();
            }
        }
        $ret->path = Request::fullUrl();
        $ret->timestamp = Carbon::now()->timezone('UTC')->toIso8601String();
        return $ret;
    }

    public function getHttpCode() : int {
        return $this->status;
    }

    public static function handlesThisException(Throwable $e) :bool  {
        if($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) return true;
        if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) return true;
        if($e instanceof \Illuminate\Validation\ValidationException) return true;
        if($e instanceof HexbatchCoreException) return true;
        return false;
    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['type'] = $this->type;
        $ret['instance'] = $this->instance;
        $ret['message'] = $this->message;
        $ret['path'] = $this->path;
        $ret['status'] = $this->status;
        $ret['timestamp'] = $this->timestamp;

        if (count($this->other_errors) ) {
            $ret['other_errors'] = $this->other_errors;
        }
        return $ret;
    }

    public static function fromCallback(ThingCallback $callback) : ?MeResponse {
        return null;
        //todo fill in error from callback
    }
}


