<?php

namespace App\Http\Controllers;

use App\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use View;
class NotificationController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');

    }
    //
    public function sendPublicNotification(){

        $data = [
            'title' => 'اشعار',
            'sub_title' => 'ارسال اشعار عام',
        ];
        return view(admin_public_notification_vw() . '.view', $data);
    }

    public function getSendPubNotification(){

        $view = View::make(admin_vw() . '.modal', [
            'modal_id' => 'send-public-notification',
            'modal_title' => 'ارسال اشعار عام',
            'action' => 'اضافة',
            'form' => [
                'method' => 'POST',
                'url' => url(admin_vw() . '/send-public-notification'),
                'form_id' => 'formAdd',
                'fields' => [
                    'text' => 'textarea',
                    ],
                'fields_ar' => [
                    'text' => 'نص الرسالة',
                ]
            ]
        ]);

        $html = $view->render();

        return $html;
    }

    public function postSendPubNotification(Request $request)
    {
        $rules = [
            'text' => 'required',
        ];

        $new_attr_name = [
            'text' => 'نص الرسالة',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $is_send = $this->sendNotification(null,null,null,'admin',null,$request->get('text'));

        return response_api($is_send);
    }

    public function publicNotificationData()
    {

        $num = 1;
        $query = Notification::where('action','admin')->orderByDesc('id');
        return datatables()->of($query)
            ->addColumn('num', function () use (&$num) {
                return $num++;
            })
            ->toJson();
    }
}
