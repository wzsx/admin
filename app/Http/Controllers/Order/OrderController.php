<?php
namespace App\Http\Controllers\Order;
use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderGoodsModel;
use App\Model\OrderModel;
use App\Model\ShopUserModel;
use App\Model\SonSectionModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AdvisoryLogModel;
use App\Jobs\OrderStatus;
use App\User;
use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class OrderController extends Controller
{
  //生成预订单
    public function beforehandOrder(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $name = $params['order_name'];
        $phone = $params['order_phone'];
//        $commodity = json_decode($params['commodity'],true);//所有商品信息
        $commodity=$params['commodity'];
        $gross_price = $params['gross_price'];
        $total_price = $params['total_price'];
        $desc = $params['desc'];//留言
        $freight_price = $params['freight_price'];
        $receiver_address = $params['receiver_address'];
        $coupon_info = $params['coupon_info']??null;
        $order_goods_num = $params['order_goods_num'];//商品总数
        if($coupon_info!=null){
            $coupon_cut = json_decode($coupon_info,true)['coupon_cut'];
            $coupon_status = 1;
        }else{
            $coupon_cut = 0;
            $coupon_status = 0;
        }
//        return $commodity;
        $sum = 0;
        foreach ($commodity as $key =>$v) {
            $price = GoodsModel::query()->where(['goods_id'=>$v['goods_id']])->pluck('goods_price');
            return ['code'=>$price,'data'=>$v['goods_id']];
//            if($price!=$v['goods_price']) {
//                return ['code' => 500003, 'msg' => '商品价格不符,请联系客服'];
//            }elseif (($price*$v['number'])!=($v['goods_price']*$v['number'])){
//                return ['code' => 500003, 'msg' => '商品价格不符,请联系客服'];
//            }
//            $sum +=$v['goods_price']*$v['number'];
//            if($sum!=$gross_price){
//                return ['code' => 500003, 'msg' => '商品价格不符,请联系客服'];
//            }
        }

//        //订单表
//        $str = 'FXT'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
//        $openid = ShopUserModel::query()->where(['mid'=>$mid])->select('openid')->first()->toArray();
//        $create_at = date('Y-m-d H:i:s');
//        $order = OrderModel::query()->insert(['order_no'=>$str,'openid'=>$openid,'mid'=>$mid,'freight_price'=>$freight_price,'goods_price'=>$gross_price,'total_price'=>$total_price,'desc'=>$desc,'create_at'=>$create_at,'receiver_address'=>$receiver_address,'order_goods_num'=>$order_goods_num,'order_phone'=>$phone,'order_name'=>$name,'coupon_info'=>$coupon_info,'coupon_cut'=>$coupon_cut,'coupon_status'=>$coupon_status]);
//        $dis_price = $gross_price - $coupon_cut;
//        foreach ($commodity as $key =>$v) {
//            $carousel = OrderGoodsModel::query()->insert(['mid'=>$mid,'order_no'=>$str,'goods_id'=>$v['goods_id'],'goods_title'=>$v['goods_name'],'goods_size'=>$v['goods_size'],'goods_img'=>$v['goods_img'],'selling_price'=>$v['goods_price'],'number'=>$v['number'],'create_at'=>$create_at,'dis_price'=>$dis_price]);
//        }
//        if($carousel || $order){
//            $job = (new OrderStatus($str))->delay(900);
//            $this->dispatch($job);
//            return ['code' => 0, 'msg' => '生成预订单成功','data'=>['order_no'=>$str]];
//        }
    }



    //cs
    public function css(){
//        $a=[['price'=>5,'number'=>3],['price'=>6,'number'=>1],['price'=>4,'number'=>6]];
//        $b = 45;
//        $sum = 0;
//        foreach ($a as $k=>$v){
//            $sum += $v['price']*$v['number'];
//        }
//        var_dump($sum);
        $goods_id = 697239;
//        $user = App\User::findOrFail($id);
        $job = (new OrderStatus($goods_id))->delay(180);
        $this->dispatch($job);
//        $this->dispatch(new OrderStatus($goods_id))->delay(180);
    }
}
?>
