<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = $this->createUser($request->validated());

        return response()->json(['message' => 'User registered successfully']);
    }

    public function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Check if email contains '@realestate.com'
        if (strpos($email, '@realestate.com') !== false) {
            // Perform custom admin authentication
            $admin = Admin::where('email', $email)->first();

            if (!$admin || !Hash::check($password, $admin->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Generate admin token manually
            $adminToken = 'manually_generated_admin_token';

            // Save admin token in the database
            $admin->update(['token' => $adminToken]);

            return response()->json(['admin_token' => $adminToken]);
        }

        // Regular user authentication
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user' => $user, 'access_token' => $token, 'token_type' => 'Bearer']);
    }

    protected function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
    }
}