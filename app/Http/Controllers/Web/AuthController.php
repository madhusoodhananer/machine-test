<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            if (! Auth::attempt($request->credentials(), $request->boolean('remember'))) {
                throw ValidationException::withMessages([
                    'email' => __('These credentials do not match our records.'),
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        } catch (ValidationException $exception) {
            // Surface invalid-credential messages inline on the login form.
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput($request->only('email'))
                ->with('error', 'We could not sign you in. Please try again.');
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect('/login');
    }
}
