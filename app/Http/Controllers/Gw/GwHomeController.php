<?php
namespace App\Http\Controllers\Gw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GwCarouselModel;
use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class GwHomeController extends Controller
{
    //官网首页轮播图列表
    public function carouselList()
    {
        $list = GwCarouselModel::query()->select('id','carousel')->get()->toArray();
        return ['code' => 0, 'msg' => '成功', 'data' => $list];
    }

    //添加轮播图
    public function insertCarousel(Request $request)
    {
        $params = $request->all();
        if(!isset($params['carousel'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $add = GwCarouselModel::query()->insert(['carousel' => $params['carousel']]);
        if ($add) {
            return ['code' => 0, 'msg' => '添加成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '添加失败'];

    }

    //删除轮播图
    public function deleteCarousel(Request $request)
    {
        $doctor = $request->input('id');
        $delete = GwCarouselModel::query()->where('id', $doctor)->delete();
        if ($delete) {
            return ['code' => 0, 'msg' => '删除成功'];
        }
        return ['code' => 40001, 'msg' => '删除失败', 'data' => []];

    }

    //修改轮播图
    public function updateCarousel(Request $request){
        $params = $request->all();
        if(!isset($params['id']) || !isset($params['carousel'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $add = GwCarouselModel::query()->where('id',$params['id'])->update(['carousel' => $params['carousel']]);
        if ($add) {
            return ['code' => 0, 'msg' => '修改成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '修改失败'];
    }
}
?>


