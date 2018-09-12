<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Group;
use App\Interest;
use App\Job;
use App\Organization;
use App\User;
use App\UserGroup;
use Illuminate\Http\Request;
use Mobily;

class UserController extends Controller
{

    //

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => 'postUser']);

        view()->share(['activities' => Activity::all(), 'interests' => Interest::all(), 'organizations' => Organization::all(), 'jobs' => Job::all()]);
    }

    public function index()
    {
        $data = [
            'title' => 'المستخدمين',
            'sub_title' => 'عرض المستخدمين',
        ];
        return view(admin_users_vw() . '.view', $data);
    }

    public function getData($group_id = null)
    {
        $num = 1;
        $users = User::query()->orderByDesc('updated_at');
        if (isset($group_id)) {
            $users_id = UserGroup::where('group_id', $group_id)->pluck('user_id');
            $users = User::whereNotIn('id', $users_id)->orderByDesc('updated_at');
        }
        return datatables()->of($users)
            ->addColumn('num', function () use (&$num) {
                return $num++;
            })
            ->editColumn('gender', function ($user) {
                if ($user->gender == 'male') return '<i class="fa fa-mars"></i>';
                else return '<i class="fa fa-venus"></i>';
            })->addColumn('groups', function ($user) {

                $count_group = UserGroup::where('user_id', $user->id)->count();
                return $count_group . '  <a href="javascript:;" class="expand_groups" data-id="' . $user->id . '"><span style="color: #0086B2; font-size: 11px;" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a>';

            })->addColumn('action', function ($user) use ($group_id) {

                if (isset($group_id)) {
                    $action = '<a href="' . url(admin_vw() . '/add-group-member/' . $group_id . '/' . $user->id) . '" class="btn btn-outline btn-circle btn-sm green add_member">
                                                            <i class="fa fa-plus"></i> اضافته للمجموعة </a>';
                } else {
                    $action = '<a href="' . url(admin_vw() . '/user-edit/' . $user->id) . '" class="btn btn-outline btn-circle btn-sm purple">
                                                            <i class="fa fa-edit"></i> تعديل </a>';

                    if ($user->status == 'active') {
                        return $action . '<a href="' . url(admin_vw() . '/user-status/' . $user->id . '/0') . '" class="btn btn-outline btn-circle dark btn-sm red status">
                                                            <i class="fa fa-ban"></i> حظر </a>';
                    }
                    $action = $action . '<a href="' . url(admin_vw() . '/user-status/' . $user->id . '/1') . '" class="btn btn-outline btn-circle dark btn-sm black status">
                                                            <i class="fa fa-check"></i> رفع الحظر </a>';
                }
                return $action;
            })
            ->rawColumns(['gender', 'action', 'groups'])
            ->toJson();
    }

    public function userGroups($user_id)
    {
        $user_groups = UserGroup::where('user_id', $user_id)->get();

        $inner_table = '<tr class="subgrid m' . $user_id . '">
<td class="center" style="vertical-align: middle;"><span style="color: #0086B2; font-size: 20px;" class="glyphicon glyphicon-indent-right" aria-hidden="true"></span></td><td colspan="10">
                                                           <table class="table table-striped table-bordered table-hover expand_group_table">
                                                    <thead >
                                                        <tr style="background-color:#6c6a6e; color:#FFFFFF">
                                                            <th class="center">
                                                                #
                                                            </th>
 <th class="center">
                                                               اسم المجموعة
                                                            </th>
                                                            <th>
                                                            تفاصيل المجموعة
                                                            </th>
                                                            <th>
                                                            حالة المستخدم
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';
        $index = 1;
        foreach ($user_groups as $user_group) {
            $group = Group::find($user_group->group_id);
            $inner_table .= '<tr><td>' . $index++ . '</td><td>' . $group->name . '</td><td>' . $group->details . '</td><td>' . $user_group->status . '</td></tr>';
        }

        $inner_table .= '</tbody></table></tr>';
        return $inner_table;

    }

    public function userStatus($user_id, $status = 0)
    {
        $user = User::find($user_id);
        if (isset($user)) {

            if ($status == 0) {
                $user->status = 'block';
                $this->sendNotification(null, $user->id, $user->id, 'block_user');
            }

            if ($status == 1)
                $user->status = 'active';

            return response_api($user->save(), $user);
        }
        return response_api(false);

    }

// add new user and send activation code
    public function postUser(Request $request)
    {
        $rules = [
            'country' => 'required',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'region' => 'required',
            'activity_id' => 'required|exists:activities,id',
            'organization_id' => 'required|exists:organizations,id',
            'interest_id' => 'required|exists:interests,id',
            'gender' => 'required|in:male,female',
            'job_id' => 'required|exists:jobs,id',
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
        }
        $activation_code = $this->generateActivationCode(5);
        $user = new User();
        $user->name = $request->get('name');
        $user->country = $request->get('country');
        $user->mobile = $request->get('mobile');
        $user->email = $request->get('email');
        $user->activation_code = $activation_code;
        $user->password = bcrypt($activation_code);

        $user->region = $request->get('region');
        $user->activity_id = $request->get('activity_id');
        $user->organization_id = $request->get('organization_id');
        $user->interest_id = $request->get('interest_id');
        $user->gender = $request->get('gender');
        $user->job_id = $request->get('job_id');

        if ($user->save()) {
            Mobily::send($request->get('mobile'), 'Your activation code: ' . $activation_code);
            return response_api(true, $user);
        }
        return response_api(false);
    }

    public function edit($user_id = null)
    {
        if (!isset($user_id))
            $user_id = auth()->user()->id;
        $user = User::find($user_id);

        if (isset($user))
            return view(admin_users_vw() . '.edit', ['user' => $user]);
        return redirect()->back();
    }

    public function putUser(Request $request, $user_id = null)
    {
        if (!isset($user_id))
            $user_id = auth()->user()->id;
        $rules = [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:users,email,' . $user_id,
            'region' => 'required',
            'activity_id' => 'required|exists:activities,id',
            'organization_id' => 'required|exists:organizations,id',
            'interest_id' => 'required|exists:interests,id',
//            'photo' => 'required|image',
            'gender' => 'required|in:male,female',
            'job_id' => 'required|exists:jobs,id',
        ];

        if ($request->has('password'))
            $rules['password'] = 'sometimes|min:6';
        $new_attr_name = [
            'name' => 'اسم المستخدم',
            'mobile' => 'رقم الهاتف',
            'region' => 'المنطقة',
            'activity_id' => 'مجال النشاط',
            'organization_id' => 'اسم الجهة',
            'interest_id' => 'الاهتمام',
            'gender' => 'الجنس',
            'job_id' => 'الوظيفة',
            'password' => 'كلمة المرور',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $user = User::find($user_id);
        $user->name = $request->get('name');
        $user->mobile = $request->get('mobile');
        $user->email = $request->get('email');
        $user->region = $request->get('region');
        $user->activity_id = $request->get('activity_id');
        $user->organization_id = $request->get('organization_id');
        $user->interest_id = $request->get('interest_id');
        $user->gender = $request->get('gender');
        $user->job_id = $request->get('job_id');
        if ($request->has('password'))
            $user->password = bcrypt($request->get('password'));
        return response_api($user->save());
    }
}
