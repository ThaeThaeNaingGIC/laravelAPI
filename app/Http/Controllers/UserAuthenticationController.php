<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserAuthenticationController extends Controller
{
    //
public function register(Request $request)
    {
        $name = $request->input('name');
        $email = strtolower($request->input('email'));
        $password = $request->input('password');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User Account Created Successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

public function login(Request $request)
    {
        $email = strtolower($request->input('email'));
        $password = $request->input('password');

        $credentials = [
            'email' => $email,
            'password' => $password
        ];
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User Account login Successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],200);
    }

public function logout()
    {
         $user = Auth::user();
    
         // Revoke (delete) each of the user's tokens
         foreach ($user->tokens as $token) {
           $token->delete();
        }

        return response()->json([
        'message' => 'Successfully logged out'
     ], 200);

    }

}
