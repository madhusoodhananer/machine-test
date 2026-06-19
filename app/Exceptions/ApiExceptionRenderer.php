<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Contracts\HttpStatusProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Maps any thrown exception to a single, consistent JSON error shape for the
 * API: { "message": "..." } with the right HTTP status.
 *
 * This is the one place that decides "which exception means which status", so
 * controllers stay free of try/catch boilerplate. It is registered in
 * bootstrap/app.php via $exceptions->render().
 */
class ApiExceptionRenderer
{
    /**
     * Returns a JSON response for API requests, or null to defer to Laravel's
     * default handling (web routes, and validation errors which already carry
     * their own { message, errors } shape).
     */
    public function render(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        // Keep Laravel's native validation payload (message + per-field errors).
        if ($e instanceof ValidationException) {
            return null;
        }

        [$status, $message] = $this->resolve($e);

        return response()->json(['message' => $message], $status);
    }

    /**
     * Resolve an exception to a [status, message] pair.
     *
     * @return array{0: int, 1: string}
     */
    private function resolve(Throwable $e): array
    {
        return match (true) {
            // Domain exceptions declare their own status (422, 409, …).
            $e instanceof HttpStatusProvider => [$e->httpStatusCode(), $e->getMessage()],

            $e instanceof AuthenticationException => [Response::HTTP_UNAUTHORIZED, 'Unauthenticated.'],

            $e instanceof AuthorizationException => [Response::HTTP_FORBIDDEN, 'This action is not authorized.'],

            // Missing model (route-model binding) or unknown route.
            $e instanceof ModelNotFoundException,
            $e instanceof NotFoundHttpException => [Response::HTTP_NOT_FOUND, 'The requested resource was not found.'],

            $e instanceof MethodNotAllowedHttpException => [Response::HTTP_METHOD_NOT_ALLOWED, 'This method is not allowed for this endpoint.'],

            // Any other HTTP exception (429 throttling, aborts, …) keeps its status.
            $e instanceof HttpExceptionInterface => [$e->getStatusCode(), $e->getMessage() ?: 'Request failed.'],

            // Anything unexpected is a 500 — never leak internals in production.
            default => [Response::HTTP_INTERNAL_SERVER_ERROR, $this->serverErrorMessage($e)],
        };
    }

    private function serverErrorMessage(Throwable $e): string
    {
        return config('app.debug')
            ? $e->getMessage()
            : 'Something went wrong. Please try again later.';
    }
}
