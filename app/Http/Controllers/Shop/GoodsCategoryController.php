<?php
namespace App\Http\Controllers\Shop;
use App\Model\GoodsCategoryModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class GoodsCategoryController extends Controller
{
    //添加分类
    public function goodsCategoryInsert(Request $request)
    {
        $params = $request->all();
        $goods_category = $params['goods_category'];
        $goods_category_img = $params['goods_category_img']??null;
        $created_at = date('Y-m-d H:i:s');
        $cate = GoodsCategoryModel::query()->where(['goods_category'=>$goods_category])->select('*')->get()->toArray();
        if($cate){
            return ['code' => 20001,'msg' => '添加失败'];
        }
        $category = GoodsCategoryModel::query()->insert(['goods_category' => $goods_category,'goods_category_img'=>$goods_category_img,'created_at'=>$created_at]);
        if($category){
            return ['code' => 0, 'msg' => '添加成功','data'=>[]];
        }
        return ['code' => 20001,'msg' => '添加失败'];
    }

    //查询分类列表
    public function goodsCategoryList(){
        $list = GoodsCategoryModel::query()->whereIn('goods_category_id',[1,2,3])->select(['goods_category_id','goods_category','goods_category_img'])->get()->toArray();
        $arr = [
            'goods_category' => '全部商品',
            'goods_category_img'  => 'https://image.kuaiqitong.com/3603phpNkiQeG1633920334211011.png'
        ];
        $data = [
            'list'=>$list,
            'more'=>$arr
        ];
        return ['code' => 0, 'msg' => '成功','data'=>$data];
    }

}
?>
