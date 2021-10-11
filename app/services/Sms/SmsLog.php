<?php
namespace App\services\Sms;
use App\Model\SmsLogModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use App\services\AliyunSms;
/**
 * 阿里云短信类
 */
class SmsLog
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
    protected static function checkCode($mobile, $code)
    {
        if (!$mobile) {
            throw new \Exception('手机号不能为空');
        }
        if (!self::checkMobile($mobile)) {
            throw new \Exception('手机号不正确');
        }
        if (!$code) {
            throw new \Exception('验证码不能为空');
        }
        $sms_log = SmsLogModel::query()->where([
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
    protected static function checkRate($mobile)
    {
        if (!$mobile) {
            throw new \Exception('手机号不能为空');
        }
        $sms_log = SmsLogModel::query()->where([
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
    public static function sendVerifyCode($mobile)
    {
        self::checkRate($mobile);
        $code = mt_rand(100000, 999999);
        $redis = Redis::connection('default');
        $redis->set($mobile,$code);
        $redis->expire($mobile,300);
        $sms_log = SmsLogModel::query()->create([
            'type' => self::TYPE_CODE,
            'mobile' => $mobile,
            'code' => $code,
            'checked' => self::CHECKED_UNVERIFIED,
            'status' => self::STATUS_NO_SEND,
            'ip' => self::getRealIp(),
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
            return false;
//            throw new \Exception($e->getMessage());
        }
    }



    static function getRealIp()
    {
        $ip = false;
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ips = explode(", ", getenv("HTTP_X_FORWARDED_FOR"));
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            $ipscount = count($ips);
            for ($i = 0; $i < $ipscount; $i++) {
                if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "unknown";
        }
        return self::isIp($ip) ? $ip : "unknown";
    }
    /**
     * 检查是否是合法的IP
     */
    static function isIp($ip)
    {
        if (preg_match('/^((\d|[1-9]\d|2[0-4]\d|25[0-5]|1\d\d)(?:\.(\d|[1-9]\d|2[0-4]\d|25[0-5]|1\d\d)){3})$/', $ip)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 验证手机号
     */
    static function checkMobile($mobile)
    {
        return preg_match('/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/i', $mobile);
    }
}

