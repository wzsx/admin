<?php

namespace App\Http\Controllers\Pay;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ShopUserModel;
use EasyWeChat\Factory;
use function EasyWeChat\Kernel\Support\generate_sign;

class PayController extends Controller
{
    public function pay(Request $request)
    {
        $params = $request->all();
        if(!isset($params['mid']) || !isset($params['order_no']) || !isset($params['total_price'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $openid = ShopUserModel::query()->where(['mid'=>$params['mid']])->value('openid');
//        $openid = 'oc0vy4jOd6EdoUQbklJk9HxHSe3Q';
        $config = [
            // 必要配置
            'app_id' => 'wx5c3075128baa7866',
            'mch_id' => '1617885587',
            'key' => '13949147108Dfcw18703979016Dfcw77',   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => 'path/to/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path'           => 'path/to/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！

            'notify_url'         => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
        ];

        $app = Factory::payment($config);

        $unify = $app->order->unify([
            'body' => '商品',
            'out_trade_no' => $params['order_no'],
//            'out_trade_no' => time(),
            'total_fee' =>bcmul($params['total_price'],100,2) ,
//            'total_fee' => 1,
            'spbill_create_ip' => Request()->getClientIp(), // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => 'https://api.kuaiqitong.com/wxpay/pay_action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $openid,
        ]);
        if ($unify['return_code'] === 'SUCCESS' && !isset($unify['err_code'])) {
            $pay = [
                'appId' =>'wx5c3075128baa7866',
                'timeStamp' => (string) time(),
                'nonceStr' => $unify['nonce_str'],
                'package' => 'prepay_id=' . $unify['prepay_id'],
                'signType' => 'MD5',
            ];

            $pay['paySign'] = generate_sign($pay, '13949147108Dfcw18703979016Dfcw77');

            return $pay;
        } else {
            $unify['return_code'] = 'FAIL';
            return $unify;
        }
    }
}
