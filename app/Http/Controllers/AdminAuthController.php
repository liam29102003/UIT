<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation rules for registration data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Create new admin user with 'admin' role
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash the password
            'role' => 'admin', // Set role to 'admin'
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    /**
     * Handle an admin login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in the user

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('', 'Invalid credentials', 401);
        }
        
        // throw ValidationException::withMessages([
        //     'email' => ['The provided credentials are incorrect.'],
        // ]);
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], 200);


        
    }
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
