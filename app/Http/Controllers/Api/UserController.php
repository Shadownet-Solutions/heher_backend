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
       $user->name = $request->name;
       $user->phone = $request->phone;
       $user->birthday = $request->birthday;
       $user->status = $request->status;
       $user->religion = $request->religion;
       $user->children = $request->children;
       $user->smoke = $request->smoke;
       $user->drink = $request->drink;
       $user->education = $request->education;
       $user->address = $request->address;
       $user->save();
       return response()->json([
        'status' => 'success',
        'user' => $user
        ]);
   }




}
