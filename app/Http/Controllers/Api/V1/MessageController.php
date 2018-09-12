<?php

namespace App\Http\Controllers\Api\V1;

use App\Group;
use App\Http\Controllers\Controller;
use App\Media;
use App\Message;
use App\MessageGroup;
use App\Reply;
use App\Survey;
use App\SurveyResult;
use App\User;
use App\UserGroup;
use App\UserMessage;
use App\UserMessageSeen;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //add new message that you want to share in group/s
    public function postMessage(Request $request)
    {

        if (!$request->has('draft'))
            $rules = [
                'title' => 'required',
                'text' => 'required',
                'pin' => 'required|in:0,1',
                'is_reply' => 'required|in:0,1',
                'type' => 'required|in:message,message_survey',
                'group_id' => 'required', //|exists:groups,id
                'member_id' => 'required', //|exists:users,id
                'media_id' => 'sometimes|exists:media,id',
                'survey' => 'required_if:type,message_survey',
            ];
        else
            $rules = [
                'draft' => 'required|in:0,1'
            ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $message = null;

        $groups_data_id = [];

        $free_group = false;

        if ($request->has('group_id')) {
            $group_ids = $request->get('group_id');

            $group_ids = substr($group_ids, 1, strlen($group_ids) - 2);
            $group_ids = explode(',', $group_ids);

            foreach ($group_ids as $group_id) {
                $group = Group::find((int)$group_id);
                if ($group->type_id == 3) {
                    $free_group = true;
                }
                if ($group->status == 'closed') {
                    if ($group->user_id == auth()->user()->id) {
                        $groups_data_id[] = (int)$group_id;

                    }
                } else {
                    $user_group = UserGroup::where('user_id', auth()->user()->id)->whereIn('group_id', (int)$group_id)->first();
                    if (isset($user_group) || $group->user_id == auth()->user()->id) {
                        $groups_data_id[] = (int)$group_id;
                    }
                }
            }

        }
        if (count($groups_data_id) > 0 || ($request->has('draft') && ($request->get('draft') == 1))) {

            $message_pin_count = Message::where('user_id', auth()->user()->id)->where('pin', 1)->count();
            $message = new Message();

            if ($request->has('title'))
                $message->title = $request->get('title');
            if ($request->has('text'))
                $message->text = $request->get('text');

            if ($request->has('link'))
                $message->link = $request->get('link');
            if ($request->has('address'))
                $message->address = $request->get('address');
            if ($request->has('latitude'))
                $message->latitude = $request->get('latitude');
            if ($request->has('longitude'))
                $message->longitude = $request->get('longitude');

            if ($request->has('is_copy'))
                $message->is_copy = $request->get('is_copy');
            if ($request->has('draft'))
                $message->draft = $request->get('draft');
            if ($request->has('pin') && $message_pin_count <= 12)
                $message->pin = $request->get('pin');
            if ($request->has('is_reply'))
                $message->is_reply = $request->get('is_reply');

            $message->user_id = auth()->user()->id;
            if ($request->has('type'))
                $message->type = $request->get('type');
            if ($message->save()) {
                if (count($groups_data_id) > 0)
                    foreach ($groups_data_id as $group_id) {
                        $message_group = new MessageGroup();
                        $message_group->message_id = $message->id;
                        $message_group->group_id = $group_id;
                        $message_group->save();
                    }

                if ($request->has('member_id')) {
                    $members = $request->get('member_id');
                    $members = substr($members, 1, strlen($members) - 2);
                    $members = explode(',', $members);

                    foreach ($members as $member_id) {
                        $user_message = new UserMessage();
                        $user_message->message_id = $message->id;
                        $user_message->user_id = (int)$member_id;
                        $user_message->save();

                        $this->sendNotification(auth()->user()->id, (int)$member_id, $message->id, 'message', null, 'تعميم جديدة');

                    }
                }


                if ($request->has('media_id')) {

                    $media_ids = $request->get('media_id');
                    $media_ids = substr($media_ids, 1, strlen($media_ids) - 2);
                    $media_ids = explode(',', $media_ids);
                    $count_media = 0;
                    foreach ($media_ids as $media_id) {
                        $media = Media::find((int)$media_id);
                        $media->message_id = $message->id;
                        $media->save();
                        $count_media++;
                        if ($count_media == 1 && $free_group) {
                            break;
                        }
                    }
                }
                if ($request->has('type') && $request->get('type') == 'message_survey') {

                    $surveys = $request->get('survey');
                    //$surveys = substr($surveys, 1, strlen($surveys) - 2);
                    //$surveys = explode(',', $surveys);

                    if ($request->has('survey'))
                        foreach ($surveys as $survey_name) {
                            $survey = new Survey();
                            $survey->name = $survey_name;
                            $survey->message_id = $message->id;
                            $survey->save();
                        }
                }

                $message = Message::find($message->id);
                return response()->json([
                    'message' => $message,
                    'status' => true
                ]);
            }
        }
        return response()->json([
            'status' => true
        ]);
    }

    //update message
    //add new message that you want to share in group/s
    public function putMessage(Request $request)
    {

        if (!$request->has('draft'))
            $rules = [
                'title' => 'sometimes',
                'text' => 'sometimes',
                'pin' => 'sometimes',
                'is_reply' => 'sometimes',
                'type' => 'sometimes',
                'group_id' => 'sometimes', //|exists:groups,id
                'member_id' => 'sometimes', //|exists:users,id
                'media_id' => 'sometimes',
                'survey' => 'required_if:type,message_survey',
                'message_id' => 'required|exists:messages,id'
            ];
        else
            $rules = [
                'draft' => 'required|in:0,1'
            ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $message = null;

        $groups_data_id = [];

        if ($request->has('group_id')) {
            $group_ids = $request->get('group_id');

            $group_ids = substr($group_ids, 1, strlen($group_ids) - 2);
            $group_ids = explode(',', $group_ids);

            foreach ($group_ids as $group_id) {
                $group = Group::find((int)$group_id);

                if ($group->status == 'closed') {
                    if ($group->user_id == auth()->user()->id) {
                        $groups_data_id[] = (int)$group_id;

                    }
                } else {
                    $user_group = UserGroup::where('user_id', auth()->user()->id)->whereIn('group_id', (int)$group_id)->first();
                    if (isset($user_group) || $group->user_id == auth()->user()->id) {
                        $groups_data_id[] = (int)$group_id;
                    }
                }
            }

        }
        $message_id = $request->message_id;
        $message_pin_count = Message::where('user_id', auth()->user()->id)->where('pin', 1)->count();

        $message = Message::where('user_id', auth()->user()->id)->find($message_id);
        if (isset($message)) {
            if ($request->has('title'))
                $message->title = $request->get('title');
            if ($request->has('text'))
                $message->text = $request->get('text');

            if ($request->has('link'))
                $message->link = $request->get('link');
            if ($request->has('address'))
                $message->address = $request->get('address');
            if ($request->has('latitude'))
                $message->latitude = $request->get('latitude');
            if ($request->has('longitude'))
                $message->longitude = $request->get('longitude');

            if ($request->has('is_copy'))
                $message->is_copy = $request->get('is_copy');
            if ($request->has('draft'))
                $message->draft = $request->get('draft');
            if ($request->has('pin') && $message_pin_count <= 12)
                $message->pin = $request->get('pin');
            if ($request->has('is_reply'))
                $message->is_reply = $request->get('is_reply');

            $message->user_id = auth()->user()->id;
            if ($request->has('type'))
                $message->type = $request->get('type');
            if ($message->save()) {
                if (count($groups_data_id) > 0)
                    foreach ($groups_data_id as $group_id) {
                        $message_group = new MessageGroup();
                        $message_group->message_id = $message->id;
                        $message_group->group_id = $group_id;
                        $message_group->save();
                    }

                if ($request->has('member_id')) {
                    $members = $request->get('member_id');
                    $members = substr($members, 1, strlen($members) - 2);
                    $members = explode(',', $members);

                    foreach ($members as $member_id) {
                        $user_message = new UserMessage();
                        $user_message->message_id = $message->id;
                        $user_message->user_id = (int)$member_id;
                        $user_message->save();

                        $this->sendNotification(auth()->user()->id, (int)$member_id, $message->id, 'message', null, 'تعميم جديدة');

                    }
                }


                if ($request->has('media_id')) {
                    $media_ids = $request->get('media_id');
                    $media_ids = substr($media_ids, 1, strlen($media_ids) - 2);
                    $media_ids = explode(',', $media_ids);

                    foreach ($media_ids as $media_id) {
                        $media = Media::find((int)$media_id);
                        $media->message_id = $message->id;
                        $media->save();
                    }
                }
                if ($request->has('type') && $request->get('type') == 'message_survey') {

                    $surveys = $request->get('survey');
                    //$surveys = substr($surveys, 1, strlen($surveys) - 2);
                    //$surveys = explode(',', $surveys);
                    if ($request->has('survey'))
                        foreach ($surveys as $survey_name) {
                            $survey = new Survey();
                            $survey->name = $survey_name;
                            $survey->message_id = $message->id;
                            $survey->save();
                        }
                }

                $message = Message::find($message->id);
                return response()->json([
                    'message' => $message,
                    'status' => true
                ]);
            }
        }

        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);
    }

    //get messages
    public function getMessages()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $messages_collection = Message::where('user_id', auth()->user()->id)->where('is_archived', 0)->where('is_draft', 0);
        $messages_count = $messages_collection->count();
        $messages = $messages_collection->orderBy('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'messages' => $messages,
            'status' => true
        ]);


    }

    //get message by id
    public function getMessage($id)
    {
        $message = Message::find($id);

        if (isset($message)) {
            $replies = Reply::where('message_id', $message->id);
            if ($message->user_id == auth()->user()->id) {
                $message->replies = $replies->get();
            } else
                $message->replies = $replies->where('user_id', auth()->user()->id)->get();

            $message_user_seen = UserMessageSeen::where('user_id', auth()->user()->id)->where('message_id', $id)->first();
            if (!isset($message_user_seen)) {

                $message_user_seen = new UserMessageSeen();
                $message_user_seen->user_id = auth()->user()->id;
                $message_user_seen->message_id = $id;
                $message_user_seen->save();
            }
            return response()->json([
                'messages' => $message,
                'status' => true
            ]);
        }
        return response()->json([
            'messages' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => true
        ]);
    }

    //choice result survey
    public function postChoiceSurveyResult(Request $request)
    {
        $rules = [
            'choice_id' => 'required|exists:surveys,id',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $message_id = $request->message_id;
        $user_message = UserMessage::where('user_id', auth()->user()->id)->where('message_id', $message_id)->first();

        if (isset($user_message)) {
            $message = Message::where('type', 'message_survey')->where('is_archived', 0)->find($message_id);
            if (isset($message)) {
                $result = SurveyResult::where('user_id', auth()->user()->id)->where('message_id', $message_id)->first();

                if (!isset($result))
                    $result = new SurveyResult();

                $result->message_id = $message_id;
                $result->choice_id = $request->get('choice_id');
                $result->user_id = auth()->user()->id;
                $result->save();
                return response()->json([
                    'user' => $result,
                    'status' => true
                ]);
            }
        }
        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);
    }

    // create new reply to message
    public function postReply(Request $request)
    {

        // `message_id`, `user_id`, `text`
        $rules = [
            'text' => 'required',
            'message_id' => 'required'
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }
        $message_id = $request->message_id;
        $text = $request->text;
        $user_id = auth()->user()->id;
        $user_message = UserMessage::where('message_id', $message_id)
            ->where('user_id', '=', $user_id)
            ->first();

        if (isset($user_message)) {
            $message = Message::where('is_reply', 1)->where('is_archived', 0)->find($message_id);
            $message_group = MessageGroup::where('message_id', $message_id)->first();
            if (isset($message_group))
                $group = Group::find($message_group->group_id);
            if (isset($message)) {
                $reply = new Reply();
                $reply->message_id = $message_id;
                $reply->user_id = $user_id;
                $reply->text = $request->get('text');
                if ($reply->save()) {
                    $data['message'] = $reply;

                    $this->sendNotification(auth()->user()->id, $message->user_id, $reply->id, 'reply', null, 'رد جديدة');
                    $this->pusher->trigger('my-channel' . $group->slug, 'my-event', $data);
                    return response()->json([
                        'replay' => $reply,
                        'status' => true
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);
    }

    // create new media
    public function postMedia(Request $request)
    {   
        $rules = [
            'media' => 'required',
            'type' => 'required|in:image,audio,video,link,file'
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $media = new Media();
        $media->name = $this->upload($request, 'media');
        $media->type = $request->get('type');
        return response()->json([
            'media' => $media,
            'status' => true
        ]);
    }

    // get media for user or group (type)
    public function getMedia($media_id = null)
    {
        if ($media_id == null) {
            $media = Media::all();
        } else {
            $media = Media::where('id', '=', $media_id)->get();
        }
        /*if (isset($_GET['type']) && ($_GET['type'] == 'user' || $_GET['type'] == 'group')) {
            $paginate_num = 0;
            if (isset($_GET['page']) && intval($_GET['page']) > 0) {
                $paginate_num = intval($_GET['page']);
            }
            $message_ids = [];
            if ($_GET['type'] == 'user') {

                if (!isset($action_id))
                    $action_id = auth()->user()->id;
                $message_ids = Message::where('user_id', $action_id)->where('is_archived', 0)->pluck('id');
            } else if (isset($action_id)) {
                $message_ids = MessageGroup::where('group_id', $action_id)->pluck('message_id');
            }

            $media_collection = Media::whereIn('message_id', $message_ids);
            $media_count = $media_collection->count();
            $media = $media_collection->orderBy('created_at', 'DESC')->get();*/

            return response()->json([
                'media' => $media,
                'status' => true
            ]);
/*        }

        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);*/
    }

    // search for user, message or group (type)
    public function postSearch(Request $request)
    {
        $rules = [
            'text' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }
        if (isset($request->type) && ($request->type == 'user' || $request->type == 'message' || $request->type == 'group')) {

            if ($request->type == 'user') {
                $search = User::where('name', 'LIKE', '%' . $request->get('text') . '%')->get();
            } else if ($request->type == 'message') {
                $search = Message::where('title', 'LIKE', '%' . $request->get('text') . '%')->where('is_archived', 0)->get();
            } else {
                $search = Group::where('name', 'LIKE', '%' . $request->get('text') . '%')->get();

            }
            return response()->json([
                'search' => $search,
                'status' => true
            ]);
        }

        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);
    }

    // get archived own messages
    public function getArchive()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $messages_collection = Message::where('user_id', auth()->user()->id)->where('is_archived', 1);
        $messages_count = $messages_collection->count();
        $messages = $messages_collection->orderBy('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'messages' => $messages,
            'status' => true
        ]);

    }

    // move message to archive
    public function postArchive(Request $request)
    {

        $rules = [
            'message_id' => 'required|exists:messages,id',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }

        $exist_message = Message::where('user_id', auth()->user()->id)
            ->where('id', '=', $request->get('message_id'))
            ->where('is_archived', 0)->first();
        if (!empty($exist_message)) {

            $message = Message::where('user_id', auth()->user()->id)
            ->where('id', '=', $request->get('message_id'))
            ->update([
                'is_archived' => 1
            ]);
            return response()->json([
                'message' => $exist_message,
                'status' => true
            ]);

        }
        return response()->json([
            'message' => 'حدث خطأ ما حاول مرة اخرى',
            'status' => false
        ]);

    }
    // get draft own messages
    public function getDraft()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $messages_collection = Message::where('user_id', auth()->user()->id)->where('is_draft', 1);
        $messages_count = $messages_collection->count();
        $messages = $messages_collection->orderBy('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'messages' => $messages,
            'status' => true
        ]);

    }

    // move message to draft
    public function postDraft(Request $request)
    {

        $rules = [
            'message_id' => 'required|exists:messages,id',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return $validator;
        }
        $message = Message::where('user_id', auth()->user()->id)
            ->where('is_archived', '=', 0)->find($request->get('message_id'));
        if (isset($message)) {

            $message->is_draft = 1;
            $message->save();
            return response()->json([
                'message' => $message,
                'status' => true
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'هذه المجموعة غير متوفرة',
        ]);

    }

    // get archived own messages
    public function getUserMessageSeen($message_id)
    {
        $paginate_num = 0;
        $users_id = UserMessageSeen::where('message_id', $message_id)->pluck('user_id');
        $users_seen_collection = User::whereIn('id', $users_id);
        $users_seen_count = $users_seen_collection->count();
        $users = $users_seen_collection->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'user_seen_messages' => $users,
            'status' => true,
        ]);

    }
}