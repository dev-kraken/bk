<?php

use App\Helpers\ResponseHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->alias([
                'abilities' => CheckAbilities::class,
                'ability' => CheckForAnyAbility::class,
            ])
            ->statefulApi();
        //->api('throttle:profile');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            // Handle Sanctum-specific exceptions
            if ($e instanceof MissingAbilityException) {
                return ResponseHandler::error('Token does not have the necessary abilities to access this resource.',
                    403);
            }

            if ($e instanceof AuthenticationException) {
                return ResponseHandler::error('Unauthenticated', 401);
            }

            // Handle other specific exceptions
            if ($e instanceof AccessDeniedHttpException) {
                return ResponseHandler::error('You do not have the necessary abilities to access this resource.', 403);
            }

            if ($e instanceof ValidationException) {
                return ResponseHandler::error('Validation Error', 422, $e->errors());
            }

            if ($e instanceof AuthorizationException) {
                return ResponseHandler::error('Forbidden', 403);
            }

            if ($e instanceof NotFoundHttpException) {
                return ResponseHandler::error('Not Found', 404);
            }

            if ($e instanceof ModelNotFoundException) {
                return ResponseHandler::error('Resource Not Found', 404);
            }

            if ($e instanceof HttpException) {
                return ResponseHandler::error($e->getMessage(), $e->getStatusCode());
            }

            // For all other exceptions, use a generic error response
            return ResponseHandler::error($e->getMessage(), 500);
        });
    })
    ->create();

