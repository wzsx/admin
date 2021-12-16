<?php

namespace App\Http\Controllers\Pay;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use EasyWeChat\Factory;

class PayController extends Controller
{
    public function pay()
    {
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

        $result = $app->order->unify([
            'body' => '商品测试',
            'out_trade_no' => time(),
            'total_fee' => 1,
            'spbill_create_ip' => Request()->getClientIp(), // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => 'https://api.kuaiqitong.com/wxpay/pay_action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'NATIVE', // 请对应换成你的支付方式对应的值类型
            // 'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjxxxxx',
        ]);

        return $result;
    }
}
