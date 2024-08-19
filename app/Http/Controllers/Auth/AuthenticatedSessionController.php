<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRoleEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        switch (auth()->user()->role) {
            case UserRoleEnums::MANAGEMENT->value:
                $routeName = 'dashboard.index';
                break;
            case UserRoleEnums::CONTROL->value:
                $routeName = 'head-unit.manual.index';
                break;
            case UserRoleEnums::CARE->value:
                $routeName = 'care.index';
                break;

            default:
                $routeName = 'dashboard.index';
                break;
        }

        activity()
            ->performedOn(User::find($request->user()->id))
            ->event('login')
            ->log('login web');

        return redirect()->intended(route($routeName, absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        activity()
            ->performedOn(User::find($request->user()->id))
            ->event('logout')
            ->log('logout web');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
