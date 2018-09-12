<?php

namespace App\Http\Controllers\Api\V1;

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

class UserController extends Controller
{
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
        $rules = [
            'mobile' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $user = User::where('mobile', $request->get('mobile'))->first();
        if (isset($user)) {
            $activation_code = $this->generateActivationCode(5);
            $resend = Mobily::send($request->get('mobile'), 'Your activation code: ' . $activation_code);
            if ($resend) {
                $user->activation_code = $activation_code;
                $user->password = bcrypt($activation_code);
                $user->save();
                return response()->json([
                    'message' => $user,
                    'status' => true
                ]);
            }
        }
        return response()->json([
            'message' => 'حدث خطأما يرجى المحاولة مرة اخرى',
            'status' => false,
        ]);
    }

    // add new user and send activation code
    public function postUser(Request $request)
    {   
        $rules = [
            'mobile' => 'required|unique:users,mobile',
            'email' => 'sometimes|unique:users'
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
        }
        $activation_code = $this->generateActivationCode(6);
        Mobily::send($request->get('mobile'), 'Your activation code: ' . $activation_code);
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
            Mobily::send($request->get('mobile'), 'Your activation code: ' . $activation_code);
            return response()->json([
                'message' => $user,
                'status' => true
            ]);
        }
        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);
    }

    // complete user profile
    public function putUser(Request $request)
    {
        $rules = [
            'name' => 'required',
            'region' => 'required',
            'activity_name' => 'sometimes',
            'mobile' => 'sometimes|unique:users,mobile',
            'organisation_name' => 'sometimes',
            'activity_id' => 'sometimes|exists:activities,id',
            'organization_id' => 'required|exists:organizations,id',
            'interest_id' => 'required|exists:interests,id',
            'gender' => 'required|in:male,female',
            'job_id' => 'required|exists:jobs,id',
            'bod' => 'required',
            'city' => 'required',
        ];
        if ($request->has('mobile')) {
            $rules['old_activation_code'] = 'required';
        }

        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
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

        if ($request->has('mobile') && auth()->user()->activation_code == $request->get('old_activation_code')){

            $activation_code = $this->generateActivationCode(5);
            $resend = Mobily::send($request->get('mobile'), 'Your activation code: ' . $activation_code);
            if ($resend) {
                auth()->user()->mobile = $request->get('mobile');
                auth()->user()->is_confirm = 0;

                auth()->user()->activation_code = $activation_code;
                auth()->user()->password = bcrypt($activation_code);
            }
        }
        if (auth()->user()->save()) {
            $user = User::find(auth()->user()->id);
            return response()->json([
                'user' => $user,
                'status' => true
            ]);
        }
        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);
    }

    // activation by mobile
    public function confirmActivationCode(Request $request)
    {
        $rules = [
            'mobile' => 'required|exists:users,mobile',
            'activation_code' => 'required'
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
        }
        $user = User::where('mobile', $request->get('mobile'))->where('activation_code', $request->get('activation_code'))->first();
        if (!isset($user)) {
            return response()->json([
                'message' => 'عذرا المستخدم غير متوفر',
                'status' => false
            ]);
        }
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
            'user' => $user,
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
            return $validator;
        }

        $contacts = $request->get('contacts');
        $contacts = substr($contacts, 1, strlen($contacts) - 2);
        $contacts = explode(',', $contacts);
        $contacts = array_unique($contacts);
        $users = [];
        foreach ($contacts as $contact) {
            $user = User::where('mobile', $contact)->first();
            if (isset($user))
                $users[] = $user;
        }
        return response()->json([
            'users' => $users,
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
        $groups_id = Group::where('admin_status', 'accept')->whereIn('id', $groups_id)->pluck('id')->toArray();
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
            'user_info' => $user_info,
            'status' => true
        ]);
    } 

    public function test()
    {
        return "test";
    }
}