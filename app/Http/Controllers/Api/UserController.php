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
use App\Classes\AgoraDynamicKey\RtcTokenBuilder2;

use PayPal\Api\Plan;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Currency;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;



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

 
    


    $appID = env('AGORA_APP_ID');
    $appCertificate = env('AGORA_APP_CERTIFICATE');
    $server_key = env('AGORA_SERVER_KEY'); //this is the server fcm key
    $channelName = $request->channelName;
    $userAccount = $user->id;
    $priviledeExpireTs = time() + 3600;
    $uid = $user->id;
    $role = $request->role; // publisher or subscriber

    //token base 007eJxTYLihnqLPNEu46mFwuPCqT 
            //   007eJxTYNCcxlO731Dr0qqnXic
            //temp 007eJxTYNCcxlO731Dr0qqnXic/7S1eFHZ6QkCvRvE7/1BxxjnVYpoKDGZJyZYp5hYW5smJliaWqRYWZubmiSZJiUYWBpaJBskGnguMUhsCGRnyDBOYGRkgEMRnZjCMN2JgAADb3Ryb
    //retrun temparary token
    return response()->json([
        'status' => 'success',
        'token' => '007eJxTYFhceXnXvpAHm506JWyamc8LKRvk9O+9+1H46CaluvpUHl0FBrOkZMsUcwsL8+RESxPLVAsLM3PzRJOkRCMLA8tEg2SD2V3GqatnGKeKFvAyMzIwMrAAMQgwgUlmMMkCZRvGGzEymAAAFPUdFA==',
        'agora_app_id' => $appID,
        'agora_server_key' => $server_key,
        ]);

// generate token
    $token = RtcTokenBuilder2::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $userAccount, $role, $priviledeExpireTs);
    if ($token) {
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'agora_app_id' => $appID,
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
    //         'key' => $appID,
    //         'channelName' => $channelName,
    //         'uid' => $uid,
    //         'role' => $role
    //     ],
    //     'headers' => [ 
            
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Basic ' . base64_encode($appID . ':' . $appCertificate),
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

 //paypal payment
public function payment(){
    $clientId = env('PAYPAL_CLIENT_ID');
    $clientSecret = env('PAYPAL_CLIENT_SECRET');

    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential($clientId, $clientSecret)
    );

    // Step 1: Create a Billing Plan
    $plan = new Plan();
    $plan->setName('Heher Premium')
    ->setDescription('Monthly subscription for heher app')
    ->setType('fixed');

    // Set up billing cycles
    $paymentDefinition = new \PayPal\Api\PaymentDefinition();
    $paymentDefinition->setName('heher Premium')
    ->setType('REGULAR')
    ->setFrequency('Month') // Monthly subscription
    ->setFrequencyInterval('1')
    ->setCycles('12'); // Number of billing cycles

    // Set up amount and currency
    $currency = new Currency();
    $currency->setCurrency('USD')
    ->setValue('5.00'); // Set your subscription amount here

    $paymentDefinition->setAmount($currency);

    $plan->setPaymentDefinitions([$paymentDefinition]);
    // Activate the plan
    $plan->setState('ACTIVE');
    $createdPlan = $plan->create($apiContext);

    // Step 2: Activate the Billing Plan
    $patch = new Patch();
    $patch->setOp('replace')
    ->setPath('/')
    ->setValue(new PayPalModel('{"state":"ACTIVE"}'));

    $patchRequest = new PatchRequest();
    $patchRequest->addPatch($patch);

    $createdPlan->update($patchRequest, $apiContext);

    // Step 3: Create a Billing Agreement
    $agreement = new \PayPal\Api\Agreement();
    $agreement->setName('Subscription Agreement')
        ->setDescription('Subscription agreement for awesome service')
    ->setStartDate(date('Y-m-d\TH:i:s\Z', strtotime('+1 day'))); // Start date is one day from now

// Set up merchant preferences
    $merchantPreferences = new MerchantPreferences();
    $merchantPreferences->setReturnUrl('YOUR_RETURN_URL')
    ->setCancelUrl('YOUR_CANCEL_URL')
    ->setAutoBillAmount('yes')
    ->setInitialFailAmountAction('CONTINUE')
    ->setMaxFailAttempts('0');

    $agreement->setMerchantPreferences($merchantPreferences);
    $agreement->setPlan($createdPlan);

    // Create the agreement
    $agreement = $agreement->create($apiContext);

    // Get the approval URL
    $approvalUrl = $agreement->getApprovalLink();

    // Redirect the user to PayPal for approval
    header("Location: $approvalUrl");
}
    //soft delete account
    public function deleteAccount(){
        $requester = Auth::user();
        try {
            $user = User::findOrFail($requester->id);
    
            // Soft delete the user
            $user->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Account deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
        





}
