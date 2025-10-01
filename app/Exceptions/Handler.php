<?php

namespace App\Exceptions;

use Exception;
use Request;
use Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Validation\ValidationException;
use \Illuminate\Auth\AuthenticationException;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
         // This will replace our 404 response with
        // a JSON response.

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException && $request->wantsJson())
        {
            return response()->json([
                'status' => 'error',
                'type' => 'Resource not found',
                'error' => $e->errors()
                ]);

        }
        /*
        return response()->json([
            'status' => $request,
            'error' => $exception

        ]);
        */


        if ($e instanceof \Illuminate\Database\Eloquent\ValidationException && $request->wantsJson() ) {

            return response()->json([
                'status' => 'error',
                'type' => 'validation',
                'error' => $e->errors(),
                'error_message' => $e->getMessage()
            ]);

        }

        if ($request->wantsJson() ) {


            return response()->json([
                'status' => 'error',
                'error' => $e,
                'error_message' => $e->getMessage()
            ]);
            return response()->json([
                'status' => 'error',
                'error' => $e->errors(),
            ]);

        }
        return parent::render($request, $e);
        // return parent::render($request, $exception);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
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
