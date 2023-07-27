<?php

namespace App\Http\Controllers\Api;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Validator;
use App\Models\UserEmailCode;

class AuthController extends Controller
{

    // use AuthenticatesUsers;



    public function __construct() {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register',
                'completeSignIn',
                ]
        ]);
    }
// register user
public function register(Request $request) {

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
    ]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    //if validator fails return error
    // if ($validator->fails()) {
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => 'Validation failed',
    //     ]);
    // }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
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


    //log user out
    public function logout(Request $request){
        $user = $request->user();
        $action = Auth::logout($user);
        // $request->user()->token()->revoke();
        return response()->json([
            'status' => 'success',
            'message' => 'User successfully logged out'
            ]);
        }
// sign user in with otp

    public function completeSignIn(Request $request){

        
        $validator = Validator::make($request->all(), [
            'code' => 'required|min:5',
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $id = $request->user_id;
        $user = User::find($id);

        $find = UserEmailCode::where('user_id', $id)
                        ->where('code', $request->code)
                        ->where('updated_at', '>=', now()->subMinutes(10))
                        ->first();
          if (!is_null($find)) {

            
            Session::put('user_2fa', $user);

            
            $token = Auth::login($user);
            
            return response()->json([
                'status' => 'success',
                'message' => 'user Successfully Logged in',
                'token' => $token,
                 'user' => $user
                 
                ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid OTP'
            ], 401);
        
        




        // $credentials = request(['email', 'password']);

        // if (Auth::attempt($credentials)) {

           

        //     return response()->json([
        //         'status' => 'success',
        //         'message' => 'OTP sent to your email!',
        //     ]);
        // } else {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Login Failed, You have entered invalid credentials'
        //         ], 401);
        //     }
    }

// resend otp

public function resend()
{
    auth()->user()->generateCode();

    return response()->json([
        'status' => 'success',
        'message' => 'OTP re-sent to your email!',
    ]);
}


// send otp after validating username and password
public function login(Request $request){
       
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    
    $credentials = request(['email', 'password']);

    if(!Auth::attempt($credentials)){
        return response()->json([
            'status' => 'error',
            'message' => 'Login Failed, Invalid Credentials'
            ], 401);
        }
        $user = $request->user();
        // $token = Auth::login($user);
        $code = auth()->user()->generateCode();
        return response()->json([
            'status' => 'success',
            'message' => 'A code has been sent to your email Valid for 5 minutes',
            'user_id' => $user->id,
            //  'token' => $token,
                
            ]);

}


    

}
