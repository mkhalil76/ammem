<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class MobileController extends Controller
{   
    private $mobile = "";							//رقم الجوال (إسم المستخدم) في ألفا سيل
    private $password = "";						  	//كلمة المرور في ألفا سيل
    private $apiKey = "";                          	//يمكنك استخدام الـ apiKey بدلا من رقم الجوال (إسم المستخدم) وكلمة المرور

    private $sender = "";					//اسم المرسل الذي سيظهر عند ارسال الرساله،

    private $numbers = "";						   	//يجب كتابة الرقم بالصيغة الدولية مثل 96650555555 وعند الإرسال إلى أكثر من رقم يجب وضع الفاصلة (,) وهي التي عند حرف الواو بين كل رقمين
                                            //لا يوجد عدد محدد من الأرقام التي يمكنك الإرسال لها في حال تم الإرسال من خلال fsockpoen أو CURL،
                                            //ولكن في حال تم الإرسال من خلال دالة fOpen، فإنه يمكنك الإرسال إلى 120 رقم فقط في كل عملية إرسال

    private $msg = "اهلا وسهلا بك مع alfa-cell.com";	   	/*
                                            نص الرسالة
                                            الرساله العربيه  70 حرف
                                            الرساله الانجليزيه 160 حرف
                                            في حال ارسال اكثر من رساله عربيه فان الرساله الواحده تحسب 67
                                            والرساله الانجليزي 153
                                            */

    private $MsgID = rand(1,99999);				  	//رقم عشوائي يتم إرفاقه مع الإرساليه، في حال الرغبة بإرسال نفس الإرساليه في أقل من ساعه من إرسال الرساله الأولى.
                                            //موقع ألفا سيل يمنع تكرار إرسال نفس الرساله خلال ساعه من إرسالها، إلا في حال تم إرسال قيمة مختلفه مع كل إرساليه.
                                            
    private $timeSend = "";						   	//لتحديد وقت الإرساليه - 0 تعني الإرسال الآن
                                            //الشكل القياسي للوقت هو hh:mm:ss

    private $dateSend = "";						   	//لتحديد تاريخ الإرساليه - 0 تعني الإرسال الآن
                                            //الشكل القياسي للتاريخ هو mm/dd/yyyy
                                            
    private $deleteKey = "";					//يمكنك من خلال هذه القيمة حذف الرسالة المجدولة (قبل موعد إرسالها) بإستخدام دالة حذف الرسائل.
                                            
    private $resultType = 1;						//دالة تحديد نوع النتيجه الراجعه من البوابة
                                            //0: إرجاع نتيجة البوابة بشكل عددي
                                            //1: إرجاع نتيجة البوابة بشكل نصي

    function __construct()
    {
        $this->mobile = '966568261041';
        $this->password = 'Qwert1234q';
        $this->apiKey = '184bfcea65ec15a7e2dd49fa9bc09c06';
        $this->sender = 'ammem';
        
        
    }

    function fsockopenTest()
    {
        $testValue = 0;
        if(function_exists("fsockopen"))
            ++$testValue;
        if(function_exists("fputs"))
            ++$testValue;
        if(function_exists("feof"))
            ++$testValue;
        if(function_exists("fread"))
            ++$testValue;
        if(function_exists("fclose"))
            ++$testValue;
        return $testValue;
    }

    //دالة فحص الإرسال curl
    function curlTest()
    {
        $testValue = 0;
        if(function_exists("curl_init"))
            ++$testValue;
        if(function_exists("curl_setopt"))
            ++$testValue;
        if(function_exists("curl_exec"))
            ++$testValue;
        if(function_exists("curl_close"))
            ++$testValue;
        if(function_exists("curl_errno"))
            ++$testValue;
        return $testValue;
    }

    //دالة فحص الإرسال fopen
    function fopenTest()
    {
        $testValue = 0;	
        if(function_exists("fopen"))
            ++$testValue;
        if(function_exists("fclose"))
            ++$testValue;	
        if(function_exists("fread"))
            ++$testValue;	
        return $testValue;
    }

    //دالة فحص الإرسال file
    function fileTest()
    {
        $testValue = 0;	
        if(function_exists("file"))
            ++$testValue;
        if(function_exists("http_build_query"))
            ++$testValue;	
        if(function_exists("stream_context_create"))
            ++$testValue;	
        return $testValue;
    }

    //دالة فحص الإرسال file
    function filegetcontentsTest()
    {
        $testValue = 0;	
        if(function_exists("file_get_contents"))
            ++$testValue;
        if(function_exists("http_build_query"))
            ++$testValue;	
        if(function_exists("stream_context_create"))
            ++$testValue;	
        return $testValue;
    }

        //دالة فحص حالة الإرسال بإستخدام CURL
    function sendStatus($viewResult=1)
    {
        global $arraySendStatus;	
        $url = "http://www.alfa-cell.com/api/sendStatus.php";$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $result = curl_exec($ch);

        if($viewResult)
            $result = $this->printStringResult(trim($result) , $arraySendStatus);
        return $result;
    }

    //دالة تغيير كلمة المرور لحساب الإرسال في موقع ألفا سيل بإستخدام CURL
    function changePassword($userAccount, $passAccount, $newPassAccount, $viewResult=1)
    {
        global $arrayChangePassword;
        $url = "http://www.alfa-cell.com/api/changePassword.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&newPassword=".$newPassAccount;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&newPassword=".$newPassAccount;
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
            $result = $this->printStringResult(trim($result) , $arrayChangePassword);
        return $result;
    }

    //دالة إسترجاع كلمة المرور لحساب الإرسال في موقع ألفا سيل بإستخدام CURL
    function forgetPassword($userAccount, $sendType, $viewResult=1)
    {
        global $arrayForgetPassword;
        $url = "http://www.alfa-cell.com/api/forgetPassword.php";
        $stringToPost = "mobile=".$userAccount."&type=".$sendType;$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
        $result = curl_exec($ch);
        
        if($viewResult)
            $result = $this->printStringResult(trim($result) , $arrayForgetPassword);
        return $result;
    }

    //دالة إسترجاع كلمة المرور لحساب الإرسال في موقع ألفا سيل بإستخدام CURL
    function forgetPasswordApiKey($apiKey, $sendType, $viewResult=1)
    {
        global $arrayForgetPassword;
        $url = "http://www.alfa-cell.com/api/forgetPassword.php";
        $stringToPost = "apiKey=".$apiKey."&type=".$sendType;$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $stringToPost);
        $result = curl_exec($ch);

        if($viewResult)
            $result = printStringResult(trim($result) , $arrayForgetPassword);
        return $result;
    }

    //دالة عرض الرصيد بإستخدام CURL
    function balanceSMS($userAccount, $passAccount, $viewResult=1)
    {
        global $arrayBalance;
        $url = "http://www.alfa-cell.com/api/balance.php";

        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount;
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
            $result = printStringResult(trim($result), $arrayBalance, 'Balance');
        return $result;
    }

    //دالة الإرسال بإستخدام CURL
    function sendSMS($userAccount, $passAccount, $numbers, $sender, $msg, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
    {
        global $arraySendMsg;
        $url = "http://www.alfa-cell.com/api/msgSend.php";
        $applicationType = "68";  
        $sender = urlencode($sender);
        $domainName = $_SERVER['SERVER_NAME'];

        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
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
            $result = printStringResult(trim($result) , $arraySendMsg);
        return $result;
    }

    //دالة الإرسال بإستخدام قالب رسالة موحد من خلال CURL
    function sendSMSWK($userAccount, $passAccount, $numbers, $sender, $msg, $msgKey, $MsgID, $timeSend=0, $dateSend=0, $deleteKey=0, $viewResult=1)
    {
        global $arraySendMsgWK;
        $url = "http://www.alfa-cell.com/api/msgSendWK.php";
        $applicationType = "68";
        $sender = urlencode($sender);
        $domainName = $_SERVER['SERVER_NAME'];

        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&numbers=".$numbers."&sender=".$sender."&msg=".$msg."&msgKey=".$msgKey."&timeSend=".$timeSend."&dateSend=".$dateSend."&applicationType=".$applicationType."&domainName=".$domainName."&msgId=".$MsgID."&deleteKey=".$deleteKey."&lang=3";
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
            $result = printStringResult(trim($result) , $arraySendMsgWK);
        return $result;
    }

    //دالة حذف الرسائل المجدولة بإستخدام CURL
    function deleteSMS($userAccount, $passAccount, $deleteKey=0, $viewResult=1)
    {
        global $arrayDeleteSMS;
        $url = "http://www.alfa-cell.com/api/deleteMsg.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&deleteKey=".$deleteKey;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&deleteKey=".$deleteKey;
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
            $result = printStringResult(trim($result) , $arrayDeleteSMS);
        return $result;
    }

    //دالة طلب إسم مرسل (جوال) بإستخدام CURL
    function addSender($userAccount, $passAccount, $sender, $viewResult=1)
    {	
        global $arrayAddSender;
        $url = "http://www.alfa-cell.com/api/addSender.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&sender=".$sender;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;
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
            $result = printStringResult(trim($result), $arrayAddSender, 'Normal');
        return $result;
    }

    //دالة تفعيل إسم مرسل (جوال) بإستخدام CURL
    function activeSender($userAccount, $passAccount, $senderId, $activeKey, $viewResult=1)
    {
        global $arrayActiveSender;
        $url = "http://www.alfa-cell.com/api/activeSender.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&senderId=".$senderId."&activeKey=".$activeKey;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId."&activeKey=".$activeKey;
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
            $result = printStringResult(trim($result) , $arrayActiveSender);
        return $result;
    }

    //دالة التحقق من حالة طلب إسم مرسل (جوال) بإستخدام CURL
    function checkSender($userAccount, $passAccount, $senderId, $viewResult=1)
    {	
        global $arrayCheckSender;
        $url = "http://www.alfa-cell.com/api/checkSender.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&senderId=".$senderId;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&senderId=".$senderId;
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
            $result = printStringResult(trim($result) , $arrayCheckSender);
        return $result;
    }

    //دالة طلب إسم مرسل (أحرف) بإستخدام CURL
    function addAlphaSender($userAccount, $passAccount, $sender, $viewResult=1)
    {
        global $arrayAddAlphaSender;
        $url = "http://www.alfa-cell.com/api/addAlphaSender.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount."&sender=".$sender;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount."&sender=".$sender;
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
            $result = printStringResult(trim($result) , $arrayAddAlphaSender);
        return $result;
    }

    //دالة التحقق من حالة طلب إسم مرسل (أحرف) بإستخدام CURL
    function checkAlphasSender($userAccount, $passAccount, $viewResult=1)
    {
        global $arrayCheckAlphasSender;
        $url = "http://www.alfa-cell.com/api/checkAlphasSender.php";
        if(!empty($userAccount) && empty($passAccount)) {
            $stringToPost = "apiKey=".$userAccount;
        } else {
            $stringToPost = "mobile=".$userAccount."&password=".$passAccount;
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
            $result = printStringResult(trim($result) , $arrayCheckAlphasSender, 'Senders');
        return $result;
    }

        //دالة طباعة القيمة الناتجه من بوابات الإرسال على شكل نص
    function printStringResult($apiResult, $arrayMsgs, $printType = 'Alpha')
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
}
