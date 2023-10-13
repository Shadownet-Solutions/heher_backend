<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Gallery;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Classes\AgoraDynamicKey\RtcTokenBuilder;


class UserController extends Controller
{
   public function index()
   {
        $users = User::all();

       return response()->json([
            "status" => "success",
            "users" => $users->toArray()
       ]);
   }

   public function userProfile()
   {


    $user = Auth::user();
    if ($user) {
    return response()->json([
        'status' => 'success',
        'user' => $user
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'session expired'
            ], 401);
        }
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

    if ($request->has('device_token')) {
        $user->device_token = $request->device_token;
    }

    if ($request->has('gender')) {
        $user->gender = $request->gender;
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

    if ($request->has('timezone')) {
        $user->timezone = $request->timezone;
    }
    if ($request->has('language')) {
            $user->language = $request->language;
    }

    $user->save();

    return response()->json([
        'status' => 'success',
        'user' => $user
    ]);
}

//accept user photos url and store
public function uploadUserPhoto(Request $request){
    $user = Auth::user();

    // Ensure 'photos' key exists and it's an array
    $photos = $request->get('photos');
    if (!$photos || !is_array($photos)) {
        return response()->json([
            'status' => 'error',
            'message' => 'No photos provided or invalid format'
        ], 400);
    }

    // Loop through each photo URL and save
    foreach ($photos as $photoUrl) {
        $gallery = new Gallery();
        $gallery->user = $user->id;
        $gallery->image = $photoUrl;
        $gallery->save();
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Photos uploaded successfully'
    ]);
    }


    // get user photos
    public function getUserPhotos($id){
        $user = Auth::user();
        $photos = Gallery::where('user', $id)->get();
        return response()->json([
            'status' => 'success',
            'photos' => $photos
            ]);
            }

//get individual user data
public function getUserData($id){
    $user = User::find($id);
    if ($user) {
        return response()->json([
            'status' => 'success',
            'user' => $user
            ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                    ], 404);
                    }
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

 // generate agora token for user
 public function Token(Request $request){
    $user = Auth::user();

    //return temporary token
    


    $appId = env('AGORA_APP_ID');
    $appCertificate = env('AGORA_APP_CERTIFICATE');
    $server_key = env('AGORA_SERVER_KEY'); //this is the server fcm key
    $channelName = $request->channelName;
    $userAccount = $user->id;
    $priviledeExpireTs = time() + 3600;
    $uid = $user->id;
    $role = $request->role; // publisher or subscriber

    //retrun temparary token
    return response()->json([
        'status' => 'success',
        'token' => '007eJxTYLikssa603/rw+98O4vlM4pkKoK3HC9RWh+TMrfSJ///l2sKDGZJyZYp5hYW5smJliaWqRYWZubmiSZJiUYWBpaJBskG8bKaqQ2BjAwfHN4xMzJAIIjPzGAYb8TAAABT3h73',
        'agora_app_id' => $appId,
        'agora_server_key' => $server_key,
        ]);

// generate token
    $token = RtcTokenBuilder::buildTokenWithUserAccount($appId, $appCertificate, $channelName, $userAccount, $role, $priviledeExpireTs);
    if ($token) {
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'agora_app_id' => $appId,
            'agora_server_key' => $server_key,
            ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Token generation failed'
            ], 500);
        }
    


    //generate token
    // $client = new Client();
    // $client->post("https://api.agora.io/v1/token/rtc/developer/6bc9d7887ca949e88677a4ba2809a0c0/generate", [
    //     'verify' => false,
    //     'json' => [
    //         'key' => $appId,
    //         'channelName' => $channelName,
    //         'uid' => $uid,
    //         'role' => $role
    //     ],
    //     'headers' => [ 
            
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Basic ' . base64_encode($appId . ':' . $appCertificate),
    //     ],
    // ]);

    // $token = json_decode($response->getBody()->getContents());
    // if ($token) {
    //     return response()->json([
    //         'status' => 'success',
    //         'token' => $token->token
    //         ]);
    // } else {
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => 'Token generation failed'
    //         ], 500);




    //     }

    
 }


 //get referral link
 public function getRefererLink(){
    $user = Auth::user();
    $referral_link = "https://www.he-her.com?referral=" . $user->username;
    return response()->json([
        'status' => 'success',
        'referral_link' => $referral_link
        ]);
 }







}
