<?php

namespace App\Exceptions;

use Throwable;
use Request;
use Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        // \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];

            if ($e instanceof ModelNotFoundException) {
                $response['type'] = 'Resource not found';
                return response()->json($response, 404);
            }

            if ($e instanceof ValidationException) {
                $response['type'] = 'validation';
                $response['errors'] = $e->errors();
                return response()->json($response, 422);
            }

            // Default error response
            $response['type'] = 'Exception';
            $response['file'] = $e->getFile();
            $response['line'] = $e->getLine();
            
            if (config('app.debug')) {
                $response['trace'] = $e->getTrace();
            }

            return response()->json($response, 500);
        }

        return parent::render($request, $e);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => 'Unauthenticated']);
        //return response()->json(['error' => 'Unauthenticated'], 401);

        // return $request->expectsJson()
        // ? response()->json(['message' => 'Unauthenticated.'], 401)
        // : redirect()->guest(route('authentication.index'));
    }
}
