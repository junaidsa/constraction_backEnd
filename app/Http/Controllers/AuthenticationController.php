<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{

    public function authenicate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 401);
        }
        if (Auth::class::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status' => true,
                'token' => Auth::user()->createToken('authToken')->plainTextToken,
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email
                ],
            ], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Invalid credentials'], 401);
        }
    }
    public function logout()
    {
        $user = User::find(Auth::id()) ?: abort(404);
        $user->tokens()->each(function ($token) {
            $token->delete();
        });
        return response()->json(['status' => true, 'message' => 'Logged out successfully'], 200);
    }
}
