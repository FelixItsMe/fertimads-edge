<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Auth\StoreLoginRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(StoreLoginRequest $request) : JsonResponse {
        $user = User::query()
            ->where([
                ['email', $request->safe()->email],
                ['role', 'control'],
            ])
            ->orWhere([
                ['email', $request->safe()->email],
                ['role', 'care'],
            ])
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 422);
        }

        if (!Hash::check($request->safe()->password, $user->password)) {
            return response()->json([
                'message' => 'Password tidak sesuai!',
            ], 422);
        }

        $token = $user->createToken(Str::random(10));

        return response()->json([
            'token' => $token->plainTextToken,
            'message' => 'User Authenticated',
        ]);
    }

    public function logout(Request $request) : JsonResponse {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logout'
        ]);
    }
}
