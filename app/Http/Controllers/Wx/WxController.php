<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
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
        $data=(string)$res->getBody();
        $arr=json_decode($data,true);
        var_dump($arr);
//        return ['code'=>200,'msg'=>'ok','data'=>['user_id'=>$arr['openid']]];
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
