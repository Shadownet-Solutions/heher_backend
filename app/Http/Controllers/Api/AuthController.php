<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register'
                ]
        ]);
    }

    public function register(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'gender' => 'required',
            'password' => 'required|min:6'

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'password' => bcrypt($request->password)
        ]);

        $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token

        ]);
    }

    public function logout(Request $request){
        $user = $request->user();
        $action = Auth::logout($user);
        // $request->user()->token()->revoke();
        return response()->json([
            'status' => 'success',
            'message' => 'User successfully logged out'
            ]);
        }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)){
            return response()->json([
                'status' => 'error',
                'message' => 'Login Failed'
                ], 401);
            }
            $user = $request->user();
            $token = Auth::login($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User successfully logged in',
                'user' => $user,
                'token' => $token
                ]);

    }


    

}
