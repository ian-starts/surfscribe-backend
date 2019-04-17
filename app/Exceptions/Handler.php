<?php

namespace App\Exceptions;

use App\Factories\CamelCaseJsonResponseFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $parentRender = parent::render($request, $exception);

        // if parent returns a JsonResponse
        // for example in case of a ValidationException
        if ($parentRender instanceof JsonResponse) {
            return $parentRender;
        }
        dd($exception);
        return (new CamelCaseJsonResponseFactory())->json(
            [
                'message' => $exception instanceof HttpException || $exception instanceof ModelNotFoundException
                    ? $exception->getMessage()
                    : 'Server Error',
            ],
            $parentRender->status()
        );
    }
}
