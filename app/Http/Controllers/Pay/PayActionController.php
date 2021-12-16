<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Http\Controllers\Controller;
class PayActionController extends Controller
{
    public function action()
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

        $response = $app->handlePaidNotify(function($message, $fail){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            // 如果订单不存在 或者 订单已经支付过了
            // 告诉微信，我已经处理完了，订单没找到，别再通知我了

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    // 更新支付时间为当前时间
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    // 用户支付失败
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            // 更新订单状态之类的信息后 保存一下

            return true; // 返回处理完成
        });

        return $response;
    }
}
