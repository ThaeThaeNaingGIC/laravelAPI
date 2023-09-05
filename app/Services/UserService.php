<?php
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validateUserData($userData)
    {
        $validator = Validator::make($userData, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ],
         [
             'name.required' => 'The name field is required.',
             'email.required' => 'The email field is required.',
             'email.email' => 'The email must be a valid email address. Eg. example@gmail.com',
             'password.required' => 'The password field is required. Null is not allowed',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return null; // Indicates successful validation
    }

    public function registerUser($userData)
    {
        return $this->userRepository->createUser($userData);
    }
}
