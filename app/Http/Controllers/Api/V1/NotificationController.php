<?php

namespace App\Http\Controllers\Api\V1;

use App\DeviceToken;
use App\Http\Controllers\Controller;
use App\Notification;
use App\NotificationReceiver;
use DB;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;

class NotificationController extends Controller
{
    //
    //device token for notification
    public function postDevice(Request $request)
    {
        $rules = [
            'device_token' => 'required',
//            'device_id' => 'required',
            'type' => 'required|in:1,2,3',
            'status' => 'sometimes|in:on,off',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }

        $device = DeviceToken::where('user_id', auth()->user()->id)->where('device_token', $request->get('device_token'))->first();
        if (empty($device)) {
            $device = new DeviceToken();
            $device->user_id = auth()->user()->id;
            $device->device_token = $request->get('device_token');
//          $device->device_id = $request->get('device_id');
            $device->type = $request->get('type'); // 1 => IOS ,2 => ANDROID
            if ($request->has('status'))// 0 => off, 1 => on
            {
                $device->status = $request->get('status');
                if ($request->get('status') == 'off') {
                    $value = $request->bearerToken();
                    $id = (new Parser())->parse($value)->getHeader('jti');
                    $token = DB::table('oauth_access_tokens')
                        ->where('id', '=', $id)
                        ->update(['revoked' => true]);
                }
            }
            $device->save();
            return response()->json([
                'status' => true,
                'items' => $device,
                'message' => __('messages.successfully_done')
            ]);
        }  else {
            return response()->json([
                'status' => true,
                'message' => __('messages.successfully_done'),
                'items' => $device
            ]);
        }   
    }

    public function postNotify(Request $request)
    {
//        enum('block_user', 'admin', 'end_duration')
        $this->validate($request, [
            'receiver_id' => 'required|exists:users,id',
            'action' => 'required|in:block_user,admin,end_duration,group,message,reply',
            'action_id' => 'required',
        ]);
        $this->sendNotification(auth()->user()->id, $request->get('receiver_id'), $request->get('action'), $request->get('action_id'));
    }

    public function getNotification()
    {

        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $notifications_id = NotificationReceiver::where('receiver_id', auth()->user()->id)->pluck('notification_id');
        $my_notifications_collection = Notification::whereIn('id', $notifications_id);

        $my_notifications_count = $my_notifications_collection->count();
        $my_notifications = $my_notifications_collection->orderBy('created_at', 'desc')->get();

        return response()->json([
            'items' => $my_notifications,
            'message' => __('messages.fetch_data_msg'),
            'status' => true
        ]);
    }

    public function getStatus($token)
    {
        $device = DeviceToken::where('device_token', $token)->first();
        if (!empty($device)) {
            return response()->json([
                'status' => true,
                'messages' => __('messages.fetch_data_msg'),
                'items' => $device
            ]);
        }
    }
}
