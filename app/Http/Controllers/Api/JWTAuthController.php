<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class JWTAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register_user', 'logout']]);
    }

    protected function get_token_response($token)
    {
        return [
            'accessToken' => $token,
            'tokenType' => 'bearer',
            'expiresIn' => Auth::factory()->getTTL() * 60
        ];
    }


    /**
     * Get a JWT via given credentials
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required | email',
            'password' => 'required | string'
        ]);

        $user_data = $request->only('email', 'password'); // credentials
        $token = Auth::attempt($user_data);

        if (!$token) {
            return response()->json([
                'message' => 'User does not exists or unauthorized'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'authorization' => $this->get_token_response($token)
        ]);
    }

    public function register_user(Request $request)
    {
        $request->validate([
            'name' => 'required | string | max:255',
            'email' => 'required | email| max:255 | unique:users',
            'password' => 'required | string | min:6'
        ]);

        $new_user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $new_user
        ]);
    }

    /**
     * Invalidate the current token
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $new_response = $this->respondWithToken(Auth::refresh());
        return response()->json($new_response);
    }
}
