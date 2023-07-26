<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
   public function index()
   {
       return response()->json(User::all());
   }

   public function userProfile()
   {


        $user = Auth::user();
    //    return User::find($id);
        return response()->json($user);
   }

   //function updateUser data

   public function updateUser(Request $request)
{
    $user = Auth::user();

    if ($request->has('name')) {
        $user->name = $request->name;
    }
    if ($request->has('username')) {
        $user->username = $request->username;
    }

    if ($request->has('phone')) {
        $user->phone = $request->phone;
    }

    if ($request->has('birthday')) {
        $user->birthday = $request->birthday;
    }

    if ($request->has('status')) {
        $user->status = $request->status;
    }

    if ($request->has('religion')) {
        $user->religion = $request->religion;
    }

    if ($request->has('children')) {
        $user->children = $request->children;
    }

    if ($request->has('smoke')) {
        $user->smoke = $request->smoke;
    }

    if ($request->has('drink')) {
        $user->drink = $request->drink;
    }

    if ($request->has('education')) {
        $user->education = $request->education;
    }

    if ($request->has('address')) {
        $user->address = $request->address;
    }
    if ($request->has('profile_image')) {
        $user->profile_image = $request->profile_image;
    }
    if ($request->has('cordinates')) {
        $user->cordinates = $request->cordinates;
    }

    $user->save();

    return response()->json([
        'status' => 'success',
        'user' => $user
    ]);
}

//get a username and check if exist or available

public function checkUsername(Request $request)
{
    $username = $request->username;
    $user = User::where('username', $username)->first();
    if ($user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Username already exist'
            ], 409);
        }
        else{
            return response()->json([
                'status' => 'success',
                'message' => 'Username available'
                ]);
            }
        }






}
