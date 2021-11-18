<?php
namespace App\Http\Controllers\Shop;
use App\Model\GoodsCarouselModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class GoodsController extends Controller
{
    //添加商品
    public function goodsInsert(Request $request)
    {
        $params = $request->all();
        $goods_name = $params['goods_name'];
        $goods_lord_img = $params['goods_lord_img'];
        $goods_carousel = $params['goods_carousel'];//轮播图
        $goods_about = $params['goods_about'];
        $goods_details = $params['goods_details'];
        $goods_size = $params['goods_size'];
        $goods_price = $params['goods_price'];
        $goods_cate = $params['goods_cate'];
        $if_show = $params['if_show'];
        $created_at = date('Y-m-d H:i:s');
        $info = GoodsModel::query()->where(['goods_name'=>$goods_name])->select('*')->get()->toArray();
        if($info){
            return ['code' => 200001, 'msg' => '添加失败,该商品已存在'];
        }
        $goods = GoodsModel::query()->insert(['goods_name'=>$goods_name,'goods_lord_img'=>$goods_lord_img,'goods_about'=>$goods_about,'goods_details'=>$goods_details,'goods_size'=>$goods_size,'goods_price'=>$goods_price,'goods_cate'=>$goods_cate,'if_show'=>$if_show,'created_at'=>$created_at]);
        $goods_info = GoodsModel::query()->where(['goods_name'=>$goods_name])->select('goods_id')->first()->toArray();
        foreach ($goods_carousel as $key =>$v) {
            $carousel = GoodsCarouselModel::query()->insert(['carousel_id' => $goods_info['goods_id'],'goods_img' => $v]);
        }
            if($carousel || $goods){
                return ['code' => 0, 'msg' => '添加成功','data'=>[]];
            }
    }
    //商品详情
    public function goodsDetails(Request $request){
        $params = $request->all();
        $goods_id = $params['goods_id'];
        $field = ['goods_id','goods_name','goods_lord_img','goods_about','goods_details','goods_size','goods_price'];
        $list = GoodsModel::query()->where(['goods_id'=>$goods_id])->select($field)->first()->toArray();
        $carousel = GoodsCarouselModel::query()->where(['carousel_id'=>$goods_id])->select('goods_img')->get()->toArray();
        $data = [
            'goods_id'=>$list['goods_id'],
            'goods_name'=>$list['goods_name'],
            'goods_lord_img'=>$list['goods_lord_img'],
            'goods_about'=>$list['goods_about'],

            'goods_size'=>$list['goods_size'],
            'goods_price'=>$list['goods_price'],
        ];
        $data['carousel'] = array_column($carousel,'goods_img');
        $data['sort'] = [['name'=>'商品详情','content'=>[$list['goods_details_img']]],['name'=>'商品评价','content'=>['暂无评价']]];

        return ['code' => 0, 'msg' => '成功','data'=>$data];
    }
    //全部商品
    public function allGoods(){
        $field = ['goods_id','goods_name','goods_lord_img','goods_price','goods_cate'];
        $goods = GoodsModel::query()->whereNotIn('goods_cate',[4,5])->where(['if_disable'=>0])->select($field)->get()->toArray();
        return ['code' => 0, 'msg' => '成功','data'=>$goods];
    }

    //后台全部商品
    public function adminGoodsList(){
        $field = ['goods_id','goods_name','goods_lord_img','goods_price','goods_cate','goods_size','if_disable'];
        $goods = GoodsModel::query()->whereNotIn('goods_cate',[4,5])->select($field)->get()->toArray();
        return ['code' => 0, 'msg' => '成功','data'=>$goods];
    }
}
?>
