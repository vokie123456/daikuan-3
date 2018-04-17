<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return starts_with(Route::currentRouteName(), 'admin')
            ? redirect(route('admin.login.show'))
            : parent::unauthenticated($request, $exception);
    }

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
    public function render($request, Exception $exception)
    {
        $error = $exception->getMessage();
        if($error = 'Unauthenticated.' && method_exists($exception, 'guards') && in_array('api', $exception->guards())) {
            return response([
                'error' => 'token认证失败',
                'errno' => 403,
            ], 403);
        }

        return parent::render($request, $exception);
    }
}
