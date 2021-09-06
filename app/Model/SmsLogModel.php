<?php

namespace App\Model;
use App\services\AliyunSms;
use App\Http\Controllers\Sms\SmsLog;
use Illuminate\Database\Eloquent\Model;

class SmsLogModel extends Model
{
    protected $fillable = [
        'type',
        'mobile',
        'code',
        'checked',
        'status',
        'reason',
        'remark',
        'operator_id',
        'ip',
    ];
    //类型(0:短信验证码,1:语音验证码,2:短信消息通知)
    const TYPE_CODE = 0;
    const TYPE_VOICE = 1;
    const TYPE_MESSAGE = 2;
    //是否验证(0:未验证,1:已验证)
    const CHECKED_UNVERIFIED = 0;
    const CHECKED_VERIFIED = 1;
    //状态(0:未发送,1:已发送,2:发送失败)
    const STATUS_NO_SEND = 0;
    const STATUS_SEND = 1;
    const STATUS_FAIL = 2;
    //短信发送间隔时间，默认60秒
    const SEND_INTERVAL_TIME = 60;
    /**
     * 检测短信验证码
     */
    protected function checkCode($mobile, $code)
    {
        if (!$mobile) {
            throw new \Exception('手机号不能为空');
        }
        if (!checkMobile($mobile)) {
            throw new \Exception('手机号不正确');
        }
        if (!$code) {
            throw new \Exception('验证码不能为空');
        }
        $sms_log = $this->where([
            ['type', self::TYPE_CODE],
            ['mobile', $mobile],
            ['status', self::STATUS_SEND],
            ['checked', self::CHECKED_UNVERIFIED],
        ])->orderBy('created', 'desc')->first();
        if (!$sms_log) {
            throw new \Exception('验证码不存在,请重新获取');
        }
        if ($code != $sms_log->code) {
            throw new \Exception('验证码错误');
        }
        $sms_log->checked = self::CHECKED_VERIFIED;
        $sms_log->save();
        return true;
    }
    /**
     * 检测短信频率
     */
    protected function checkRate($mobile)
    {
        if (!$mobile) {
            throw new \Exception('手机号不能为空');
        }
        $sms_log = $this->where([
            ['mobile', $mobile],
            ['status', self::STATUS_SEND],
        ])->orderBy('created', 'desc')->first();
        $now = time();
        if ($sms_log) {
            if (($now - strtotime($sms_log->created)) < self::SEND_INTERVAL_TIME) {
                throw new \Exception('短信发送太频繁,请稍后再试');
            }
        }
        return true;
    }
    /**
     * 发送短信验证码
     */
    protected function sendVerifyCode($mobile)
    {
        self::checkRate($mobile);
        $code = mt_rand(1000, 9999);
        $sms_log = $this->create([
            'type' => self::TYPE_CODE,
            'mobile' => $mobile,
            'code' => $code,
            'checked' => self::CHECKED_UNVERIFIED,
            'status' => self::STATUS_NO_SEND,
            'ip' => getRealIp(),
        ]);
        try {
            AliyunSms::sendSms($mobile, AliyunSms::VERIFICATION_CODE, ['code' => $code]);
            $sms_log->status = self::STATUS_SEND;
            $sms_log->save();
            return true;
        } catch (\Exception $e) {
            $sms_log->status = self::STATUS_FAIL;
            $sms_log->reason = $e->getMessage();
            $sms_log->save();
            throw new \Exception($e->getMessage());
        }
    }
}
