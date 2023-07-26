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

        
        $request->validate([
            'code' => 'required',
            'user_id' => 'required'
        ]);

        $id = $request->user_id;
        $user = User::find($id);

        $find = UserEmailCode::where('user_id', $id)
                        ->where('code', $request->code)
                        ->where('updated_at', '>=', now()->subMinutes(5))
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
            // $token = Auth::login($user);
            $code = auth()->user()->generateCode();
            return response()->json([
                'status' => 'success',
                'message' => 'A code has been sent to your email',
                'user_id' => $user->id,
                //  'token' => $token,
                    
                ]);

    }


    

}
