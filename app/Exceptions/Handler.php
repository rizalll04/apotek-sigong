<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle 404 Not Found exceptions
        $this->renderable(function (NotFoundHttpException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Halaman tidak ditemukan.'
                ], 404);
            }

            // Redirect sesuai role pengguna
            if (auth()->check()) {
                $user = auth()->user();
                
                if ($user->role === 'admin') {
                    return redirect()->route('admin.index');
                } elseif ($user->role === 'apoteker') {
                    return redirect()->route('manajemen.index');
                } else {
                    // Untuk role user biasa atau yang lain
                    return redirect()->route('pengguna.index');
                }
            }

            // Jika user belum login, redirect ke halaman login
            return redirect()->route('login');
        });
    }
}
