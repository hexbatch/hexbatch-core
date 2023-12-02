<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->wantsJson() && (  $e instanceof HexbatchCoreException))
        {
            // Default response of 400
            $status = $e->getCode();
            if (empty($status)) {$status = 400;}
            $response = [
                'status' => $status,
                'message'=> $e->getMessage(),
                'code' => $e->getRefCode(),
                'more_info' => $e->getRefCodeUrl(),
                'errors'=> [],
            ];
            $other = $e->getPrevious();
            while($other) {
                $response['errors'][] = $other->getMessage();
                $other = $other->getPrevious();
            }
            if (empty($response['errors'])) {
                unset($response['errors']);
            }


            // Return a JSON response with the response array and status code
            return response()->json($response, $status);
        }
        return parent::render($request, $e);
    }
}
