<?php
namespace App\Http\Controllers\Cart;
use App\Model\DoctorInfoModel;
use App\Model\DoctorTagModel;
use App\Model\SonSectionModel;
use App\Model\UserEvaluateModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DoctorSectionModel;
use App\Model\AdvisoryLogModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class CartController extends Controller
{
    //加入购物车
    public function addCart()
    {
        $list = CartModel::query()->where('id','<=',9)->select('*')->get()->toArray();
        $arr = [
            'name' => '更多',
            'img'  => 'https://image.kuaiqitong.com/2375phpm94xKy1629079107210816.png'
        ];
        $data = [
          'list'=>$list,
          'more'=>$arr
        ];
        return ['code' => 0, 'msg' => '成功','data'=>$data];
    }

}
?>
