<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Group;
use App\Media;
use App\Message;
use App\MessageGroup;
use App\Reply;
use App\Survey;
use App\User;
use App\UserGroup;
use App\UserMessage;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Mail;
use View;

class MessageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');

    }

    public function index()
    {
        $data = [
            'title' => 'التعميمات',
            'sub_title' => 'عرض التعميمات',
        ];
        return view(admin_messages_vw() . '.view', $data);
    }

    public function getData()
    {

        $num = 1;
        return datatables()->of(Message::query()->orderByDesc('updated_at'))
            ->addColumn('num', function () use (&$num) {
                return $num++;
            })->addColumn('sender', function ($m) {

                return $m->Sender->name;
            })->addColumn('group_name', function ($m) {
                $group = Group::find($m->group_id);
                if (isset($group))
                    return $group->name;
                return '';
            })->editColumn('type', function ($m) {

                if ($m->type == 'message') {
                    $m = 'رسالة';
                } else {
                    $m = 'رسالة و استبيان';
                }
                return $m;
            })->editColumn('created_at', function ($m) {

                return Carbon::parse($m->created_at)->format('d/m/Y');

            })->addColumn('action', function ($m) {

                $action = '<a href="' . url(admin_vw() . '/delete-message/' . $m->id) . '" class="btn btn-outline btn-circle btn-sm red delete">
                                                            <i class="fa fa-trash"></i> حذف </a>';

                return $action;
            })
            ->rawColumns(['members', 'action'])
            ->toJson();
    }

    public function getMessageConversation($group_id)
    {
        $group = Group::find($group_id);

        $my_messages_id = UserMessage::where('user_id', auth()->user()->id)->pluck('message_id')->toArray();

        $message_ids = MessageGroup::where('group_id', $group_id)->whereIn('message_id', $my_messages_id)->pluck('message_id');
        $messages = null;
        $replies = null;

        if (isset($message_ids) && !empty($message_ids)) {
            $messages = Message::whereIn('id', $message_ids)->orderByDesc('created_at')->get();

//            if ($message->user_id != auth()->user()->id)
//                $replies = Reply::where('user_id', auth()->user()->id)->whereIn('message_id', $message_group->message_id)->get();
//            else
//                $replies = Reply::whereIn('message_id', $message_group->message_id)->get();
        }

        $data = [
            'title' => 'التعميمات',
            'sub_title' => 'عرض التعميمات',
            'group' => $group,
            'messages' => $messages,
//            'replies' => $replies,
        ];
//        dd($replies);
        return view(admin_messages_vw() . '.conversation', $data);
    }

    // التعاميم
    public function getMessages()
    {

        $my_messages_id = UserMessage::where('user_id', auth()->user()->id)->pluck('message_id')->toArray();
        $messages = Message::whereIn('id', $my_messages_id)->orderByDesc('id')->get();
        $data = [
            'title' => 'التعاميم',
            'sub_title' => 'عرض التعاميم الخاص',
            'messages' => $messages,
        ];

        return view(admin_messages_vw() . '.messages', $data);
    }

// جهات الاتصال
    public function getContacts()
    {
        $my_contacts = Contact::where('user_id', auth()->user()->id)->orderByDesc('id')->get();
        $data = [
            'title' => 'جهات الاتصال',
            'sub_title' => 'جهات الاتصال الخاص',
            'my_contacts' => $my_contacts,
        ];

        return view(admin_users_vw() . '.contacts', $data);
    }

    public function postContacts(Request $request)
    {

        $rules = [
            'contacts' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $arr = [];
        if ($request->file('contacts')) {
            $path = $request->file('contacts')->getRealPath();
            $data = Excel::load($path, function ($reader) {
            })->get();

            if (!empty($data) && $data->count()) {
                $data = $data->toArray();

                for ($i = 0; $i < count($data); $i++) {

                    if (isset($data[$i]) && !empty($data[$i]['mobile'])) {
                        $is_exist = 0;
                        $user = User::where('mobile', $data[$i]['mobile'])->first();
                        if (isset($user)) {
                            $is_exist = 1;
                        } else {

                            //send an email to other user
                        }
                        $flag = false;

                        $contact = Contact::where('mobile', $data[$i]['mobile'])->first();
                        if (!isset($contact)) {
                            $flag = true;
                            $contact = new Contact();
                        }
                        $contact->is_exist = $is_exist;
                        $contact->name = $data[$i]['name'];
                        $contact->mobile = $data[$i]['mobile'];
                        $contact->email = $data[$i]['email'];
                        $contact->user_id = auth()->user()->id;
                        $contact->save();

                        if ($flag)
                            $arr[] = $contact;
//                        Mail::to($contact)->send(new Invitation($contact));

                    }
                }
                return response_api(true, $arr);
            }


        }
        return response_api(false);
    }

    public function getGroupConversation()
    {

        $my_groups_id = UserGroup::where('user_id', auth()->user()->id)->pluck('group_id')->toArray();
        $my_group_auth_id = Group::where('user_id', auth()->user()->id)->pluck('id')->toArray();

        $my_groups_id = array_merge($my_groups_id, $my_group_auth_id);

        $my_groups = Group::where('admin_status', 'accept')->whereIn('id', $my_groups_id)->get();
        $data = [
            'title' => 'مجموعاتي',
            'sub_title' => 'عرض المجموعاتي',
            'my_groups' => $my_groups,
        ];

        return view(admin_messages_vw() . '.my_groups', $data);
    }

    public function sendReplyMessage(Request $request, $message_id)
    {

        $rules = [
            'message' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $user_message = UserMessage::where('message_id', $message_id)->where('user_id', auth()->user()->id)->first();
//

//        dd($user_message);
        if (isset($user_message)) {
            $message = Message::where('is_reply', 1)->find($message_id);

            $message_group = MessageGroup::where('message_id', $message_id)->first();
            $group = Group::find($message_group->group_id);

            if (isset($message)) {

                $reply = new Reply();
                $reply->message_id = $message_id;
                $reply->user_id = auth()->user()->id;
                $reply->text = $request->get('message');

                $data = [];
                if ($reply->save()) {

//                    $data['type'] = 1;
                    $data['message_id'] = $reply->id;
                    $data['message'] = $reply->text;
                    $data['group_name'] = $group->name;
                    $data['is_reply'] = $message->is_reply;
                    if (isset($reply->user->photo))
                        $data['sender_photo'] = $reply->user->photo->name;
                    else
                        $data['sender_photo'] = '';
                    $data['sender_name'] = $reply->user->name;
                    $data['sender_id'] = $reply->user_id;
                    $data['created_date'] = $reply->created_date;
                    $this->pusher->trigger('my-channel' . $group->slug, 'my-event', $data);

                }

                return response_api(true);
            }
        }

        return response_api(false);

    }

    public function sendMessage(Request $request, $group_id)
    {

        $rules = [
            'title' => 'required',
            'text' => 'required',
            'pin' => 'required|in:0,1',
            'is_reply' => 'required|in:0,1',
            'type' => 'required|in:message,message_survey',
            'member_id' => 'required|exists:users,id',
            'media_id' => 'sometimes|exists:media,id',
            'survey' => 'required_if:type,message_survey',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }
        $group = Group::where('user_id', auth()->user()->id)->find($group_id);
        $message_to_users = [];
        if (isset($group)) {
            $message = new Message();
            $message->title = $request->get('title');
            $message->text = $request->get('text');
            if ($request->has('draft'))
                $message->draft = $request->get('draft');
            $message->pin = $request->get('pin');
            $message->is_reply = $request->get('is_reply');
            $message->user_id = auth()->user()->id;
            $message->type = $request->get('type');
            if ($message->save()) {
                $message_group = new MessageGroup();
                $message_group->message_id = $message->id;
                $message_group->group_id = $group_id;
                $message_group->save();
                //------------user auth message ----------
                $user_message = new UserMessage();
                $user_message->message_id = $message->id;
                $user_message->user_id = auth()->user()->id;
                $user_message->save();
//                $message_to_users[] = $user_message->user_id;
                foreach ($request->get('member_id') as $member_id) {
                    $user_message = new UserMessage();
                    $user_message->message_id = $message->id;
                    $user_message->user_id = $member_id;
                    $user_message->save();
                    $message_to_users[] = $user_message->user_id;

                    $this->sendNotification(auth()->user()->id, $member_id, $message->id, 'message', null, 'تعميم جديدة');

                }
                if ($request->has('media_id'))
                    foreach ($request->get('media_id') as $media_id) {
                        $media = Media::find($media_id);
                        $media->message_id = $message->id;
                        $media->save();
                    }
                if ($request->get('type') == 'message_survey')
                    foreach ($request->get('survey') as $survey_name) {
                        $survey = new Survey();
                        $survey->name = $survey_name;
                        $survey->message_id = $message->id;
                        $survey->save();
                    }

                $data['type'] = 1;
                $data['title'] = $message->title;
                $data['group_id'] = $message->groups->id;
                $data['group_name'] = $message->groups->name;
                $data['message_id'] = $message->id;
                $data['message'] = $message->text;
                $data['user_message'] = $message_to_users;
                $data['is_reply'] = $message->is_reply;

                if (isset($message->user->photo))
                    $data['sender_photo'] = $message->user->photo->name;
                else
                    $data['sender_photo'] = '';
                $data['sender_name'] = $message->user->name;
                $data['created_date'] = $message->created_date;


                $this->pusher->trigger('my-channel' . $group->slug, 'my-event', $data);

                return response_api(true, $message);
            }
        }
        return response_api(false);
    }


    public function getSendMessage($group_id)
    {

        $user_ids = UserGroup::where('group_id', $group_id)->pluck('user_id');
        $users = User::whereIn('id', $user_ids)->orderByDesc('updated_at')->get();

        $members = [];
        foreach ($users as $user) {
            $members[$user->id] = $user->name . ' - ' . $user->country;
        }
// `title`, `text`, `draft`, `pin`, `is_reply`, `user_id`, `type`,
        $view = View::make(admin_vw() . '.modal', [
            'modal_id' => 'addNewMessage',
            'modal_title' => 'انشاء تعميم جديدة',
            'action' => 'اضافة',
            'form' => [
                'method' => 'POST',
                'url' => url(user_vw() . '/send-message/' . $group_id),
                'form_id' => 'formAdd',
                'fields' => [
                    'title' => 'text',
                    'text' => 'text',
                    'draft' => 'text',
                    'pin' => ['غير مثبت', 'مثبت'],
                    'is_reply' => ['غير قابلة لرد', 'قابلة لرد'],
                    'type' => ['message' => 'تعميم', 'message_survey' => 'تعميم و استبيان'],
                    'member_id' => ['member_id' => $members, 'is_multiple' => true],

                ],
                'fields_ar' => [
                    'title' => 'عنوان',
                    'text' => 'النص',
                    'draft' => 'المسوده',
                    'pin' => 'قابلية التثبيت',
                    'is_reply' => 'قابلية الرد',
                    'type' => 'النوع',
                    'member_id' => 'اختيار الاعضاء',
                ]
            ]
        ]);

        $html = $view->render();

        return $html;
    }

    public function destroy($message_id)
    {

        $message = Message::find($message_id);

        return response_api(isset($message) && $message->delete());
    }

}
