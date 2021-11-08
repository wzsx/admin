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
use Illuminate\Support\Carbon;
use App\User;
use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class OrderGoodsController extends Controller
{
  //生成预订单
    public function beforehandOrder(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $name = $params['order_name'];
        $phone = $params['order_phone'];
//        $commodity = json_decode($params['commodity'],true);//所有商品信息
        $commodity=$params['commodity'];
//        $commodity = [['goods_id'=>697239,'goods_img'=>1111,'goods_price'=>39,'number'=>1,'goods_name'=>'清补凉','goods_size'=>1000],['goods_id'=>697238,'goods_img'=>1111,'goods_price'=>39,'number'=>1,'goods_name'=>'艾草','goods_size'=>1000]];
        $gross_price = $params['gross_price'];
        $total_price = $params['total_price'];
        $desc = $params['desc'];//留言
        $freight_price = $params['freight_price'];
        $receiver_address = $params['receiver_address'];
        $coupon_info = $params['coupon_info']??null;
        $order_goods_num = $params['order_goods_num'];//商品总数
        if($coupon_info!=null){
//            $coupon_cut = json_decode($coupon_info,true)['coupon_cut'];
            $coupon_cut = $coupon_info['coupon_cut'];
            $coupon_status = 1;
        }else{
            $coupon_cut = 0;
            $coupon_status = 0;
        }
        $sum = 0;
        foreach ($commodity as $key =>$v) {
            $price = GoodsModel::query()->where(['goods_id'=>$v['goods_id']])->value('goods_price');
            if($price!=$v['goods_price']) {
                return ['code' => 500003, 'msg' => '商品价格不符,请联系客服1'];
            }elseif (($price*$v['number'])!=($v['goods_price']*$v['number'])){
                return ['code' => 500003, 'msg' => '商品价格不符,请联系客服2'];
            }
            $sum +=$v['goods_price']*$v['number'];
        }
            if($sum!=$gross_price){
                return ['code' => 500003, 'msg' => '商品价格不符,请联系客服3'];
            }


        //订单表
        $str = 'FXT'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $openid = ShopUserModel::query()->where(['mid'=>$mid])->value('openid');
        $create_at = date('Y-m-d H:i:s');
        $order = OrderModel::query()->insert(['order_no'=>$str,'openid'=>$openid,'mid'=>$mid,'freight_price'=>$freight_price,'gross_price'=>$gross_price,'total_price'=>$total_price,'desc'=>$desc,'create_at'=>$create_at,'receiver_address'=>$receiver_address,'order_goods_num'=>$order_goods_num,'order_phone'=>$phone,'order_name'=>$name,'coupon_info'=>$coupon_info,'coupon_cut'=>$coupon_cut,'coupon_status'=>$coupon_status]);
        $dis_price = $gross_price - $coupon_cut;
        //写入订单商品表
        $carousel = [];
        foreach ($commodity as $key =>$v) {
            $carousel = OrderGoodsModel::query()->insert(['mid'=>$mid,'order_no'=>$str,'goods_id'=>$v['goods_id'],'goods_name'=>$v['goods_name'],'goods_size'=>$v['goods_size'],'goods_img'=>$v['goods_img'],'selling_price'=>$v['goods_price'],'number'=>$v['number'],'create_at'=>$create_at,'dis_price'=>$dis_price]);
        }
        $goods = OrderGoodsModel::query()->where(['order_no'=>$str])->pluck('goods_id');
        $delete_cat_goods = CartModel::query()->where(['mid'=>$mid])->whereIn('goods_id',$goods)->delete();
        if($carousel || $order||$delete_cat_goods){
//            $job = (new OrderStatus($str))->delay(Carbon::now()->addMinute(15));
//            $this->dispatch($job);
            return ['code' => 0, 'msg' => '生成预订单成功','data'=>['order_no'=>$str]];
        }
    }

    //假支付
    public function ifpay(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $order_no = $params['order_no'];
        $status = $params['status']??0;
        $pay_at = date('Y-m-d H:i:s');
        if($status==1){
            OrderModel::query()->where(['order_no'=>$order_no,'mid'=>$mid])->update(['status'=>2,'is_pay'=>1,'pay_at'=>$pay_at]);
            return ['code' => 0, 'msg' => '支付成功','data'=>[]];
        }
        return ['code' => 500004, 'msg' => '订单支付失败'];
    }

//当前用户的待支付订单
    public function unpaid(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
            ->where(['o.mid'=>$mid,'o.is_pay'=>0,'o.status'=>1])
            ->select('o.order_no','g.goods_id','g.goods_img','g.goods_name','g.selling_price','g.goods_size','g.number')
            ->get()->toArray();
        $arr = [];
        foreach ($order as $item){
            if(isset($arr[$item['order_no']])){
                $arr[$item['order_no']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
            }else{
                $arr[$item['order_no']]['order_no'] = $item['order_no'];
                $arr[$item['order_no']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
            }
        }
        return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
    }

    //当前用户的待发货订单
    public function unshipped(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
            ->where(['o.mid'=>$mid,'o.is_pay'=>1,'o.status'=>2])
            ->select('o.order_no','g.goods_id','g.goods_img','g.goods_name','g.selling_price','g.goods_size','g.number')
            ->get()->toArray();
        $arr = [];
        foreach ($order as $item){
            if(isset($arr[$item['order_no']])){
                $arr[$item['order_no']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
            }else{
                $arr[$item['order_no']]['order_no'] = $item['order_no'];
                $arr[$item['order_no']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
            }
        }
        return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
    }
    //订单列表
    public function orderStatus(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $status = $params['status'];
        if($status == 0){
            $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
                ->where(['o.mid'=>$mid,'o.is_pay'=>0,'o.status'=>$status])
                ->select('o.order_no','g.goods_id','g.goods_img','g.goods_name','g.selling_price','g.goods_size','g.number')
                ->get()->toArray();
            $arr = [];
            foreach ($order as $item){
                if(isset($arr[$item['order_no']])){
                    $arr[$item['order_no']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }else{
                    $arr[$item['order_no']]['order_no'] = $item['order_no'];
                    $arr[$item['order_no']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }
            }
            return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
        }elseif ($status == 1){
            $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
                ->where(['o.mid'=>$mid,'o.is_pay'=>0,'o.status'=>$status])
                ->select('o.order_no','g.goods_id','g.goods_img','g.goods_name','g.selling_price','g.goods_size','g.number')
                ->get()->toArray();
            $arr = [];
            foreach ($order as $item){
                if(isset($arr[$item['order_no']])){
                    $arr[$item['order_no']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }else{
                    $arr[$item['order_no']]['order_no'] = $item['order_no'];
                    $arr[$item['order_no']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }
            }
            return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
        }elseif ($status == 2){
            $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
                ->where(['o.mid'=>$mid,'o.is_pay'=>1,'o.status'=>$status])
                ->select('o.order_no','g.goods_id','g.goods_img','g.goods_name','g.selling_price','g.goods_size','g.number')
                ->get()->toArray();
            $arr = [];
            foreach ($order as $item){
                if(isset($arr[$item['order_no']])){
                    $arr[$item['order_no']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }else{
                    $arr[$item['order_no']]['order_no'] = $item['order_no'];
                    $arr[$item['order_no']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }
            }
            return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
        }elseif ($status == 3){
            $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
                ->where(['o.mid'=>$mid,'o.is_pay'=>1,'o.status'=>$status])
                ->select('o.order_no','g.goods_id','g.goods_img','g.goods_name','g.selling_price','g.goods_size','g.number')
                ->get()->toArray();
            $arr = [];
            foreach ($order as $item){
                if(isset($arr[$item['order_no']])){
                    $arr[$item['order_no']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }else{
                    $arr[$item['order_no']]['order_no'] = $item['order_no'];
                    $arr[$item['order_no']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_img'=>$item['goods_img'],'selling_price'=>$item['selling_price'],'goods_size'=>$item['goods_size'],'number'=>$item['number']];
                }
            }
            return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
        }elseif ($status == 4) {
            $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
                ->where(['o.mid' => $mid, 'o.is_pay' => 1, 'o.status' => $status])
                ->select('o.order_no', 'g.goods_id', 'g.goods_img', 'g.goods_name', 'g.selling_price', 'g.goods_size', 'g.number')
                ->get()->toArray();
            $arr = [];
            foreach ($order as $item) {
                if (isset($arr[$item['order_no']])) {
                    $arr[$item['order_no']]['sub'][] = ['goods_id' => $item['goods_id'], 'goods_name' => $item['goods_name'], 'goods_img' => $item['goods_img'], 'selling_price' => $item['selling_price'], 'goods_size' => $item['goods_size'], 'number' => $item['number']];
                } else {
                    $arr[$item['order_no']]['order_no'] = $item['order_no'];
                    $arr[$item['order_no']]['sub'][] = ['goods_id' => $item['goods_id'], 'goods_name' => $item['goods_name'], 'goods_img' => $item['goods_img'], 'selling_price' => $item['selling_price'], 'goods_size' => $item['goods_size'], 'number' => $item['number']];
                }
            }
            return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
        }elseif ($status == 8){
            $order = OrderModel::query()->from('store_order as o')->join('store_order_goods as g', 'o.order_no', '=', 'g.order_no')
                ->where(['o.mid' => $mid])
                ->select('o.order_no','o.status', 'g.goods_id', 'g.goods_img', 'g.goods_name', 'g.selling_price', 'g.goods_size', 'g.number')
                ->get()->toArray();
            $arr = [];
            foreach ($order as $item) {
                if (isset($arr[$item['order_no']])) {
                    $arr[$item['order_no']]['sub'][] = ['goods_id' => $item['goods_id'], 'goods_name' => $item['goods_name'], 'goods_img' => $item['goods_img'], 'selling_price' => $item['selling_price'], 'goods_size' => $item['goods_size'], 'number' => $item['number']];
                } else {
                    $arr[$item['order_no']]['order_no'] = $item['order_no'];
                    $arr[$item['order_no']]['status'] = $item['status'];
                    $arr[$item['order_no']]['sub'][] = ['goods_id' => $item['goods_id'], 'goods_name' => $item['goods_name'], 'goods_img' => $item['goods_img'], 'selling_price' => $item['selling_price'], 'goods_size' => $item['goods_size'], 'number' => $item['number']];
                }
            }
            return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
        }
    }
    //cs
    public function css(){
        $goods_id = "FXT2021110556484997";
        $job = (new OrderStatus($goods_id))->delay(Carbon::now()->addMinute(2));
        $this->dispatch($job);
//        date_default_timezone_set('PRC');
        var_dump(date('Y-m-d H:i:s'));
    }

    //cs
    public function bss(){
        $goods_id = 697238;
        $job = (new OrderStatus($goods_id))->delay(Carbon::now()->addMinute(1));
        $this->dispatch($job);
//        date_default_timezone_set('PRC');
//        $status = GoodsModel::query()->where(['goods_id'=>697239])->value('if_disable');
//        var_dump($status);
        var_dump(date('Y-m-d H:i:s'));
    }

    public function updateOrder($order_no){
        OrderModel::query()->where(['order_no'=>$order_no])->update(['status'=>0,'is_deleted'=>1]);
        OrderGoodsModel::query()->where(['order_no'=>$order_no])->update(['is_deleted'=>1]);
    }
}
?>
