<?php

namespace App\Http\Controllers;

use App\Activity;
use App\GroupType;
use App\Interest;
use App\Job;
use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ConstantController extends Controller
{
    //
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        view()->share(['title' => 'الثوابت']);
    }

    public function getConstantData($type)
    {

        $num = 1;
        if ($type == 1) {
            $query = Activity::all();
        } else if ($type == 2) {
            $query = Organization::all();

        } else if ($type == 3) {
            $query = Interest::all();

        } else if ($type == 4) {
            $query = Job::all();

        } else if ($type == 5) {
            $query = GroupType::all();

        }
        return datatables()->of($query)
            ->addColumn('num', function () use (&$num) {
                return $num++;
            })->addColumn('action', function ($constant) use ($type) {

                $action = '<a href="' . url(admin_vw() . '/constant-edit/' . $constant->id . '/' . $type) . '" class="btn btn-outline btn-circle btn-sm purple edit">
                                                            <i class="fa fa-edit"></i> تعديل </a>
                                                            <a href="' . url(admin_vw() . '/constant/' . $constant->id) . '" class="btn btn-outline btn-circle btn-sm red delete">
                                                            <i class="fa fa-trash"></i> حذف </a>';


                return $action;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function index($data)
    {

        if ($data['type'] != 5)
            return view(admin_vw() . '.constant.view', $data);
        return view(admin_vw() . '.constant.group-types', $data);
    }

    public function getActivities()
    {
        $data = [
            'sub_title' => 'عرض النشاطات',
            'type' => 1, // النشاطات
        ];
        return $this->index($data);
    }

    public function getOrganizations()
    {
        $data = [
            'sub_title' => 'عرض الجهات',
            'type' => 2, // الجهات
        ];
        return $this->index($data);
    }

    public function getInterests()
    {
        $data = [
            'sub_title' => 'عرض الاهتمامات',
            'type' => 3, // الاهتمامات
        ];
        return $this->index($data);
    }

    public function getJobs()
    {
        $data = [
            'sub_title' => 'عرض الوظائف',
            'type' => 4, // الوظائف
        ];
        return $this->index($data);
    }

    public function getGroupTypes()
    {
        $data = [
            'sub_title' => 'عرض انواع المجموعات',
            'type' => 5, // الوظائف
        ];
        return $this->index($data);
    }

    public function edit($constant_id, $type)
    {

        if ($type == 1) {
            $query = Activity::find($constant_id);
            $title = 'تعديل النشاط';

        } else if ($type == 2) {
            $query = Organization::find($constant_id);
            $title = 'تعديل الجهة';

        } else if ($type == 3) {
            $query = Interest::find($constant_id);
            $title = 'تعديل الاهتمام';

        } else if ($type == 4) {
            $query = Job::find($constant_id);
            $title = 'تعديل الوظيفة';

        } else if ($type == 5) {
            $query = GroupType::find($constant_id);
            $title = 'تعديل نوع المجموعة';

        }
        if (isset($query)) {

            $options = [
                'modal_id' => 'editConstant',
                'modal_title' => $title,
                'action' => 'تعديل',
                'form' => [
                    'method' => 'PUT',
                    'url' => url(admin_vw() . '/constant-edit/' . $query->id . '/' . $type),
                    'form_id' => 'formEdit',
                    'fields' => [
                        'name' => 'text',
                    ],
                    'values' => [
                        'name' => $query->name,
                    ],
                    'fields_ar' => [
                        'name' => 'الاسم',
                    ]
                ]
            ];

            if ($type == 5) {
                $options['form']['fields']['cost'] = 'text';
                $options['form']['fields']['max_num_member'] = 'text';

                $options['form']['values']['cost'] = $query->cost;
                $options['form']['values']['max_num_member'] = $query->max_num_member;

                $options['form']['fields_ar']['cost'] = 'التكلفة';
                $options['form']['fields_ar']['max_num_member'] = 'الحد الاقصى من الاعضاء';
            }
            $view = View::make(admin_vw() . '.modal', $options);

            $html = $view->render();

            return $html;
        }
        return false;
    }

    public function update(Request $request, $constant_id, $type)
    {
        if ($type == 1) {
            $query = Activity::find($constant_id);

        } else if ($type == 2) {
            $query = Organization::find($constant_id);

        } else if ($type == 3) {
            $query = Interest::find($constant_id);

        } else if ($type == 4) {
            $query = Job::find($constant_id);

        } else if ($type == 5) {
            $query = GroupType::find($constant_id);

        }
        $rules = [
            'name' => 'required',
        ];

        $new_attr_name = [
            'name' => 'اسم',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }
        $query->name = $request->get('name');

        if ($type == 5) {
            $query->cost = $request->get('cost');
            $query->max_num_member = $request->get('max_num_member');
        }
        return response()->json([
            'status' => $query->save()
        ]);
        return response_api($query->save());

    }

    public function create($type)
    {

        if ($type == 1) {
            $title = 'اضافة النشاط جديد';

        } else if ($type == 2) {
            $title = 'اضافة الجهة جديدة';

        } else if ($type == 3) {
            $title = 'اضافة الاهتمام جديد';


        } else if ($type == 4) {
            $title = 'اضافة الوظيفة جديدة';

        } else if ($type == 5) {
            $title = 'اضافة نوع مجموعة جديدة';

        }
        $options = [
            'modal_id' => 'addConstant',
            'modal_title' => '<span>' . $title . '</span>',
            'action' => 'اضافة',
            'form' => [
                'method' => 'POST',
                'url' => url(admin_vw() . '/constant/' . $type),
                'form_id' => 'formAdd',
                'fields' => [
                    'name' => 'text',
                ],
                'fields_ar' => [
                    'name' => 'الاسم',
                ]
            ]
        ];
        if ($type == 5) {
            $options['form']['fields']['cost'] = 'text';
            $options['form']['fields']['max_num_member'] = 'text';

            $options['form']['fields_ar']['cost'] = 'التكلفة';
            $options['form']['fields_ar']['max_num_member'] = 'الحد الاقصى من الاعضاء';
        }
        $view = View::make(admin_vw() . '.modal', $options);

        $html = $view->render();

        return $html;
    }

    public function store(Request $request, $type)
    {   
        if ($type == 1) {
            $query = new Activity();

        } else if ($type == 2) {
            $query = new Organization();

        } else if ($type == 3) {
            $query = new Interest();

        } else if ($type == 4) {
            $query = new Job();

        } else if ($type == 5) {
            $query = new GroupType();

        }
        $rules = [
            'name' => 'required',
        ];

        $new_attr_name = [
            'name' => 'اسم',
        ];

        $validator = $this->makeValidation($request, $rules, $new_attr_name);
        if (!$validator->getData()->status) {
            return $validator;
        }

        $query->name = $request->get('name');

        if ($type == 5) {
            $query->cost = $request->get('cost');
            $query->max_num_member = $request->get('max_num_member');
        }
        return response()->json([
            'status' => $query->save()
        ]);
        //return response_api($query->save());
    }

    public function destroy(Request $request, $constant_id)
    {

        $type = $request->get('type');
        if ($type == 1) {
            $query = Activity::find($constant_id);

        } else if ($type == 2) {
            $query = Organization::find($constant_id);

        } else if ($type == 3) {
            $query = Interest::find($constant_id);

        } else if ($type == 4) {
            $query = Job::find($constant_id);

        } else if ($type == 5) {
            $query = GroupType::find($constant_id);

        }
        return response_api(isset($query) && $query->delete());
    }
}
