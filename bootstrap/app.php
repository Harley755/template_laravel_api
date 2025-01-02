<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\Access\AuthorizationException as UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configure dontFlash
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        // Render ValidationException
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }
        });

        // Render NotFoundHttpException
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => trans('exceptions.not_found'),
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }
        });

        // Render MethodNotAllowedHttpException
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => trans('exceptions.not_allowed'),
                    'status' => Response::HTTP_METHOD_NOT_ALLOWED
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }
        });

        // Render ModelNotFoundException
        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => trans('exceptions.Modelnot_found'),
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }
        });

        // Render RouteNotFoundException
        $exceptions->renderable(function (RouteNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => trans('exceptions.Routenot_found'),
                    'status' => Response::HTTP_NOT_FOUND
                ], Response::HTTP_NOT_FOUND);
            }
        });

        // Render AuthenticationException
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => trans('exceptions.unauthenticated'),
                    'status' => Response::HTTP_UNAUTHORIZED
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        // Render UnauthorizedException
        $exceptions->renderable(function (UnauthorizedException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => trans('exceptions.unauthorized'),
                    'status' => Response::HTTP_FORBIDDEN
                ], Response::HTTP_FORBIDDEN);
            }
        });
    })->create();
