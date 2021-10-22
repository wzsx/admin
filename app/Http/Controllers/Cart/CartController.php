<?php
namespace App\Http\Controllers\Cart;
use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\SonSectionModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AdvisoryLogModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\services\Doctor\DoctorServices;
class CartController extends Controller
{
    //详情页加入购物车
    public function addCart(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $goods_id = $params['goods_id'];
        $goods_num = $params['goods_num'];
        if (!$goods_id || !$mid || !$goods_num) {
            return ['code' => 500001, 'msg' => '缺少必要参数'];
        }
        $created_at = date('Y-m-d H:i:s');
        $goods = CartModel::query()->where(['mid'=>$mid,'goods_id'=>$goods_id])->select('*')->first();
        if($goods){
            $update_at = date('Y-m-d H:i:s');
            $num = $goods_num + $goods['goods_num'];
            $update = CartModel::query()->where(['mid'=>$mid,'goods_id'=>$goods_id])->update(['goods_num'=>$num,'update_at'=>$update_at]);
            if($update){
                return ['code' => 0, 'msg' => '加入购物车成功','data'=>[]];
            }
            return ['code' => 200001, 'msg' => '添加失败'];
        }
        $insert = CartModel::query()->insert(['mid'=>$mid,'goods_id'=>$goods_id,'goods_num'=>$goods_num,'create_at'=>$created_at]);
        if($insert){
            return ['code' => 0, 'msg' => '加入购物车成功','data'=>[]];
        }
        return ['code' => 200001, 'msg' => '添加失败'];
    }
    //修改购物车
    public function updateCart(Request $request)
    {
        $params = $request->all();
        $mid = $params['mid'];
        $goods_id = $params['goods_id'];
        $goods_num = $params['goods_num'];
        $is_selected = $params['is_selected'];
        if (!$goods_id || !$mid || !$goods_num || !$is_selected) {
            return ['code' => 500001, 'msg' => '缺少必要参数'];
        }
            $update_at = date('Y-m-d H:i:s');
            $update = CartModel::query()->where(['mid'=>$mid,'goods_id'=>$goods_id])->update(['goods_num'=>$goods_num,'update_at'=>$update_at,'is_selected'=>$is_selected]);
            if($update){
                return ['code' => 0, 'msg' => '加入购物车成功','data'=>[]];
            }
            return ['code' => 200001, 'msg' => '添加失败'];
    }
    //购物车列表
    public function cartList(Request $request)
    {
        $params = $request->all();
        $mid = $params['mid'];
        if (!$mid) {
            return ['code' => 500001, 'msg' => '缺少必要参数'];
        }
        $goods = CartModel::query()->where(['mid' => $mid, 'status' => 0])->select(['id','goods_id','goods_num','is_selected'])->get()->toArray();
        $goods_id = array_column($goods, 'goods_id');
        $goods_info = GoodsModel::query()->whereIn('goods_id', $goods_id)->where(['if_disable'=>0])->select('goods_id', 'goods_name', 'goods_lord_img', 'goods_price', 'goods_size')->get()->toArray();
        $goodsArr = array_column($goods_info,'goods_id','goods_id');
        $failure_goods = GoodsModel::query()->whereIn('goods_id', $goods_id)->where(['if_disable'=>1])->select('goods_id', 'goods_name', 'goods_lord_img', 'goods_price', 'goods_size')->get()->toArray();
        $goodsRes = array_column($goods_info,null,'goods_id');
//        var_dump($goodsArr);
        foreach ($goods as $k=>&$v){
            if (!isset($goodsArr[$v['goods_id']])) {
                unset($goods[$k]);
                continue;
            }
            $v['valid'] = [
                $v['goods_name']=$goodsRes[$v['goods_id']]['goods_name'],
            $v['goods_lord_img']=$goodsRes[$v['goods_id']]['goods_lord_img'],
            $v['goods_price']=$goodsRes[$v['goods_id']]['goods_price'],
            $v['goods_size']=$goodsRes[$v['goods_id']]['goods_size']
            ];
//            $v['goods_name']=$goodsRes[$v['goods_id']]['goods_name'];
//            $v['goods_lord_img']=$goodsRes[$v['goods_id']]['goods_lord_img'];
//            $v['goods_price']=$goodsRes[$v['goods_id']]['goods_price'];
//            $v['goods_size']=$goodsRes[$v['goods_id']]['goods_size'];
        }
        return $goods;


    }
}
?>
