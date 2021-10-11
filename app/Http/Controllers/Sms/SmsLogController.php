<?php
namespace App\Http\Controllers\Sms;
use App\Http\Controllers\Controller;
use App\services\Sms\SmsLog;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    /**
     * 发送短信
     */
    public function sendCode(Request $request){
        $phone = $request->input('phone');
        $code = SmsLog::sendVerifyCode($phone);
        if($code==true){
            return ['code' => 0, 'msg' => '发送成功','data'=>[]];
        }
        return ['code' => 200002, 'msg' => '验证码发送失败,请过段时间后重试'];
    }

}
