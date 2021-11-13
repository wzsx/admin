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
    //订单列表
    public function webOrderList()
    {
        $field = ['order_no','status','create_at','order_name','order_phone','receiver_address','desc'];
        $order = OrderModel::query()->select($field)->orderBy('create_at','DESC')->get()->toArray();
        return ['code' => 0, 'msg' => '成功','data'=>$order];
    }
    //根据订单号查订单详情
    public function webOrderdetails(Request $request){
        $params = $request->all();
        $order_no = $params['order_no'];
        $field = [''];
        $arr =[];
        return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
    }
}
?>
