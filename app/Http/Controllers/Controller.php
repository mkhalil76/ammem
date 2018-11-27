<?php

namespace App\Http\Controllers;

use App\DeviceToken;
use App\Notification;
use App\NotificationReceiver;
use FCM;
use File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Pusher\Pusher;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public $pusher;
    private $_apiContext;
    public function __construct()
    {

        \app()->setLocale(app('request')->header('Accept-Language'));

        $options = array(
            'encrypted' => true
        );
        $this->pusher = new Pusher(
            '6905ea279b00cd9932b7',
            '23310e66f6e12e326a7f',
            '481565',
            $options
        );


//        $this->_apiContext = PayPal::ApiContext(
//            config('services.paypal.client_id'),
//            config('services.paypal.secret'));
//
//        $this->_apiContext->setConfig(array(
//            'mode' => 'sandbox',
//            'service.EndPoint' => 'https://api.sandbox.paypal.com',
//            'http.ConnectionTimeOut' => 30,
//            'log.LogEnabled' => true,
//            'log.FileName' => storage_path('logs/paypal.log'),
//            'log.LogLevel' => 'FINE'
//        ));
    }

    protected function formatValidationErrors(Validator $validator)
    {
        $arr = array();
        $errors = [];
        $messages = $validator->errors()->toArray();

        foreach ($messages as $key => $row) {
            $errors['fieldname'] = $key;
            $errors['message'] = $row[0];
            $arr[] = $errors;
        }
        return response()->json([
            'sucess' => false,
            'message' => $arr
        ]);
    }

    public function validateService($validator)
    {
        $arr = array();
        $errors = [];
        $messages = $validator->errors()->toArray();
        foreach ($messages as $key => $row) {
            $errors['fieldname'] = $key;
            $errors['message'] = $row[0];
            $arr[] = $errors;
        }

        return response()->json([
            'sucess' => false,
            'message' => $arr,
            'status' => false
        ]);
    }

    public function makeValidation($request, $rules, $nice_name = null)
    {
        $validator = Validator::make($request->all(),
            $rules);
        if (isset($nice_name))
            $validator->setAttributeNames($nice_name);
        if ($validator->fails()) {
            return $this->validateService($validator);
        }
        return response()->json([
            'sucess' => true,
            'status' => true
        ]);
    }

    //Our generate activation code function.
    public function generateActivationCode($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    //upload file
    public function upload($request, $input_name)
    {
        $temp = time() . rand(5, 50);
        $ext = $request->file($input_name)->getClientOriginalExtension();
        $new_file_name = $temp . '.' . $ext;
        $path = public_path().'/public/assets/upload';
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $uploaded = $request->file($input_name)->move($path, $new_file_name);

        if ($uploaded)
            return $new_file_name;
        return '';
    }

    public function sendNotification($sender_id, $receiver_id, $action_id, $action, $request_action = null, $text = null) //$object
    {

        if ($action == 'admin') {//where('status', 'on')->
            $tokens = DeviceToken::select('device_token', 'user_id')->get();

            if (count($tokens) > 0) {
                $notification = new Notification;
                $notification->sender_id = $sender_id;
                $notification->action = $action;
                $notification->action_id = $action_id;
                $notification->text = $text;

                if ($notification->save()) {

                    foreach ($tokens as $token) {
                        $receiver_notification = new NotificationReceiver;
                        $receiver_notification->notification_id = $notification->id;
                        $receiver_notification->receiver_id = $token->user_id;
                        $receiver_notification->save();
                    }
                    $this->FCM('Ammem', auth()->user()->name, $notification, $tokens->pluck('device_token')->toArray());

                    return true;
                }
                return false;
            }
        } else {
            if ($sender_id != $receiver_id) {
                $tokens = DeviceToken::where('user_id', $receiver_id)->where('status', 'on')->pluck('device_token')->toArray();

                if (count($tokens) > 0) {
                    $notification = new Notification();
                    $notification->sender_id = $sender_id;
                    $notification->action = $action;
                    $notification->action_id = $action_id;
                    if ($notification->save()) {

                        $receiver_notification = new NotificationReceiver();
                        $receiver_notification->notification_id = $notification->id;
                        $receiver_notification->receiver_id = $receiver_id;
                        $receiver_notification->save();
                        $message = '';

                        $this->FCM('Ammem', auth()->user()->name . ' ' . $message, $notification, $tokens);
                        return true;
                    }
                    return false;
                }
            }
        }
    }

//    send notification
    public function FCM($title, $body, $data, $tokens)
    {   
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => $data]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // You must change it to get your tokens
        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens
        $object = [
            'numberSuccess' => $downstreamResponse->numberSuccess(),
            'numberFailure' => $downstreamResponse->numberFailure(),
            'numberModification' => $downstreamResponse->numberModification(),
        ];

        return $object;
//        return response_api($downstreamResponse->numberSuccess() > 0, $object);
    }

    //send sms messages
    public function sendSMS($message, $numbers)
    {

        $url = 'http://www.mursalat-sms.com/api/sendsms.php?username=dalah&password=050961&message=' . $message . '&numbers=' . $numbers . '&sender=muhannadapp&unicode=e&Rmduplicated=1&return=json';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        $data = json_decode($data, true);
        return $data['Code'];
    }


}
