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
class AdminOrderController extends Controller
{
    //根据订单号查订单详情
    public function webOrderdetails(Request $request)
    {
        $params = $request->all();
        $order_no = $params['order_no'];
        $field = ['order_no','status','create_at','order_name','order_phone','receiver_address','desc','total_price','pay_at','shipments_at','complete_date','freight_price','coupon_cut','logistics_company','logistics_odd'];
        $fields = ['goods_id','goods_name','goods_img','goods_size','selling_price','number'];
            $order = OrderModel::query()->where(['order_no'=>$order_no])->select($field)->first()->toArray();
            $order_goods = OrderGoodsModel::query()->where(['order_no'=>$order_no])->select($fields)->get()->toArray();
            $order_info = ['total_price'=>$order['total_price'],'order_no'=>$order['order_no'],'desc'=>$order['desc'],'create_at'=>$order['create_at'],'pay_at'=>$order['pay_at'],'shipments_at'=>$order['shipments_at'],'complete_date'=>$order['complete_date']];
            $consignee_info = ['order_name'=>$order['order_name'],'order_phone'=>$order['order_phone'],'receiver_address'=>$order['receiver_address']];
            $arr = [];
            foreach ($order_goods as $item){
                if(isset($arr[$item['goods_id']])){
                    $arr[$item['goods_id']]['sub'][]= [];
                }else{
                    $arr[$item['goods_id']]['goods_id'] = $item['goods_id'];
                    $arr[$item['goods_id']]['goods_name'] = $item['goods_name'];
                    $arr[$item['goods_id']]['goods_img'] = $item['goods_img'];
                    $arr[$item['goods_id']]['goods_size'] =$item['goods_size'];
                    $arr[$item['goods_id']]['selling_price'] =$item['selling_price'];
                    $arr[$item['goods_id']]['number'] =$item['number'];
                    $arr[$item['goods_id']]['total']=$item['selling_price'] * $item['number'];
                }
            }
            $actual_amount = $order['total_price'] - $order['coupon_cut'];
            $cost_info = ['freight_price'=>$order['freight_price'],'total_price'=>$order['total_price'],'actual_amount'=>$actual_amount,'coupon_cut'=>$order['coupon_cut']];
            $logistics_info = ['logistics_company'=>$order['logistics_company'],'logistics_odd'=>$order['logistics_odd']];
            return ['code' => 2000, 'msg' => '成功','data'=>['order_info'=>$order_info,'consignee_info'=>$consignee_info,'goods_info'=>array_values($arr),'cost_info'=>$cost_info,'logistics_info'=>$logistics_info]];
    }

    //根据订单状态查订单
    public function webStatusOrderList(Request $request)
    {
        $params = $request->all();
        $field = ['order_no','status','create_at','order_name','order_phone','receiver_address','desc'];
        if(isset($params['status']) && isset($params['order_no'])){
            $order = OrderModel::query()->select($field)->where(['status'=>$params['status']])->where('order_no','like','%'.$params['order_no'].'%')->orderBy('create_at','DESC')->paginate(10);
            return ['code' => 0, 'msg' => '成功','data'=>$order];
        }elseif(isset($params['status']) && !isset($params['order_no'])){
            $order = OrderModel::query()->select($field)->where(['status'=>$params['status']])->orderBy('create_at','DESC')->paginate(10);
            return ['code' => 0, 'msg' => '成功','data'=>$order];
        }elseif (!isset($params['status']) && isset($params['order_no'])){
            $order = OrderModel::query()->select($field)->where('order_no','like','%'.$params['order_no'].'%')->orderBy('create_at','DESC')->paginate(10);
            return ['code' => 0, 'msg' => '成功','data'=>$order];
        }
        $order = OrderModel::query()->select($field)->orderBy('create_at','DESC')->paginate(10);
        return ['code' => 0, 'msg' => '成功','data'=>$order];
    }

    //填写物流信息 修改发货状态
    public function webDeliveryStatus(Request $request){
        $params = $request->all();
        if (empty($params['order_no'])||empty($params['logistics_odd'])) {
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $shipments_at = date('Y-m-d H:i:s');
        $order = OrderModel::query()->where(['order_no'=>$params['order_no']])->update(['shipments_at'=>$shipments_at,'logistics_odd'=>$params['logistics_odd'],'status'=>3]);
        if($order){
            return ['code' => 0, 'msg' => '成功','data'=>[]];
        }
        return ['code' => 250001, 'msg' => '失败','data'=>[]];
    }
}
?>
