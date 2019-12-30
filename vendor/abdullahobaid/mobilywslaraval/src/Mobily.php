<?php
namespace abdullahobaid\mobilywslaraval;

use App\Http\Controllers\Controller;

class Mobily extends Controller
{
    protected static $sender;
    protected static $timeSend;
    protected static $dateSend;
    protected static $deleteKey;
    protected static $resultType;
    protected static $viewResult;
    protected static $userAccount;
    protected static $passAccount;
    protected static $MsgID;

    
    public static function arrayOfResults()
    {
        $arraySendMsg = array();
        $arraySendMsg[0] = "لم يتم الاتصال بالخادم";
        $arraySendMsg[1] = "تمت عملية الإرسال بنجاح";
        $arraySendMsg[2] = "رصيدك 0 , الرجاء إعادة التعبئة حتى تتمكن من إرسال الرسائل";
        $arraySendMsg[3] = "رصيدك غير كافي لإتمام عملية الإرسال";
        $arraySendMsg[4] = "رقم الجوال (إسم المستخدم) غير صحيح";
        $arraySendMsg[5] = "كلمة المرور الخاصة بالحساب غير صحيحة";
        $arraySendMsg[6] = "صفحة الانترنت غير فعالة , حاول الارسال من جديد";
        $arraySendMsg[7] = "نظام المدارس غير فعال";
        $arraySendMsg[8] = "تكرار رمز المدرسة لنفس المستخدم";
        $arraySendMsg[9] = "انتهاء الفترة التجريبية";
        $arraySendMsg[10] = "عدد الارقام لا يساوي عدد الرسائل";
        $arraySendMsg[11] = "اشتراكك لا يتيح لك ارسال رسائل لهذه المدرسة. يجب عليك تفعيل الاشتراك لهذه المدرسة";
        $arraySendMsg[12] = "إصدار البوابة غير صحيح";
        $arraySendMsg[13] = "الرقم المرسل به غير مفعل أو لا يوجد الرمز BS في نهاية الرسالة";
        $arraySendMsg[14] = "غير مصرح لك بالإرسال بإستخدام هذا المرسل";
        $arraySendMsg[15] = "الأرقام المرسل لها غير موجوده أو غير صحيحه";
        $arraySendMsg[16] = "إسم المرسل فارغ، أو غير صحيح";
        $arraySendMsg[17] = "نص الرسالة غير متوفر أو غير مشفر بشكل صحيح";
        $arraySendMsg[18] = "تم ايقاف الارسال من المزود";
        $arraySendMsg[19] = "لم يتم العثور على مفتاح نوع التطبيق";
        $arraySendMsg[101] = "الارسال باستخدام بوابات الارسال معطل";
        $arraySendMsg[102] = "الاي بي الخاص بك غير مصرح له بإستخدم بوابات الارسال.";
        $arraySendMsg[103] = "الدولة التي تقوم بالإرسال منها غير مصرح لها بإستخدم بوابات الارسال.";
        return $arraySendMsg;

    }
    
    public static function run()
    {
        static::$sender = config('mobilysms.sender');
        static::$deleteKey = config('mobilysms.deleteKey');
        static::$resultType = config('mobilysms.resultType');
        static::$viewResult = config('mobilysms.viewResult');
        static::$MsgID = config('mobilysms.MsgID');
        static::$userAccount = config('mobilysms.mobile');
        static::$passAccount = config('mobilysms.password');
    }

    public function Balance()
    {
        static::run();
        $url = "http://www.mobily.ws/api/balance.php";
        $stringToPost = "mobile=" . static::$userAccount . "&password=" . static::$passAccount;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
        $result = curl_exec($ch);
        return $result;
    }

    public static function Send($numbers, $msg, $dateSend = 0, $timeSend = 0, $viewResult=1)
    {   
        static::run();
        $arraySendMsg = static::arrayOfResults();
        $url = "http://www.alfa-cell.com/api/msgSend.php";
        $applicationType = "68";  
        $sender = urlencode(static::$sender);
        $domainName = $_SERVER['SERVER_NAME'];

        if(!empty(static::$userAccount) && empty(static::$passAccount)) {
            $stringToPost = "apiKey=".static::$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".static::$MsgID."&deleteKey=".static::$deleteKey."&lang=3";
        } else {
            $stringToPost = "mobile=".static::$userAccount."&password=".static::$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".static::$MsgID."&deleteKey=".static::$deleteKey."&lang=3";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
        $result = curl_exec($ch);

        if($viewResult)
            $result = static::printStringResult(trim($result) , $arraySendMsg);
        return $result;
    }

    public static function Sends($numbers, $msg, $dateSend = 0, $timeSend = 0)
    {
        static::run();
        $numbers = self::format_numbers($numbers);
        $url = "www.mobily.ws/api/msgSend.php";
        $applicationType = "68";
        $sender = urlencode(static::$sender);
        $domainName = $_SERVER['SERVER_NAME'];
        $stringToPost = "mobile=" . static::$userAccount . "&password=" . static::$passAccount . "&numbers=" . $numbers . "&sender=" . $sender . "&msg=" . $msg . "&timeSend=" . $timeSend . "&dateSend=" . $dateSend . "&applicationType=" . $applicationType . "&domainName=" . $url . "&msgId=" . static::$MsgID . "&deleteKey=" . static::$deleteKey . "&lang=3";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
        $result = curl_exec($ch);

        if ($result == 5) {
            return trans('mobily.wrondpassword');
        } elseif ($result == 4) {
            return trans('mobily.null_user_or_mobile');
        } elseif ($result == 3) {
            return trans('mobily.no_charge');
        } elseif ($result == 2) {
            return trans('mobily.no_charge_zeor');
        } elseif ($result == 6) {
            return trans('mobily.try_later');
        } elseif ($result == 10) {
            return trans('mobily.not_equeal');
        } elseif ($result == 13) {
            return trans('mobily.sender_not_approval');
        } elseif ($result == 14) {
            return trans('mobily.empty_sender');
        } elseif ($result == 15) {
            return trans('mobily.empty_numbers');
        } elseif ($result == 16) {
            return trans('mobily.empty_sender2');
        } elseif ($result == 17) {
            return trans('mobily.message_not_encoding');
        } elseif ($result == 18) {
            return trans('mobily.service_stoped');
        } elseif ($result == 19) {
            return trans('mobily.app_error');
        } elseif ($result == 1) {
            return true;
        }
    }

    public static function printStringResult($apiResult, $arrayMsgs, $printType = 'Alpha')
    {   
        global $undefinedResult;
        switch ($printType)
        {
            case 'Alpha':
            {
                if(array_key_exists($apiResult, $arrayMsgs))
                    return $arrayMsgs[$apiResult];
                else
                    return $arrayMsgs[0];
            }
            break;

            case 'Balance':
            {
                if(array_key_exists($apiResult, $arrayMsgs))
                    return $arrayMsgs[$apiResult];
                else
                {
                    list($originalAccount, $currentAccount) = explode("/", $apiResult);
                    if(!empty($originalAccount) && !empty($currentAccount))
                    {
                        return sprintf($arrayMsgs[3], $currentAccount, $originalAccount);
                    }
                    else
                        return $arrayMsgs[0];
                }
            }
            break;
                
            case 'Senders':
            {
                $apiResult = str_replace('[pending]', '[pending]<br>', $apiResult);
                $apiResult = str_replace('[active]', '<br>[active]<br>', $apiResult);
                $apiResult = str_replace('[notActive]', '<br>[notActive]<br>', $apiResult);
                return $apiResult;
            }
            break;
            
            case 'Normal':
                if($apiResult{0} != '#')
                    return $arrayMsgs[$apiResult];
                else
                    return $apiResult;
            break;
        }		
    }

    public static function format_numbers($numbers)
    {
        if (!is_array($numbers))
            return self::format_number($numbers);
        $numbers_array = array();
        foreach ($numbers as $number) {
            $n = self::format_numbers($number);
            array_push($numbers_array, $n);
        }
        return implode(',', $numbers_array);
    }

    public static function format_number($number)
    {
        if (strlen($number) == 10 && starts_with($number, '05'))
            return preg_replace('/^0/', '966', $number);
        elseif (starts_with($number, '00'))
            return preg_replace('/^00/', '', $number);
        elseif (starts_with($number, '+'))
            return preg_replace('/^+/', '', $number);
        return $number;
    }

}
