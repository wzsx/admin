<?php
namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\services\OSS;
use Illuminate\Http\Request;

class WxController extends Controller
{
    var $appId = 'wx5c3075128baa7866';
    var $appSecret = '2af4b5b48cbde19665fa6a1209c8ac12';

    //获取用户登录信息
    public function codeSession(Request $request)
    {
        var_dump(111);
//        $code = $request->input('code');
////        $code = '083s76100yJGAM1XrQ300iFcDb3s761W';
//        $url=sprintf(config('wechatUrl.url'),config('wechatUrl.appid'),config('wechatUrl.secret'),$code);
//        $client=new Client(['timeout'=>5,'verify'=>false]);
//        $res=$client->post($url);
//        $data=(string)$res->getBody();
//        $arr=json_decode($data,true);
//        return ['code'=>200,'msg'=>'ok','data'=>$arr];
    }
}
