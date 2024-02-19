<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        if ($request->wantsJson() )
        {
            if ($e instanceof HexbatchCoreException) {
                // Default response of 400
                $status = $e->getCode();
                if (empty($status)) {
                    $status = 400;
                }
                $response = [
                    'type' => $e->getRefCodeUrl(),
                    'title' => $e->getMessage(),
                    'instance' => $e->getRefCode(),
                    'status' => $status,
                    'errors' => [],
                ];
                $other = $e->getPrevious();
                while ($other) {
                    $response['errors'][get_class($other)] = $other->getMessage();
                    $other = $other->getPrevious();
                }
                if (empty($response['errors'])) {
                    unset($response['errors']);
                }


                // Return a JSON response with the response array and status code
                return response()->json($response, $status);
            }
        }
        if ($e instanceof \Illuminate\Validation\ValidationException) {

            $status = $e->status;

            $response = [
                'title' => $e->getMessage(),
                'instance' => RefCodes::VALIDATION,
                'status' => $status,
                'errors' => $e->errors(),
            ];
            $other = $e->getPrevious();
            while ($other) {
                $response['errors'][] = $other->getMessage();
                $other = $other->getPrevious();
            }
            if (empty($response['errors'])) {
                unset($response['errors']);
            }


            // Return a JSON response with the response array and status code
            return response()->json($response, $status);
        }
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['status'=>($e->getCode()?: 404),'message'=> $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json(['status'=>($e->getCode()?: 404),'message'=> $e->getMessage()], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
        return parent::render($request, $e);
    }
}
