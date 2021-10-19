<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WxController extends Controller
{
    var $appId = 'wx5c3075128baa7866';
    var $appSecret = '2af4b5b48cbde19665fa6a1209c8ac12';
    var $wxUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';

    //获取用户登录信息
    public function codeSession(Request $request)
    {
        $code = $request->input('code');
        $url=sprintf($this->wxUrl,$this->appId,$this->appSecret,$code);
//        $url=sprintf(config('wechatUrl.url'),config('wechatUrl.appid'),config('wechatUrl.secret'),$code);
        $client=new Client(['timeout'=>5,'verify'=>false]);
        $res=$client->get($url);
        $arr=(string)$res->getBody();
        $arr=json_decode($arr,true);
//        return ['code'=>200,'msg'=>'ok','data'=>$arr];
        $openid = $arr['openid'];
        $session_key = $arr['session_key'];
        // 数据签名校验
        $signature = $request->input('signature');
        $rawData = Request::instance()->post('rawData');
        $signature2 = sha1($rawData . $session_key);
        if ($signature != $signature2) {
            return json_encode(['code' => 500, 'msg' => '数据签名验证失败！']);
        }
//        Vendor("PHP.wxBizDataCrypt"); //加载解密文件，在官方有下载
        $encryptedData = $request->input('encryptedData');
        $iv = $request->input('iv');
        $pc = new \WXBizDataCrypt($this->appId, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data); //其中$data包含用户的所有数据
        $data = json_decode($data,true);
        if ($errCode == 0) {
            dump($data);
            die;//打印解密所得的用户信息
        } else {
            echo $errCode;//打印失败信息
        }
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

    //1111
    public function aaad(){
        var_dump(11111);
    }
}
