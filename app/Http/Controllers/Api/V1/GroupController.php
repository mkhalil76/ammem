<?php

namespace App\Http\Controllers\Api\V1;

use App\Group;
use App\GroupType;
use App\Http\Controllers\Controller;
use App\UserGroup;
use Hash;
use Illuminate\Http\Request;
use Mobily;
use URL;
use App\groupBackground;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;

class GroupController extends Controller
{
    // add new group
    public function postGroup(Request $request)
    {
        $rules = [
            'name' => 'required',
            'details' => 'required',
            'image' => 'required|image',
            'type_id' => 'required|exists:group_types,id',
            'status' => 'required|in:closed,opened',
            'members' => 'required', //|exists:users,id
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }
        $password = $this->generateActivationCode(6);
        $group = new Group();

        $group->name = $request->get('name');
        $group->image = $this->upload($request, 'image');
        $group->details = $request->get('details');
        $group->type_id = $request->get('type_id');
        $group->status = $request->get('status');
        $group->user_id = auth()->user()->id;
        $group->password = $password;

        if ($group->type_id == 3) {
            $group->admin_status = 'accept';
        }
        if ($group->save()) {
            
            if ($request->get('status') == "closed") {
                $resend = Mobily::send(auth()->user()->mobile, 'Your group\'s (' . $request->get('name') . ') password: ' . $password);
            }
            $group_type = GroupType::find($group->type_id);

            $has_limited_number_member = ($group_type->id != 1) ?: false;
            // firstly add admin as member of group
            $user_group = new UserGroup();
            $user_group->user_id = auth()->user()->id;
            $user_group->group_id = $group->id;
            $user_group->status = 'accept';
            if ($request->has('is_notification'))
                $user_group->is_notification = $request->get('is_notification');
            $user_group->save();
            if ($request->has('members')) {

                $members = $request->get('members');
                $members = substr($members, 1, strlen($members) - 2);
                $members = explode(',', $members);
                $count = 0;
                foreach ($members as $member_id) {
                    try {
                        $user = User::findOrFail($member_id);
                        if ($has_limited_number_member && ++$count > $group_type->max_num_member) break;
                        $user_group = UserGroup::where('group_id', $group->id)->where('user_id', $member_id)->first();

                        if (!isset($user_group))
                            $user_group = new UserGroup();
                            $user_group->user_id = (int)$member_id;
                            $user_group->group_id = $group->id;

                        if ($request->has('is_notification'))
                            $user_group->is_notification = $request->get('is_notification');
                        $user_group->save();

                        $this->sendNotification(auth()->user()->id, (int)$member_id, $group->id, 'group', null, __('messages.new_group'));
                        $resend = Mobily::send($user->mobile, 'Your group\'s (' . $request->get('name') . ') password: ' . $password);
                } catch (ModelNotFoundException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => __('messages.member_not_exist')
                    ]);
                }

                }
                $group = Group::find($group->id);
                return response()->json([
                    'items' => $group,
                    'message' => __('messages.create_new_group'),
                    'status' => true
                ]);
            }
        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);
    }

    //edit group
    public function putGroup(Request $request)
    {
        $rules = [
            'name' => 'sometimes',
            'details' => 'sometimes',
            'image' => 'sometimes|image',
            'type_id' => 'sometimes',
            'status' => 'sometimes',
            'members' => 'sometimes', //|exists:users,id
            'group_id' => 'required'
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }
        $group_id = $request->group_id;
        $group = Group::where('user_id', auth()->user()->id)->find($group_id);
        if (isset($group)) {

            if ($request->has('name'))
                $group->name = $request->get('name');
            if ($request->hasFile('image'))
                $group->image = $this->upload($request, 'image');
            if ($request->has('details'))
                $group->details = $request->get('details');
            if ($request->has('type_id'))
                $group->type_id = $request->get('type_id');
            if ($request->has('status'))
                $group->status = $request->get('status');

            $group->user_id = auth()->user()->id;

            if ($group->type_id == 3) {
                $group->admin_status = 'accept';
            }
            if ($group->save()) {
                if ($request->has('members')) {
                    $group_type = GroupType::find($group->type_id);
                    $has_limited_number_member = ($group_type->id != 1) ?: false;
                    $members = $request->get('members');
                    $members = substr($members, 1, strlen($members) - 2);
                    $members = explode(',', $members);

                    $count = UserGroup::where('group_id', $group->id)->whereIn('user_id', $members)->count();

                    foreach ($members as $member_id) {

                        try {
                            $user = User::findOrFail($member_id);
                            if ($has_limited_number_member && $count >= $group_type->max_num_member) break;
                            $user_group = UserGroup::where('group_id', $group->id)->where('user_id', $member_id)->first();
                            if (!isset($user_group)) {
                                $user_group = new UserGroup();
                                $count++;
                            }
                            $user_group->user_id = (int)$member_id;
                            $user_group->group_id = $group->id;
                            if ($request->has('is_notification'))
                                $user_group->is_notification = $request->get('is_notification');
                            $user_group->save();

                            $this->sendNotification(auth()->user()->id, (int)$member_id, $group->id, 'group', null, 'مجموعة جديدة');

                        } catch (ModelNotFoundException $e) {
                            return response()->json([
                                'status' => false,
                                'message' => __('messages.member_not_exist')
                            ]);
                        }
                    }
                }
                $group = Group::find($group->id);
                return response()->json([
                    'items' => $group,
                    'message' => __('messages.update_group'),
                    'status' => true
                ]);
            }
        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);
    }

    //get groups that I member in it
    public function getGroups()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }
        $user_groups = UserGroup::where('user_id', auth()->user()->id)->where('status', 'accept')->pluck('group_id')->toArray();

        $groups_id = array_unique($user_groups);
        //$groups_id = Group::where('admin_status', 'accept')->whereIn('id', $groups_id)->pluck('id')->toArray();
        $groups_id = Group::whereIn('id', $groups_id)->pluck('id')->toArray();

        $groups_collection = Group::whereIn('id', $groups_id);
        $groups_count = $groups_collection->count();
        $groups = $groups_collection->orderBy('created_at', 'DESC')->get();
        
        return response()->json([
            'items' => $groups,
            'message' => __('messages.fetch_data_msg'),
            'status' => true
        ]);
    }

    //get group by id
    public function getGroup($group_id, $password = null)
    {
        $user_groups = UserGroup::where('user_id', auth()->user()->id)->where('status', 'accept')->where('group_id', $group_id)->first();

        if (isset($user_groups)) {
            $group = Group::find($group_id);
            return response()->json([
                'items' => $group,
                'message' => __('messages.fetch_data_msg'),
                'status' => true
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => __('messages.error_msg')
        ]);
    }
    //get waiting group list by id
    public function getWaitingGroupList()
    {
        $user_groups = UserGroup::where('user_id', auth()->user()->id)->where('status', 'pending')->pluck('group_id')->toArray();
        $groups_id = array_unique($user_groups);

        if (isset($user_groups)) {
            $groups_collection = Group::where('admin_status', 'pending')->whereIn('id', $groups_id);
            $groups_count = $groups_collection->count();
            $groups = $groups_collection->orderBy('created_at', 'DESC')->get();
            return response()->json([
                'items' => $groups,
                'count' => $groups_count,
                'message' => __('messages.fetch_data_msg'),
                'status' => true
            ]);

        }
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);
    }

    public function postAcceptInvitation(Request $request)
    {
        $rules = [
            'group_id' => 'required|exists:groups,id',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }

        $user_id = auth()->user()->id;
        $user_group = UserGroup::where('status', 'pending')->where('user_id', $user_id)->where('group_id', $request->get('group_id'))->first();

        if ($user_group) {
            $user_group->status = 'accept';
            $user_group->save();

            $group = Group::where('user_id', '=', $user_id)->update([
                'admin_status' => 'accept',
            ]);
            return response()->json([
                'message' => __('messages.accept_group'),
                'items' => $user_group,
                'status' => true
            ]);
        }
        
        return response()->json([
            'message' => __('messages.error_msg'),
            'status' => false
        ]);
    }

    /**
     * function to change the group background image
     * 
     * @param  Request $request
     * 
     * @return  response
     */
    public function changeGroupWallpaper(Request $request)
    {   
        $group_id = $request->group_id;

        $background = new groupBackground;
        $background->background = $this->upload($request, 'image');
        $background->group_id = $group_id;
        $background->save();

        return response()->json([
            'message' => __('messages.group_change_image'),
            'status' => true,
            'items' => URL::to('/assets/upload/'.$background->background)
        ]);
    }

    /**
     * function to login to the group
     * 
     * @param Request $request
     * 
     * @return  response
     */
    public function loginToGroup(Request $request)
    {
        $group_id = $request->group_id;
        $password = $request->password;

        $group = Group::where('id', '=', $group_id)->first();
        if (!empty($group)) {
            if ($group->password == $password) {
                return response()->json([
                    'status' => true,
                    'items' => $group,
                    'message' => __('messages.login_to_group')
                ]);
            } else {
                return response()->json([
                    'message' => __('messages.error_msg'),
                    'status' => false
                ]);
            }
        } else {
            return response()->json([
                'message' => __('messages.error_msg'),
                'status' => false
            ]);
        }
    } 
}
