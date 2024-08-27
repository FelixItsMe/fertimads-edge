<?php

namespace App\Http\Controllers\v1;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $user = User::create([
            'name'      => $request->safe()->name,
            'email'     => $request->safe()->email,
            'role'      => $request->safe()->role,
            'password'  => Hash::make($request->safe()->password),
        ]);

        activity()
            ->performedOn($user)
            ->event('create')
            ->log('User baru ditambahkan');

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

        activity()
            ->performedOn($user)
            ->event('edit')
            ->log('User ' . $user->name . ' diupdate');

        return redirect()->route('user.index')->with('user-success', 'Berhasil disimpan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse|RedirectResponse
    {
        activity()
            ->performedOn($user)
            ->event('delete')
            ->log('User ' . $user->name . ' dihapus');

        $user->delete();

        session()->flash('user-success', 'Berhasil dihapus');

        if (request()->acceptsJson()) {
            return response()->json([
                'message' => 'Berhasil dihapus!'
            ]);
        }

        return redirect()->route('user.index');
    }

    public function exportExcel() : BinaryFileResponse|RedirectResponse {
        $collect = [];

        foreach (
            User::query()
                ->whereNot('role', 'su')
                ->lazy() as $user
        ) {
            $collect[] = (object) [
                "name"          => $user->name,
                "role"          => ucfirst($user->role),
                "email"         => $user->email,
                "created_at"    => $user->created_at->format('Y-m-d H:i:s'),
                "updated_at"    => $user->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        $collect = collect($collect);

        if ($collect->count() == 0) {
            return back()->with('user-success', 'Tidak ada data user!');
        }

        return Excel::download(new UserExport($collect), now()->format('YmdHis') . '-anggota.xlsx');
    }
}
