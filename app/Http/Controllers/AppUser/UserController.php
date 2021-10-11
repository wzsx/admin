<?php
namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Controller;
use App\Model\DoctorInfoModel;
use App\services\Sms\SmsLog;
use Illuminate\Http\Request;
use App\Model\DoctorSectionModel;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * 注册用户
     */
    public function register(Request $request){
        $params = $request->all();
        $doctor_name = (string)$params['doctor_name'];
        $phone = (int)$params['phone'];
        $code = $params['code'];
        $password = password_hash($params['doctor_password'],PASSWORD_DEFAULT);
        $redis = Redis::connection('default');
        if($code!==$redis->get($phone)){
            return ['code' => 200001, 'msg' => '验证码错误'];
        }
        $user = DoctorInfoModel::query()->where(['phone'=>$phone])->select('*')->first();
        if ($user){
            return ['code' => 200003, 'msg' => '该手机号已注册'];
        }
        DoctorInfoModel::query()->insert(['phone'=>$phone,'doctor_name'=>$doctor_name,'doctor_password'=>(string)$password]);
        $res = $user->toArray();
            return ['code' => 0, 'msg' => '发送成功','data'=>$res];
    }

    /**
     * 短信验证码登录
     */
    public function login(Request $request){
        $params = $request->all();
        $phone = $params['phone'];
        $code = $params['code'];
        $redis = Redis::connection('default');
        if($code==$redis->get($phone)){
            $user = DoctorInfoModel::query()->where(['phone'=>$phone])->select('*')->first();
            if($user){
                return ['code'=>0,'msg'=>'登录成功','data'=>$user];
            }
            return ['code' => 200003, 'msg' => '请填写正确手机号'];
        }
        return ['code' => 200001, 'msg' => '验证码错误'];
    }

    /**
     * 密码登录
     */
    public function pwdLogin(Request $request){
        $params = $request->all();
        $phone = $params['phone'];
        $password = $params['password'];
        $user = DoctorInfoModel::query()->where(['phone'=>$phone])->select('*')->first();
        if($user){
            if(password_verify($password,$user['password'])){
                return ['code'=>0,'msg'=>'登录成功','data'=>$user];
            }
            return  ['code' => 200004, 'msg' => '密码错误'];
        }
        return ['code' => 200003, 'msg' => '请填写正确手机号'];
    }

    /**
     * 修改密码
     */
    public function updatePwd(Request $request){
        $params = $request->all();
        $phone = $params['phone'];
        $password = $params['password'];
        $user = DoctorInfoModel::query()->where(['phone'=>$phone])->select('*')->first();
        if($user){
            if(password_verify($password,$user['password'])){
              $res = DoctorInfoModel::query()->where(['phone'=>$phone])->update(['password'=>password_hash($password,PASSWORD_DEFAULT)]);
              if($res){
                  return ['code'=>0,'msg'=>'修改成成功','data'=>[]];
              }return ['code' => 200005, 'msg' => '修改失败'];
            }
        }
        return ['code' => 200003, 'msg' => '请填写正确手机号'];
    }

    /**
     * 短信修改密码
     */
    public function MessagesupdatePwd(Request $request){
        $params = $request->all();
        $phone = $params['phone'];
        $code = $params['code'];
        $password = password_hash($params['password'],PASSWORD_DEFAULT);
        $redis = Redis::connection('default');
        if($code==$redis){
            $user = DoctorInfoModel::query()->where(['phone'=>$phone])->select('*')->first();
            if($user) {
                $res = DoctorInfoModel::query()->where(['phone' => $phone])->update(['password' => $password]);
                if ($res) {
                    return ['code' => 0, 'msg' => '修改成成功', 'data' => []];
                }return ['code' => 200005, 'msg' => '修改失败'];
            }
            return ['code' => 200003, 'msg' => '请填写正确手机号'];
        }
        return ['code' => 200001, 'msg' => '验证码错误'];
    }
}
