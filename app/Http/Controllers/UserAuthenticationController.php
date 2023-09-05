<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserAuthenticationController extends Controller
{
    protected $userService, $user;
  
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }  

    function register(Request $request)
    {
        $userData = [
            'name' => $request->input('name'),
            'email' => strtolower($request->input('email')),
            'password' => $request->input('password'),
        ];

        $validateUserData = $this->userService->validateUserData($userData);

        if ($validateUserData) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validateUserData,
            ], 422);
        }

        $user = $this->userService->registerUser($userData);
        $token = $user->createToken('auth_token')->plainTextToken;     

        return response()->json([
             'user'=> $user,
             'message' => 'User Account Created Successfully',
             'access_token' => $token,
             'token_type' => 'Bearer',
            ],200);  
    }    

    public function login(UserRegistrationFormRequest $request)
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
