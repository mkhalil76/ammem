<?php

namespace App\Http\Controllers;

use App\Group;
use App\GroupType;
use App\User;
use App\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class GroupController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => 'user_group_create']);
    }

    public function index()
    {
        $data = [
            'title' => 'المجموعات',
            'sub_title' => 'عرض المجموعات',
        ];
        return view(admin_groups_vw() . '.view', $data);
    }

    public function addMember($group_id)
    {
        $group = Group::find($group_id);
        if (isset($group)) {
            $data = [
                'title' => 'المجموعات',
                'sub_title' => 'اضافة عضو جديد ' . '(<a href="javascript:;">' . $group->name . '</a>)',
                'group' => $group
            ];
            return view(admin_groups_vw() . '.add_member', $data);
        }
        return redirect()->back();
    }

    public function postAddMember(Request $request, $group_id, $user_id)
    {
        $user_group = new UserGroup();
        $user_group->user_id = $user_id;
        $user_group->group_id = $group_id;
        return response_api($user_group->save());
    }

    //
    public function getData()
    {
        $num = 1;
        return datatables()->of(Group::query()->orderByDesc('updated_at'))
            ->addColumn('num', function () use (&$num) {
                return $num++;
            })->addColumn('admin', function ($group) {

                return $group->admin->name;
            })->addColumn('members', function ($group) {

                $count_group = UserGroup::where('group_id', $group->id)->count();
                return $count_group . '  <a href="javascript:;" class="expand_group_members" data-id="' . $group->id . '"><span style="color: #0086B2; font-size: 11px;" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a>';
            })->addColumn('type', function ($group) {

                $types = GroupType::all();
                $select = '<select class="form-control edited type" name="type_id" href="' . url(admin_vw() . '/change-group-type/' . $group->id) . '">';
                foreach ($types as $type) {
                    $selected = '';
                    if ($group->type_id == $type->id) {
                        $selected = 'selected';
                    }
                    $select .= '<option value="' . $type->id . '" ' . $selected . '>' . $type->name . '
                                        </option>';
                }
                $select .= '</select>';
                return $select;
            })->editColumn('status', function ($group) {

                if ($group->status == 'closed') {
                    $status = 'مغلقة';
                } else {
                    $status = 'مفتوحة';
                }
                return $status;
            })->editColumn('admin_status', function ($group) {

                $selected_a = '';
                $selected_p = '';
                $selected_f = '';
                if ($group->admin_status == 'accept') {
                    $selected_a = 'selected';
                } else if ($group->admin_status == 'pending') {
                    $selected_p = 'selected';
                } else {
                    $selected_f = 'selected';
                }
                $select = '<select class="form-control edited admin_status" name="admin_status" href="' . url(admin_vw() . '/change-group-privilege/' . $group->id) . '">
                <option value="pending" ' . $selected_p . '>انتظار التفعيل</option>
                <option value="accept" ' . $selected_a . '>فعالة</option>
                <option value="finish_duration" ' . $selected_f . '>انتهت المدة</option>
                </select>';
                return $select;

            })->addColumn('duration', function ($group) {


                if (isset($group->end_duration) && isset($group->start_duration)) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $group->start_duration);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $group->end_duration);

                    $diff_in_days = $to->diffInDays($from);
                    return $diff_in_days;
                }
                return 0;

            })->addColumn('action', function ($group) {

                $action = '<a href="' . url(admin_vw() . '/add-group-member/' . $group->id) . '" class="btn btn-outline btn-circle btn-sm green add_member">
                                                            <i class="fa fa-plus"></i> اضافة عضو جديد </a><a href="' . url(admin_vw() . '/group/' . $group->id) . '" class="btn btn-outline btn-circle btn-sm purple edit">
                                                            <i class="fa fa-edit"></i> تعديل </a>
                                                            <a href="' . url(admin_vw() . '/group/' . $group->id) . '" class="btn btn-outline btn-circle btn-sm red delete">
                                                            <i class="fa fa-trash"></i> حذف </a>';

                return $action;
            })
            ->rawColumns(['members', 'action', 'type', 'admin_status'])
            ->toJson();
    }

    public function userGroups($group_id)
    {
        $user_groups = UserGroup::where('group_id', $group_id)->get();

        $inner_table = '<tr class="subgrid m' . $group_id . '">
<td class="center" style="vertical-align: middle;"><span style="color: #0086B2; font-size: 20px;" class="glyphicon glyphicon-indent-right" aria-hidden="true"></span></td><td colspan="10">
                                                           <table class="table table-striped table-bordered table-hover expand_group_members_table">
                                                    <thead >
                                                        <tr style="background-color:#6c6a6e; color:#FFFFFF">
                                                            <th class="center">
                                                                #
                                                            </th>
 <th class="center">
                                                               اسم العضو
                                                            </th>
                                                            
                                <th>
                                    المنطقة
                                </th>
                                <th>
                                    الجنس
                                </th>
                                <th>
                                    رقم الهاتف
                                </th>
                                <th>
                                    البريد الالكتروني
                                </th>
                                <th>
                                    العمليات
                                </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';
        $index = 1;
        foreach ($user_groups as $user_group) {


            $user = User::find($user_group->user_id);
            if ($user->gender == 'male') {
                $gender = 'ذكر';
            } else {
                $gender = 'انثى';

            }
            $inner_table .= '<tr><td>' . $index++ . '</td>
<td>' . $user->name . '</td>
<td>' . $user->region . '</td>
<td>' . $gender . '</td>
<td>' . $user->mobile . '</td>
<td>' . $user->email . '</td>
<td><a href="' . url(admin_vw() . '/delete-member/' . $user_group->group_id . '/' . $user->id) . '" class="btn btn-outline btn-circle btn-sm red delete">
                                                            <i class="fa fa-trash"></i> حذف </a></td>

</tr>';
        }

        $inner_table .= '</tbody></table></tr>';
        return $inner_table;

    }

    public function edit($id)
    {

        $query = Group::find($id);

        $title = 'تعديل بيانات المجموعة';
        if (isset($query)) {

            $group_types = GroupType::all();
            $group_types_names = [];
            foreach ($group_types as $type) {
                $group_types_names[$type->id] = $type->name;
            }
            $view = View::make(admin_vw() . '.modal', [
                'modal_id' => 'editGroup',
                'modal_title' => $title,
                'action' => 'تعديل',
                'form' => [
                    'method' => 'PUT',
                    'url' => url(admin_vw() . '/group/' . $query->id),
                    'form_id' => 'formEdit',
                    'fields' => [
                        'name' => 'text',
                        'image' => 'file',
                        'details' => 'textarea',
                        'type_id' => $group_types_names,
                        'status' => ['closed' => 'مغلقة', 'opened' => 'مفتوحة'],
                        'start_duration' => 'data-time',
                        'end_duration' => 'data-time',
                    ],
                    'values' => [
                        'name' => $query->name,
                        'image' => 'text',
                        'details' => $query->details,
                        'type_id' => $query->type_id,
                        'status' => $query->status,
                        'start_duration' => $query->start_duration,
                        'end_duration' => $query->end_duration,
                    ],
                    'fields_ar' => [
                        'name' => 'الاسم',
                        'image' => 'الصورة',
                        'details' => 'الوصف',
                        'type_id' => 'نوع المجموعة',
                        'status' => 'الحالة',
                        'start_duration' => 'تاريخ البدء',
                        'end_duration' => 'تاريخ الانتهاء',
                    ]
                ]
            ]);

            $html = $view->render();

            return $html;
        }
        return false;
    }

    public function update(Request $request, $group_id)
    {
        $rules = [
            'name' => 'required',
            'details' => 'required',
//            'image' => 'required|image',
            'type_id' => 'required|exists:group_types,id',
            'status' => 'required|in:closed,opened',
        ];
        $new_attr_name = [
            'name' => 'اسم',
            'details' => 'الوصف',
//            'image' => 'الصورة',
            'type_id' => 'نوع المجموعة',
            'status' => 'الحالة',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $group_type = GroupType::find($request->get('type_id'));

        $start_duration = date('Y-m-d H:i:s', strtotime($request->get('start_duration')));
        $end_duration = date('Y-m-d H:i:s', strtotime($request->get('end_duration')));
        $group = Group::find($group_id);
        $group->name = $request->get('name');
        if ($request->has('image'))
            $group->image = $this->upload($request, 'image');
        $group->details = $request->get('details');
        $group->type_id = $request->get('type_id');
        $group->status = $request->get('status');
        $group->user_id = auth()->user()->id;


        if ($group_type->cost == 0) {
            $group->start_duration = null;
            $group->end_duration = null;

        } else {
            $group->start_duration = $start_duration;
            $group->end_duration = $end_duration;
        }

        return response_api($group->save());
    }

    public function create()
    {

        $group_types = GroupType::all();
        $group_types_names = [];
        foreach ($group_types as $type) {
            $group_types_names[$type->id] = $type->name;
        }
        $view = View::make(admin_vw() . '.modal', [
            'modal_id' => 'addGroup',
            'modal_title' => 'اضافة مجموعة جديدة',
            'action' => 'اضافة',
            'form' => [
                'method' => 'POST',
                'url' => url(admin_vw() . '/group'),
                'form_id' => 'formAdd',
                'fields' => [
                    'name' => 'text',
                    'image' => 'file',
                    'details' => 'textarea',
                    'type_id' => $group_types_names,
                    'status' => ['closed' => 'مغلقة', 'opened' => 'مفتوحة'],
                ],
                'fields_ar' => [
                    'name' => 'الاسم',
                    'image' => 'الصورة',
                    'details' => 'الوصف',
                    'type_id' => 'نوع المجموعة',
                    'status' => 'الحالة',
                ]
            ]
        ]);

        $html = $view->render();

        return $html;
    }

    public function user_group_create()
    {

        $group_types = GroupType::all();
        $group_types_names = [];
        $members = [];
        foreach ($group_types as $type) {
            $group_types_names[$type->id] = $type->name;
        }

        $users = User::whereNotNull('name')->orderByDesc('updated_at')->get();


        foreach ($users as $user) {
            $members[$user->id] = $user->name . ' - ' . $user->country;
        }
        $view = View::make(admin_vw() . '.modal', [
            'modal_id' => 'addGroup',
            'modal_title' => 'اضافة مجموعة جديدة',
            'action' => 'اضافة',
            'form' => [
                'method' => 'POST',
                'url' => url(user_vw() . '/user-group-create'),
                'form_id' => 'formAdd',
                'fields' => [
                    'name' => 'text',
                    'image' => 'file',
                    'details' => 'textarea',
                    'type_id' => $group_types_names,
                    'status' => ['closed' => 'مغلقة', 'opened' => 'مفتوحة'],
                    'members' => ['members' => $members, 'is_multiple' => true],
                ],
                'fields_ar' => [
                    'name' => 'الاسم',
                    'image' => 'الصورة',
                    'details' => 'الوصف',
                    'type_id' => 'نوع المجموعة',
                    'status' => 'الحالة',
                    'members' => 'اختيار اعضاء',
                ]
            ]
        ]);

        $html = $view->render();

        return $html;
    }

    public function user_store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'details' => 'required',
            'image' => 'required|image',
            'type_id' => 'required|exists:group_types,id',
            'status' => 'required|in:closed,opened',
            'members' => 'required|exists:users,id',
        ];
        $new_attr_name = [
            'name' => 'اسم',
            'details' => 'الوصف',
            'image' => 'الصورة',
            'type_id' => 'نوع المجموعة',
            'status' => 'الحالة',
            'members' => 'الاعضاء',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $group_type = GroupType::find($request->get('type_id'));
//        $start_duration = date('Y-m-d H:i:s', strtotime($request->get('start_duration')));
//        $end_duration = date('Y-m-d H:i:s', strtotime($request->get('end_duration')));
        $group = new Group();
        $group->name = $request->get('name');
        $group->image = $this->upload($request, 'image');
        $group->details = $request->get('details');
        $group->type_id = $request->get('type_id');
        $group->status = $request->get('status');
        $group->user_id = auth()->user()->id;

        if ($group_type->cost == 0) {
            $group->start_duration = null;
            $group->end_duration = null;

        }
//        else {
//            $group->start_duration = $start_duration;
//            $group->end_duration = $end_duration;
//        }
        $is_save = false;
        if ($group->save()) {

            $members = $request->get('members');
            foreach ($members as $member_id) {
                $user_group = new UserGroup();
                $user_group->user_id = $member_id;
                $user_group->group_id = $group->id;
                $user_group->save();
            }
            $is_save = true;
        }
        return response_api($is_save, $group);
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'details' => 'required',
            'image' => 'required|image',
            'type_id' => 'required|exists:group_types,id',
            'status' => 'required|in:closed,opened',
        ];
        $new_attr_name = [
            'name' => 'اسم',
            'details' => 'الوصف',
            'image' => 'الصورة',
            'type_id' => 'نوع المجموعة',
            'status' => 'الحالة',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $group_type = GroupType::find($request->get('type_id'));
        $start_duration = date('Y-m-d H:i:s', strtotime($request->get('start_duration')));
        $end_duration = date('Y-m-d H:i:s', strtotime($request->get('end_duration')));
        $group = new Group();
        $group->name = $request->get('name');
        $group->image = $this->upload($request, 'image');
        $group->details = $request->get('details');
        $group->type_id = $request->get('type_id');
        $group->status = $request->get('status');
        $group->user_id = auth()->user()->id;

        if ($group_type->cost == 0) {
            $group->start_duration = null;
            $group->end_duration = null;

        } else {
            $group->start_duration = $start_duration;
            $group->end_duration = $end_duration;
        }
        return response_api($group->save());
    }

    public function destroy($group_id)
    {

        $group = Group::find($group_id);

        return response_api(isset($group) && $group->delete());
    }

    public function deleteMemberGroup($group_id, $member_id)
    {
        $user_group = UserGroup::where('group_id', $group_id)->where('user_id', $member_id)->first();

        return response_api(isset($user_group) && $user_group->delete());

    }

    public function changeGroupType(Request $request, $group_id)
    {

        $rules = [
            'type_id' => 'required|exists:group_types,id',
        ];
        $new_attr_name = [
            'type_id' => 'نوع المجموعة',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $group_type = GroupType::find($request->get('type_id'));
        $group = Group::find($group_id);
        $group->type_id = $request->get('type_id');

        if ($group_type->cost == 0) {
            $group->start_duration = null;
            $group->end_duration = null;

        }
        return response_api($group->save());

    }

    public function changeGroupPrivilege(Request $request, $group_id)
    {

        $rules = [
            'admin_status' => 'required|in:pending,accept,finish_duration',
        ];
        $new_attr_name = [
            'admin_status' => 'حالة الصلاحية',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $group = Group::find($group_id);
        $group->admin_status = $request->get('admin_status');

        if ($group->save())
        {
            $this->sendNotification(auth()->user()->id, $group->user_id, $group->id, 'group', null, 'تغير حالة المجموعه');
            return response_api(true);
        }
        return response_api(false);

    }
}
