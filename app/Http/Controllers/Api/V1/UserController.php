<?php

namespace App\Http\Controllers\Api\V1;

use abdullahobaid\mobilywslaraval\Mobily as MobilywslaravalMobily;
use App\DeviceToken;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mobily;
use App\UserGroup;
use App\Group;
use App\Message;
use App\Media;
use App\Organization;
use App\Job;
use App\GroupType;
use App\Notification;
use App\NotificationReceiver;
use App\BankTransferRequirement;
use App\Interest;
use App\Activity;
use App\UserMessageSeen;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\groupBackground;
use URL;
use Request as HttpRequest;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use PushNotification;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class UserController extends Controller
{   
    private $serviceAccount;

    private $firebase;

    function __construct()
    {
        $this->serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/ammem-a0240-385b3d3ec166.json');
        $this->firebase = (new Factory)
            ->withServiceAccount($this->serviceAccount)
            ->withDatabaseUri('https://ammem-a0240.firebaseio.com/')
            ->create();
    }

    // generate refresh token
    protected function refresh_token(Request $request)
    {
        $request->request->add([
            'grant_type' => $request->get('grant_type'),
            'client_id' => $request->get('client_id'),
            'client_secret' => $request->get('client_secret'),
            'refresh_token' => $request->get('refresh_token'),
            'scope' => null,
        ]);

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        return Route::dispatch($proxy);
    }

    // generate access token (login)
    protected function access_token(Request $request)
    {
        $rules = [
            'grant_type' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
            'username' => 'sometimes',//|exists:users,username
            'email' => 'sometimes',//|exists:users,email
            'password' => 'required|min:6',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }
/*        $user = User::where('email', $request->get('email'))->first();
        if (!isset($user)) {
            return response_api(false, null, null, null, array(['fieldname' => 'User', 'message' => 'You are not a member with us']));
        }
        if ($user->status == 'suspend')
            return response_api(false, null, null, null, array(['fieldname' => 'Status suspend', 'message' => 'User was suspended by admin']));*/
        $request->request->add([
            'grant_type' => $request->get('grant_type'),
            'client_id' => $request->get('client_id'),
            'client_secret' => $request->get('client_secret'),
            'username' => $request->get('username'),
            'password' => $request->get('password'),
            'scope' => null
        ]);

        $proxy = Request::create(
            env('APP_URL').'/oauth/token',
            'POST'
        );
        // turn on mobile
        // DeviceToken::where('user_id', $user->id)->update(['status' => 'on']);
        return Route::dispatch($proxy);
    }

    // resend activation code
    public function resendActivationCode(Request $request)
    {   
        //HttpRequest::merge(['mobile' => $this->formatMobileNumber($request->get('mobile'))]);
        $rules = [
            'mobile' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }
        $user = User::where('mobile', $request->get('mobile'))->first();
        if ($user->resend_number == 0) {
            return response()->json([
                'status' => false,
                'message' => __('messages.excced_number_of_send_activation_code')
            ]);
        }
        if (isset($user)) {
            $activation_code = $this->generateActivationCode(6);
            $resend = Mobily::send($request->get('mobile'), 'Your activation code: ' . $activation_code);
            if ($resend) {
                $user->activation_code = $activation_code;
                $user->password = bcrypt($activation_code);
                $user->resend_number = $user->resend_number-1;
                $user->save();
                return response()->json([
                    'item' => $user,
                    'message' => __('messages.resend_activiation_code') ,
                    'status' => true
                ]);
            }
        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false,
        ]);
    }


    /**
     * function to post create new user from web app
     * 
     * @param Request $request
     * 
     * @return response
     * 
     */
    public function PostNewUser(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'family_name' => 'required',
            'region' => 'required',
            'mobile' => 'sometimes|unique:users,mobile',
            'activity_id' => 'sometimes|exists:activities,id',
            'organization_id' => 'required|exists:organizations,id',
            'interest_id' => 'required|exists:interests,id',
            'gender' => 'required|in:male,female',
            'job_id' => 'required|exists:jobs,id',
            'email' => 'required|unique:users,email',
            'profile_pic' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }

        $mobile_number = $request->get('mobile');

        $activation_code = $this->generateActivationCode(6);

        $user = new User();
        if (!empty($request->get('country'))) {
            $user->country = $request->get('country');
        } else {
            $user->country = "";
        }
        if (!empty($request->profile_pic)) {
            $imageData = $request->get('profile_pic');
            $fileName = time().'.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
            \Image::make($request->get('profile_pic'))->save(public_path().'/assets/upload/'.$fileName);
        }  
        $user->mobile = $request->get('mobile');
        $user->email = $request->get('email');
        $user->gender = $request->get('gender');
        $user->name = $request->get('first_name'). " ".$request->get('family_name');
        $user->activity_id = $request->get('activity_id');
        $user->organization_id = $request->get('organization_id');
        $user->interest_id = $request->get('interest_id');
        $user->mobile = $request->get('mobile');
        $user->type = 'user';
        $user->status = 'active';
        $user->job_id = $request->get('job_id');
        $user->profile_pic = $fileName;
        $user->activation_code = $activation_code;
        $user->password = bcrypt($activation_code);

        if ($user->save()) {
            $this->storeInFireBase($user);
            Mobily::send($mobile_number, 'Your activation code: ' . $activation_code);
            //$this->send($mobile_number, 'Your activation code: ' . $activation_code);
            return response()->json([
                'items' => $user,
                'message' => __('messages.create_new_user'),
                'status' => true
            ]);
        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);

    }
    // add new user and send activation code
    public function postUser(Request $request)
    {   
        //HttpRequest::merge(['mobile' => $this->formatMobileNumber($request->get('mobile'))]);
        $rules = [
            'mobile' => 'required|unique:users,mobile',
            //'email' => 'sometimes|unique:users',
            'country' => 'sometimes'
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }
        $mobile_number = $request->get('mobile');

        $activation_code = $this->generateActivationCode(6);
        Mobily::send($mobile_number, 'Your activation code: ' . $activation_code);
        //$this->send($mobile_number, 'Your activation code: ' . $activation_code);
        $user = new User();
        if (!empty($request->get('country'))) {
            $user->country = $request->get('country');
        } else {
            $user->country = "";
        }
        
        $user->mobile = $request->get('mobile');
        $user->email = $request->get('email');
        $user->activation_code = $activation_code;
        $user->password = bcrypt($activation_code);

        if ($user->save()) {
            $this->storeInFireBase($user);
            Mobily::send($mobile_number, 'Your activation code: ' . $activation_code);
            //$this->send($mobile_number, 'Your activation code: ' . $activation_code);
            return response()->json([
                'items' => $user,
                'message' => __('messages.create_new_user'),
                'status' => true
            ]);
        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);
    }

    // complete user profile
    public function putUser(Request $request)
    {   
        $imageName = "";
        $rules = [
            'name' => 'required',
            'region' => 'required',
            'activity_name' => 'sometimes',
            'mobile' => 'sometimes|unique:users,mobile,'.auth()->user()->id,
            'organisation_name' => 'sometimes',
            'activity_id' => 'sometimes|exists:activities,id',
            'organization_id' => 'required|exists:organizations,id',
            'interest_id' => 'required|exists:interests,id',
            'gender' => 'required|in:male,female',
            'job_id' => 'required|exists:jobs,id',
            'bod' => 'required',
            'city' => 'required',
            'email' => 'required|unique:users,email,'.auth()->user()->id,
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ];

        if (!empty($request->mobile)) {
            $rules['old_activation_code'] = 'required';
        }

        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
        }

        if (!empty($request->profile_pic)) {
            $imageName = $this->upload($request, 'profile_pic');
        }  

        auth()->user()->name = $request->get('name');
        auth()->user()->region = $request->get('region');
        auth()->user()->bod = $request->get('bod');
        auth()->user()->city = $request->get('city');

        if ($request->has('activity_id'))
            auth()->user()->activity_id = $request->get('activity_id');
        auth()->user()->organization_id = $request->get('organization_id');
        auth()->user()->interest_id = $request->get('interest_id');
        if ($request->has('activity_name'))
            auth()->user()->activity_name = $request->get('activity_name');
        if ($request->has('organisation_name'))
            auth()->user()->organisation_name = $request->get('organisation_name');
        auth()->user()->photo_id = $request->get('photo_id');
        auth()->user()->gender = $request->get('gender');
        auth()->user()->job_id = $request->get('job_id');
        auth()->user()->email = $request->get('email');
        auth()->user()->profile_pic = $imageName;
        if ($request->has('mobile') && auth()->user()->activation_code == $request->get('old_activation_code')){
            $mobile_number = $request->get('mobile');
            $activation_code = $this->generateActivationCode(6);
            $resend = Mobily::send($mobile_number, 'Your activation code: ' . $activation_code);
            if ($resend) {
                auth()->user()->mobile = $mobile_number;
                auth()->user()->is_confirm = 0;

                auth()->user()->activation_code = $activation_code;
                auth()->user()->password = bcrypt($activation_code);
            }
        }
        if (auth()->user()->save()) {
            $user = User::find(auth()->user()->id);
            return response()->json([
                'items' => $user,
                'message' => 'تم تعديل بيانات المستخدم بنجاح' ,
                'status' => true
            ]);
        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);
    }

    // activation by mobile
    public function confirmActivationCode(Request $request)
    {   
        //HttpRequest::merge(['mobile' => $this->formatMobileNumber($request->get('mobile'))]);
        $rules = [
            'mobile' => 'required|exists:users,mobile',
            'activation_code' => 'required'
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }
        //$mobile_number = $this->formatMobileNumber($request->get('mobile'));

        $user = User::where('mobile', $request->get('mobile'))->where('activation_code', $request->get('activation_code'))->first();
        if (!isset($user)) {
            return response()->json([
                'message' => __('messages.not_exist_user'). " ".__('messages.mobile_or_activation_not_exist'),
                'status' => false
            ]);
        }
        $user->name = $request->username;
        $user->is_confirm = 1;
        $user->save();

        $request->request->add([
            'grant_type' => $request->get('grant_type'),
            'client_id' => $request->get('client_id'),
            'client_secret' => $request->get('client_secret'),
            'username' => $user->mobile,
            'password' => $user->activation_code, // password == activation code
            'scope' => null,
        ]);
        $proxy = Request::create(
            'oauth/token',
            'POST'
        );
        // turn on mobile
        DeviceToken::where('user_id', $user->id)->update(['status' => 'on']);
        return Route::dispatch($proxy);
    }

    // get user information or auth user
    public function getUser($user_id = null)
    {
        $user = auth()->user();
        if (isset($user_id)) {
            $user = User::find($user_id);
        } else {
            $user = User::all();
        }

        return response()->json([
            'items' => $user,
            'message' => __('messages.get_user_info_msg'),
            'status' => true
        ]);

    }

    public function checkContacts(Request $request)
    {
        $rules = [
            'contacts' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }
        $contacts = $request->get('contacts');
        $platform = $request->get('platform');
        if (empty($platform)) {
            $platform = "1";
        }

        $contacts = substr($contacts, 1, strlen($contacts) - 2);
        $contacts = explode(',', $contacts);
        $contacts = array_unique($contacts);
        
        $users = [];
        foreach ($contacts as $contact) {
            if ($platform == 2) {
                $contact = substr($contact, 1, strlen($contact) - 2);
            }
            $user = User::where('mobile', $contact)->first();
            if (isset($user))
                $users[] = $user;
        }
        return response()->json([
            'items' => $users,
            'message' => __('messages.fetch_data_msg'),
            'status' => true
        ]);
    }

    /**
     * function to get all required user infos
     * 
     * @param Requet $request
     * 
     * @return  response
     */
    public function getUserInfo($action_id = null)
    {
        $user_id = auth()->user()->id;

        $user_groups = UserGroup::where('user_id', $user_id)->where('status', 'accept')->pluck('group_id')->toArray();

        $groups_id = array_unique($user_groups);
        $groups_id = Group::whereIn('id', $groups_id)->pluck('id')->toArray();

        $groups_background = groupBackground::whereIn('group_id', $groups_id)->pluck('group_id', 'background');

        $groups_collection = Group::whereIn('id', $groups_id);
        $groups_count = $groups_collection->count();
        $groups = $groups_collection->orderBy('created_at', 'DESC')->get();

        if (isset($_GET['media_type']) && ($_GET['media_type'] == 'user' || $_GET['media_type'] == 'group')) {
            $message_ids = [];
            if ($_GET['media_type'] == 'user') {
                if (!isset($action_id))
                $message_ids = Message::where('user_id', $user_id)->where('is_archived', '=', 0)->pluck('id')->toArray();
            } elseif (isset($action_id)) {
                $message_ids = MessageGroup::where('group_id', '=', $action_id)->pluck('message_id')->toArray();
            }
            $media_collection = Media::whereIn('message_id', $message_ids);
            $media_count = $media_collection->count();
            $media = $media_collection->orderBy('created_at', 'DESC')->get();
        } else {
            $media = "";
        }
        $organizations = Organization::orderBy('created_at', 'desc')->get();
        $jobs = Job::orderBy('created_at', 'desc')->get();
        $group_types = GroupType::orderBy('created_at', 'desc')->get();

        $notifications_id = NotificationReceiver::where('receiver_id', $user_id)->pluck('notification_id');
        $my_notifications_collection = Notification::whereIn('id', $notifications_id);
        $my_notifications = $my_notifications_collection->orderBy('created_at', 'desc')->get();

        $my_groups_id = Group::where('user_id',$user_id)->pluck('id');

        $requests_collection = BankTransferRequirement::whereIn('group_id', $my_groups_id);
        $requests = $requests_collection->orderBy('created_at', 'DESC')->get();

        $messages_collection = Message::where('user_id', $user_id)->where('is_archived', 0)->where('is_draft', 0);
        $messages = $messages_collection->orderBy('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        $interests = Interest::orderBy('created_at', 'desc')->get();

        $seen_messages_collection = Message::where('user_id', $user_id)->where('is_archived', 1);
        $seen_messages = $seen_messages_collection->orderBy('created_at', 'DESC')->get();

        $activities_count = Activity::count();
        $activities = Activity::orderBy('created_at', 'desc')->get();

        $user_waiting_groups = UserGroup::where('user_id', $user_id)->where('status', 'pending')->pluck('group_id')->toArray();
        $waiting_groups_id = array_unique($user_waiting_groups);

        if (isset($user_waiting_groups)) {
            $waiting_groups_collection = Group::where('admin_status', 'pending')->whereIn('id', $waiting_groups_id);
            $waiting_groups = $waiting_groups_collection->orderBy('created_at', 'DESC')->get();

        }


        // user seen messages
        
        $seen_msg_id = UserMessageSeen::where('user_id', $user_id)->pluck('message_id');
        $users_seen_collection = Message::whereIn('id', $seen_msg_id);
        $user_seen_msg = $users_seen_collection->orderBy('created_at', 'DESC')->get();

        $user_info = User::findOrFail($user_id);
        
        $back_ground_array = [];
        foreach ($groups_background->toArray() as $key => $value) {
            $back_ground_array[$value] = URL::to('/assets/upload/'.$key);

        }
        return response()->json([
            'groups' => $groups,
            'media' => $media,
            'organizations' => $organizations,
            'jobs' => $jobs,
            'group_types' => $group_types,
            'my_notifications' => $my_notifications,
            'bank_transfer_requirements' => $requests,
            'message' => $messages,
            'interests' => $interests,
            'seen_messages' => $user_seen_msg,
            'archived_msg' => $seen_messages,
            'activities' => $activities,
            'waiting_groups' => $waiting_groups,
            'items' => $user_info,
            'groups_background' => $back_ground_array,
            'status' => true,
            'message' => __('messages.get_user_info_msg')
        ]);
    } 

    /**
     * function to remove user information
     * 
     * @param Request $request
     * 
     * @return  response
     */
    public function deleteMyAccount(Request $request)
    {   
        $this->sendVirificationCode($request);
        return response()->json([
            'message' => __('messages.send_activiation_code'),
            'status' => true,
        ]);
    }


    /**
     * function to confirm delete my account
     * 
     * @param  Request $request
     * 
     * @return  response
     */
    public function confirmDeleteMyAccount($activation_code)
    {   
        $user_info = User::where('activation_code', '=', $activation_code)->first();
        if (empty($user_info)) {
            return response()->json([
                'status' => true,
                'message' => __('messages.not_exist_user')
            ]);
        }
        try {
            $user = User::findOrFail($user_info->id);
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => __('messages.delete_account_msg')
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg')
            ]);
        }
    } 
    /**
     * function to send varification code for the user
     * 
     * @param Request $request
     * 
     * @return  response
     */
    public function sendVirificationCode(Request $request)
    {
        $user_id = Auth::user()->id;

        try {
            $user = User::findOrFail($user_id);
            $activation_code = $this->generateActivationCode(6);
            if ($user->resend_number == 0) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.excced_number_of_send_activation_code')
                ]);
            }
            $resend = Mobily::send($user->mobile, 'Your activation code: ' . $activation_code);
            if ($resend) {
                $user->activation_code = $activation_code;
                $user->password = bcrypt($activation_code);
                $user->resend_number = $user->resend_number-1;
                $user->save();
                return response()->json([
                    'message' => __('messages.send_activiation_code'),
                    'status' => true,
                    'items' => $user
                ]);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => __('messages.not_exist_user'),
                'status' => true
            ]);
        }
    }

    /**
     * function to reset user mobile number
     * 
     * @param  Request $request
     * 
     * @return  response 
     */
    public function resetMobileNumber(Request $request)
    {   
        //HttpRequest::merge(['new_mobile_number' => $this->formatMobileNumber($request->new_mobile_number)]);
        $rules = [
            'activation_code' => 'required',
            'new_mobile_number' => 'required'
        ];

        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }


        $user_info = User::where('activation_code', '=', $request->activation_code)->first();
        if (empty($user_info)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.not_exist_user')
            ]);
        }
        try {
            $user = User::findOrFail($user_info->id);
            if ($user->mobile == $request->new_mobile_number) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.user_exist_msg'),
                ]);
            }
            //$mobile_number = $this->formatMobileNumber($request->new_mobile_number);
            if ($user->activation_code == $request->activation_code) {
                $user->mobile = $request->new_mobile_number;
                $user->save();
                // generate the access token 
                
                $tokenResult = $user->createToken('password');
                $token = $tokenResult->token;                
                return response()->json([
                    'status' => true,
                    'items' => $user,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'message' => __('messages.successfully_update_mobile_number')
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.not_exist_user')
                ]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg')
            ]);
        }
    }

    /**
     * function to search for contacts by filter
     * 
     * @param Request $request
     * 
     * @return  response
     */
    public function searchForContact(Request $request)
    {
        $country = $request->country;
        $region = $request->region;
        $activity_name = $request->activity_name;
        $organisation_name = $request->organisation_name;
        $gender = $request->gender;
        $mobile_number = $request->mobile_number;

        $users_list = [];
        if ($mobile_number) {
            $user = User::where('status', '=', 'active')
                ->where('mobile', '=', $mobile_number)
                ->get();
            return response()->json([
                'items' => $user,
                'message' => __('messages.fetch_data_msg'),
                'status' => true
            ]);    
        } else {
            $user = User::where('status', '=', 'active');
            if ($country) {
                $user = $user->where('country', '=', $country);
            }
            if ($region) {
                $user = $user->where('region', '=', $region);
            }
            if ($activity_name) {
                $user = $user->where('activity_name', '=', $activity_name);
            }
            if ($organisation_name) {
                $user = $user->where('organisation_name', '=', $organisation_name);
            }
            if ($gender) {
                $user = $user->where('gender', '=', $gender);
            }

            $user = $user->get();
            return response()->json([
                'items' => $user,
                'message' => __('messages.fetch_data_msg'),
                'status' => true
            ]);  
        }

    }

    /**
     * function to accept or reject group invitaion by user
     * 
     * @param  Request $request
     * 
     * @return response
     */
    public function userAcceptOrRejectGroup(Request $request)
    {
        $status = $request->status;
        $group_id = $request->group_id;
        $user_id = Auth::user()->id;

        $user_group = UserGroup::where('user_id', '=', $user_id)
            ->where('group_id', '=', $group_id)
            ->first();
        if (!empty($user_group)) {
            if ($status == "accept") {
            
            try {
                $update_user_group = UserGroup::findOrFail($user_group->id);
                $update_user_group->status = "accept";
                $update_user_group->save();
                return response()->json([
                    'status' => true,
                    'items' => $user_group,
                    'message' => __('messages.accept_join_group')
                ]);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.error_msg'),
                ]);
            }

            
        } else {
            try {
                $update_user_group = UserGroup::findOrFail($user_group->id);
                $update_user_group->status = "reject";
                $update_user_group->save();
                return response()->json([
                    'status' => true,
                    'message' => __('messages.reject_join_group')
                ]);
            } catch (ModelNotFoundException $e) {
                return response()->json([
                    'ststus' => false,
                    'message' => __('messages.error_msg'),
                ]);
            }
        }
        } else {
            return response()->json([
                'ststus' => false,
                'message' => __('messages.request_not_exist')
            ]);
        }
    }

    /**
     * function to format phone number
     * 
     * @param  String $mobile_number
     * 
     * @return  string
     */
    public function formatMobileNumber($mobile_number)
    {
        if ($mobile_number[0] == "+") {
            $mobile_number = str_replace($mobile_number[0], "00", $mobile_number);
        }
        return $mobile_number;
    }

    /**
     * function to remove null's from collection value 
     * 
     * @param  object
     * 
     * @return  object
     */ 
    private function array_remove_null($item)
    {
        $attr = $item['fillable'];
        foreach ($attr as $key => $value) {
            if (is_null($item[$value])) {
                $item[$value] = "";
            }
        }
        return $item;
    }

    /**
     * function to push data to realtime database
     * 
     * @param object $message
     * 
     * @return  response
     */
    public function storeInFireBase($user)
    {   
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/ammem-a0240-385b3d3ec166.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://ammem-a0240.firebaseio.com/')
            ->create();

        $database = $firebase->getDatabase();
        $newMsg = $database
            ->getReference('users/'.$user->mobile)
            ->set($user->mobile);
    }

    /**
     * function to send activation code for the user
     * 
     * @param Request $request
     * 
     * @return  response
     */
    public function sendActivationCode(Request $request)
    {
      //HttpRequest::merge(['mobile' => $this->formatMobileNumber($request->mobile)]);
      $exist_mobile = User::where('mobile', '=', $request->mobile)->first();
      if (empty($exist_mobile)) {
          return response()->json([
              'status' => false,
              'message' => __('messages.not_exist_user')
          ]);
      } else {
          $exist_mobile = User::where('mobile', '=', $request->mobile)->first();
          $activation_code = $this->generateActivationCode(6);
          $resend = Mobily::send($request->mobile, 'Your activation code: ' . $activation_code);
          if ($resend) {
              $user = User::where('mobile', '=', $request->mobile)->update([
                'activation_code' => $activation_code,
                'password' => bcrypt($activation_code),
                'resend_number' => $exist_mobile->resend_number-1
              ]);
              return response()->json([
                  'message' => __('messages.resend_activiation_code'),
                  'activation_code' => $activation_code,
                  'status' => true
              ]);
          }
    }
  }

  /**
  * function to reset user account data
  * 
  */
  public function reset (Request $request) 
  {     
        $mobile = $request->mobile_number;
        $user = User::where('mobile', '=', $mobile)->delete();
        return response()->json($user);
  }

  /**
   * function to send sms message
   * 
   */
  public function testSMS()
  { 
      echo Mobily::send('+966557287888', 'hello from Ammem');
  }


      /**
     * function to test notification 
     * 
     * 
     */
    public function testNotification()
    {   
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder(__('Test Notification'));
        $notificationBuilder->setBody(__('Hello from ammem app'))
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'name' => 'Hello'
        ]);
        
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();


        
        $downstreamResponse = FCM::sendTo('dWNW43tY5EY:APA91bGgsVIfQ_yf9KLyq-E6aU_IqtDUHYc_34O6wBuR9r71f_lAWO93-1PUCt-fUPmdcDWvU2v_-4elZ-bQMpa00KEosK71iPmWXSdAUI0naxkowTIQMvSufKTf8VAyWes-UAXk7cvY', $option, $notification, $data);
        
        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
    }
}
