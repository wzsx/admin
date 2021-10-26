<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use App\Model\ShopUserModel;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\wxBizDataCrypt;
use GuzzleHttp\Client;

class WxController extends Controller
{
    var $appId = 'wx5c3075128baa7866';
    var $appSecret = '2af4b5b48cbde19665fa6a1209c8ac12';
    var $wxUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';

    //获取用户登录信息
    public function codeSession(Request $request)
    {
//        if(empty($request->input->post('code')) || empty($request->input->post('signature')) || empty($request->input->post('rawData')) || empty($request->input->post('encryptedData')) || empty($request->input->post('iv'))){
//            return ['code'=>0,'msg'=>'缺少必要参数'];
//        }
        $code = $request->input('code');
//        var_dump($code);
        $url=sprintf($this->wxUrl,$this->appId,$this->appSecret,$code);
//        $url=sprintf(config('wechatUrl.url'),config('wechatUrl.appid'),config('wechatUrl.secret'),$code);
        $client=new Client(['timeout'=>5,'verify'=>false]);
        $res=$client->get($url);
        $arr=(string)$res->getBody();
        $arr=json_decode($arr,true);
        return ['code'=>200,'msg'=>'ok','data'=>$arr];
//        if(empty($arr)||empty($arr['openid'])||empty($arr['session_key'])){
//            return ['code' => 200001, 'msg' => 'code已过期或不正确'];
//        }
//        $openid = $arr['openid'];
//        $session_key = $arr['session_key'];
//        // 数据签名校验
//        $signature = $request->input('signature');
//        $rawData = Request::instance()->post('rawData');
//        $signature2 = sha1($rawData . $session_key);
//        if ($signature != $signature2) {
//            return ['code' => 500, 'msg' => '数据签名验证失败！'];
//        }
////        Vendor("PHP.wxBizDataCrypt"); //加载解密文件，在官方有下载
//        $encryptedData = $request->input('encryptedData');
//        $iv = $request->input('iv');
//        $pc = new \WXBizDataCrypt($this->appId, $session_key);
//        $errCode = $pc->decryptData($encryptedData, $iv, $data); //其中$data包含用户的所有数据
//        $data = json_decode($data,true);
//        if ($errCode == 0) {
//            $this->car_owner_model->api_save($data);
//            $time = 2*60*60;
//            $data['sid'] = md5($session_key);
//            $key = 'ses_'.$data['sid'];
//            $user_json = json_encode($data);
//            $this->cache->redis->save($key,$user_json,$time);
//            return ['code'=>200,'msg'=>'ok','data'=>$data];
//        } else {
//            return ['code' => 0, 'msg' => $errCode];
//        }
    }

    //获取用户登录信息
    public function Session(Request $request)
    {
        $code = $request->input('code');
        $url=sprintf($this->wxUrl,$this->appId,$this->appSecret,$code);
        $client=new Client(['timeout'=>5,'verify'=>false]);
        $res=$client->get($url);
        $data=(string)$res->getBody();
        $arr=json_decode($data,true);
        return ['code'=>200,'msg'=>'ok','data'=>$arr];
    }

    public function weappLogin(Request $request)
    {
        $code = $request->input('code');
        $url=sprintf($this->wxUrl,$this->appId,$this->appSecret,$code);
//        $url=sprintf(config('wechatUrl.url'),config('wechatUrl.appid'),config('wechatUrl.secret'),$code);
        $client=new Client(['timeout'=>5,'verify'=>false]);
        $res=$client->get($url);
        $arr=(string)$res->getBody();
        $arr=json_decode($arr,true);
        // 根据 code 获取微信 openid 和 session_key
//        $miniProgram = \EasyWeChat::miniProgram();
//        $data = $miniProgram->auth->session($code);
        if (isset($arr['errcode'])) {
            return ['code' => 200001, 'msg' => 'code已过期或不正确'];
        }
//        $weappOpenid = $arr['openid'];
//        $weixinSessionKey = $arr['session_key'];
//        $nickname = $request->nickname;
//        $avatar = str_replace('/132', '/0', $request->avatar);//拿到分辨率高点的头像
//        $country = $request->country?$request->country:'';
//        $province = $request->province?$request->province:'';
//        $city = $request->city?$request->city:'';
//        $gender = $request->gender == '1' ? '1' : '2';//没传过性别的就默认女的吧，体验好些
//        $language = $request->language?$request->language:'';
//
//        //找到 openid 对应的用户
//        $user = User::where('weapp_openid', $weappOpenid)->first();
//        //没有，就注册一个用户
//        if (!$user) {
//            $user = User::create([
//                'weapp_openid' => $weappOpenid,
//                'weapp_session_key' => $weixinSessionKey,
//                'password' => $weixinSessionKey,
//                'avatar' => $request->avatar,
//                'weapp_avatar' => $avatar,
//                'nickname' => $nickname,
//                'country' => $country,
//                'province' => $province,
//                'city' => $city,
//                'gender' => $gender,
//                'language' => $language,
//            ]);
//        }
//        //如果注册过的，就更新下下面的信息
//        $attributes['updated_at'] = now();
//        $attributes['weixin_session_key'] = $weixinSessionKey;
//        $attributes['weapp_avatar'] = $avatar;
//        if ($nickname) {
//            $attributes['nickname'] = $nickname;
//        }
//        if ($request->gender) {
//            $attributes['gender'] = $gender;
//        }
//        // 更新用户数据
//        $user->update($attributes);
//        // 直接创建token并设置有效期
//        $createToken = $user->createToken($user->weapp_openid);
//        $createToken->token->expires_at = Carbon::now()->addDays(30);
//        $createToken->token->save();
//        $token = $createToken->accessToken;
//
//        return response()->json([
//            'access_token' => $token,
//            'token_type' => "Bearer",
//            'expires_in' => Carbon::now()->addDays(30),
//            'data' => $user,
//        ], 200);
    }



    public function wxLogin(Request $request) {
        if(empty($request->input('code')) || empty($request->input('signature')) || empty($request->input('rawData')) || empty($request->input('encryptedData')) || empty($request->input('iv'))){
            return ['code'=>0,'msg'=>'缺少必要参数'];
        }
        $code = $request->input('code');
        $url=sprintf($this->wxUrl,$this->appId,$this->appSecret,$code);
        $client=new Client(['timeout'=>5,'verify'=>false]);
        $res=$client->get($url);
        $arr=(string)$res->getBody();
        $arr=json_decode($arr,true);
        if(empty($arr)||empty($arr['openid'])||empty($arr['session_key'])){
            return ['code' => 200001, 'msg' => 'code已过期或不正确'];
        }
        $openid = $arr['openid'];
        $session_key = $arr['session_key'];
        // 数据签名校验
        $signature = $request->input('signature');
        $rawData = $request->input('rawData');
        $signature2 = sha1($rawData . $session_key);
        if ($signature != $signature2) {
            return ['code' => 500, 'msg' => '数据签名验证失败','signature'=>$signature,'signatures'=>$signature2];
        }
        $encryptedData = $request->input('encryptedData');
        $iv = $request->input('iv');
        $pc = new WXBizDataCrypt($this->appId, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        if ($errCode !== 0) {
            return ['code' => 0, 'msg' => $errCode];
        }
        $data = json_decode($data, true);
        $session3rd = self::randomFromDev(16);
//        $data['session3rd'] = $session3rd;
//        cache($session3rd, $openid. $session_key);
        $ars = ShopUserModel::query()->where(['openid'=>$openid])->select('*')->get()->toArray();
        if($ars){
            $update_at = date('Y-m-d H:i:s');
             $update = ShopUserModel::query()->where(['openid'=>$openid])->update(['nickname'=>$data['nickName'],'face_url'=>$data['avatarUrl'],'gender'=>$data['gender'],'s_key'=>$session_key,'openid'=>$openid,'token'=>$session3rd,'update_at'=>$update_at]);
            if($update){
                $cate = ShopUserModel::query()->where(['openid'=>$openid])->select('*')->first()->toArray();
                $user_res=[
                    'mid'=>$cate['mid'],
                    'nickname'=>$cate['nickname'],
                    'face_url'=>$cate['face_url'],
                    'token'=>$cate['token']
                ];
                return ['code'=>200,'msg'=>'ok','data'=>$user_res];
            }
        }
        $created_at = date('Y-m-d H:i:s');
        $info = ShopUserModel::query()->insert(['nickname'=>$data['nickName'],'face_url'=>$data['avatarUrl'],'gender'=>$data['gender'],'s_key'=>$session_key,'openid'=>$openid,'token'=>$session3rd,'created_at'=>$created_at]);
        if($info){
            $cate = ShopUserModel::query()->where(['nickname'=>$data['nickName']])->select('*')->first()->toArray();
            $user_res=[
                'mid'=>$cate['mid'],
                'nickname'=>$cate['nickname'],
                'face_url'=>$cate['face_url'],
                'token'=>$cate['token']
            ];
            return ['code'=>200,'msg'=>'ok','data'=>$user_res];
        }

    }

    /**
     * 读取/dev/urandom获取随机数
     * @param $len
     * @return mixed|string
     */
    function randomFromDev($len) {
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }
        else
        {
            trigger_error('Can not open /dev/urandom.');
        }
        $result = base64_encode($result);
        $result = strtr($result, '+/', '-_');

        return substr($result, 0, $len);
    }

}
