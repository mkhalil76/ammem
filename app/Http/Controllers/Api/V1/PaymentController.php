<?php

namespace App\Http\Controllers\Api\V1;

use App\BankTransferRequirement;
use App\Group;
use App\GroupType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Send bank requirements
    public function postBankRequirement(Request $request)
    {
        $rules = [
            'group_id' => 'required|exists:groups,id',
            'person_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'country' => 'required',
            'transfer_no' => 'required',
            'bank_name' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);

        if (!$validator->getData()->status) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_msg'),
                'errors' => $validator->getData()->message
            ]);
        }

        $group = Group::where('user_id', auth()->user()->id)->find($request->get('group_id'));
        if (isset($group) && ($group->type_id == 1 || $group->type_id == 2)) {
            $bank_transfer_require = new BankTransferRequirement();
            $bank_transfer_require->group_id = $request->get('group_id');
            $bank_transfer_require->person_name = $request->get('person_name');
            $bank_transfer_require->email = $request->get('email');
            $bank_transfer_require->mobile = $request->get('mobile');
            $bank_transfer_require->country = $request->get('country');
            $bank_transfer_require->transfer_no = $request->get('transfer_no');
            $bank_transfer_require->bank_name = $request->get('bank_name');

            $group_type = GroupType::find($group->type_id);
            $bank_transfer_require->transfer_price = (double)$group_type->cost;
            $bank_transfer_require->save();
            return response()->json([
                'items' => $bank_transfer_require,
                'message' => __('messages.successfully_done'),
                'status' => true
            ]);

        }
        return response()->json([
            'status' => false,
            'message' => __('messages.error_msg'),
        ]);

    }

    // Get own payment request
    public function getPaymentRequest()
    {
        $my_groups_id = Group::where('user_id',auth()->user()->id)->pluck('id');
        $requests_collection = BankTransferRequirement::whereIn('group_id', $my_groups_id);

        $requests_count = $requests_collection->count();
        $requests = $requests_collection->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'items' => $requests,
            'message' => __('messages.fetch_data_msg'),
            'status' => true
        ]);

    }
}
