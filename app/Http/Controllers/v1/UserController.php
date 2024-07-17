<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::query()
            ->whereNot('role', 'su')
            ->orderBy('name')
            ->paginate(10);

        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name'      => $request->safe()->name,
            'email'     => $request->safe()->email,
            'role'      => $request->safe()->role,
            'password'  => Hash::make($request->safe()->password),
        ]);

        return redirect()->route('user.index')->with('user-success', 'Berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($request->safe()->password) {
            $user->password = Hash::make($request->safe()->password);
        }

        $user->name     = $request->safe()->name;
        $user->email    = $request->safe()->email;
        $user->role     = $request->safe()->role;
        $user->save();

        return redirect()->route('user.index')->with('user-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse|RedirectResponse
    {
        $user->delete();

        session()->flash('user-success', 'Berhasil dihapus');

        if (request()->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus!'
            ]);
        }

        return redirect()->route('user.index');
    }
}
