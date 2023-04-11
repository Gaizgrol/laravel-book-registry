<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $user = User::where('email', $request->input('email'))->get()->first();

        if (!$user) {
            return response()->json([
                'error' => 'Email not found'
            ], 404);
        }
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'error' => 'Invalid password'
            ], 401);
        }

        $user->auth_token = (string) \Illuminate\Support\Str::uuid();
        $user->save();

        return response()->json([
            'message' => 'Success.',
            'data' => $user->auth_token
        ]);
    }
}
