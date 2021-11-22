<?php
namespace App\Http\Controllers\Shop;
use App\Model\GoodsCarouselModel;
use App\Model\GoodsCategoryModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class HomeGoodsController extends Controller
{
    //首页轮播图
    public function homeCarouselImg()
    {
//        $field = ['goods_id','goods_lord_img'];
        $field = ['goods_id','home_carousel_img'];
        $info = GoodsModel::query()->where(['goods_cate'=>4,'if_show'=>1,'if_disable'=>0])->select($field)->get()->toArray();
        if($info){
            return ['code' => 0, 'msg' => '成功','data'=>$info];
        }
        return ['code' => 200001, 'msg' => '查询失败'];
    }
    //首页展示图
    public function homeShowImg(){
//        $field = ['goods_id','goods_lord_img'];
        $field = ['goods_id','home_show_img'];
        $info = GoodsModel::query()->where(['goods_cate'=>5,'if_show'=>1,'if_disable'=>0])->select($field)->first()->toArray();
        if($info){
            return ['code' => 0, 'msg' => '成功','data'=>$info];
        }
        return ['code' => 200001, 'msg' => '查询失败'];
    }
    //首页分类商品
    public function homeCateGoods()
    {
        $field = ['goods_id','goods_name','goods_lord_img','goods_price','goods_cate'];
        $list = GoodsCategoryModel::query()->whereIn('goods_category_id',[1,2,3])->select(['goods_category_id','goods_category','goods_category_img'])->get()->toArray();
        $goods_category_id = array_column($list,'goods_category_id');
        $goods = GoodsModel::query()->where(['if_show'=>1,'if_disable'=>0])->whereIn('goods_cate',$goods_category_id)->select($field)->get()->toArray();
        $res =  array_column($list,null,'goods_category_id');
        $arr = [];
        foreach ($goods as $item){
            if(isset($arr[$item['goods_cate']])){
                $arr[$item['goods_cate']]['sub'][]= ['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_lord_img'=>$item['goods_lord_img'],'goods_price'=>$item['goods_price']];;
            }else{
                $arr[$item['goods_cate']]['goods_category_id'] = $item['goods_cate'];
                $arr[$item['goods_cate']]['goods_category'] = $res[$item['goods_cate']]['goods_category'];
                $arr[$item['goods_cate']]['goods_category_img'] = $res[$item['goods_cate']]['goods_category_img'];
                $arr[$item['goods_cate']]['sub'][]=['goods_id'=>$item['goods_id'],'goods_name'=>$item['goods_name'],'goods_lord_img'=>$item['goods_lord_img'],'goods_price'=>$item['goods_price']];
            }
        }
        return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
    }
}
?>
