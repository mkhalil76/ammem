<?php

namespace App\Http\Controllers;

use App\Activity;
use App\BankTransferRequirement;
use App\Group;
use App\Interest;
use App\Job;
use App\Message;
use App\Organization;
use App\User;
use Illuminate\Http\Request;
use Mobily;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['UserLogin', 'postUserLogin', 'sendActivationCode']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (auth()->user()->type == 'user')
            return redirect()->to('user/my-groups');
//        dd(\request()->segment(2));
        $user_count = User::count();
        $group_count = Group::count();
        $message_count = Message::count();
        $payment_count = BankTransferRequirement::count();
        $data = [
            'user_count' => $user_count,
            'group_count' => $group_count,
            'message_count' => $message_count,
            'payment_count' => $payment_count,
        ];
        return view(admin_vw() . '.home', $data);
    }

    public function UserLogin()
    {
        $data = [
            'activities' => Activity::all(),
            'organizations' => Organization::all(),
            'jobs' => Job::all(),
            'interests' => Interest::all(),
        ];
//        $activities
//        dd(session()->get('is_sent_activation_code'));
        return view(admin_users_vw() . '.login', $data);

    }

    public function postUserLogin(Request $request)
    {
       
        if (auth()->attempt(['mobile' => $request->get('mobile'), 'password' => $request->get('activation_code')])) {

            return redirect()->intended(admin_vw() . '/home');
        }

        return back();
    }

    public function sendActivationCode(Request $request)
    {


        $rules = [
            'mobile' => 'required',
        ];
        $validator = $this->makeValidation($request, $rules);
        if (!$validator->getData()->status) {
            return $validator;
        }
        $activation_code = $this->generateActivationCode(5);

        $user = User::where('mobile', $request->get('mobile'))->first();


        if (!isset($user))
            return response_api(false);
        $user->activation_code = $activation_code;
        $user->password = bcrypt($activation_code);
        Mobily::send($request->get('mobile'), 'رمز التحقق:' . $activation_code);

        session()->put('is_sent_activation_code', 1);
        return response_api($user->save());
    }

    public function logout()
    {

        if (auth()->check() && auth()->user()->type == 'user') {
            auth()->logout();

            return redirect()->to(user_vw() . '/login');
        }

        auth()->logout();

        return redirect()->back();
    }
}
