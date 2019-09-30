<?php

namespace App\Http\Controllers;

use App\BankTransferRequirement;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');

    }
    //
    public function index()
    {
        $data = [
            'title' => 'حركات الدفعات',
            'sub_title' => 'حركات الدفع للمجموعات',
        ];
        return view(admin_payment_vw() . '.view', $data);
    }

    public function getData()
    {
        $num = 1;
        return datatables()->of(BankTransferRequirement::query()->orderByDesc('updated_at'))
            ->addColumn('num', function () use (&$num) {
                return $num++;
            })
            ->addColumn('group_name', function ($payment) {

                return $payment->Group->name;
            })
//            ->addColumn('action', function ($payment) {
//
//                $action = '<a href="' . url(admin_vw() . '/add-group-member/' . $payment->id) . '" class="btn btn-outline btn-circle btn-sm green add_member">
//                                                            <i class="fa fa-plus"></i> اضافة عضو جديد </a><a href="' . url(admin_vw() . '/group/' . $payment->id) . '" class="btn btn-outline btn-circle btn-sm purple edit">
//                                                            <i class="fa fa-edit"></i> تعديل </a>
//                                                            <a href="' . url(admin_vw() . '/group/' . $payment->id) . '" class="btn btn-outline btn-circle btn-sm red delete">
//                                                            <i class="fa fa-trash"></i> حذف </a>';
//
//                return $action;
//            })
            ->rawColumns(['members', 'action', 'type', 'admin_status'])
            ->toJson();
    }

}
