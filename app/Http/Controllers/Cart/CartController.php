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
        $goods_num = $params['goods_num']??0;
//        $is_selected = $params['is_selected']??0;
        if (!$goods_id || !$mid) {
            return ['code' => 500001, 'msg' => '缺少必要参数'];
        }
        if($goods_num==0){
            $delete =  CartModel::query()->where(['mid'=>$mid,'goods_id'=>$goods_id])->delete();
            if($delete){
                return ['code' => 0, 'msg' => '删除成功','data'=>[]];
            }
            return ['code' => 300001, 'msg' => '删除失败'];
        }
            $update_at = date('Y-m-d H:i:s');
//            $update = CartModel::query()->where(['mid'=>$mid,'goods_id'=>$goods_id])->update(['goods_num'=>$goods_num,'update_at'=>$update_at,'is_selected'=>$is_selected]);
        $update = CartModel::query()->where(['mid'=>$mid,'goods_id'=>$goods_id])->update(['goods_num'=>$goods_num,'update_at'=>$update_at]);
            if($update){
                return ['code' => 0, 'msg' => '成功','data'=>[]];
            }
            return ['code' => 200001, 'msg' => '操作失败'];
    }
    //购物车列表
    public function cartList(Request $request)
    {
        $params = $request->all();
        $mid = $params['mid'];
        if (!$mid) {
            return ['code' => 500001, 'msg' => '缺少必要参数'];
        }
        $goods = CartModel::query()->where(['mid' => $mid, 'status' => 0])->select(['id','goods_id','goods_num'])->get()->toArray();
        $goods_id = array_column($goods, 'goods_id');
        $goods_on = $goods;
        $goods_info = GoodsModel::query()->whereIn('goods_id', $goods_id)->where(['if_disable'=>0])->select('goods_id', 'goods_name', 'goods_lord_img', 'goods_price', 'goods_size')->get()->toArray();
        $goodsArr = array_column($goods_info,'goods_id','goods_id');
        $failure_goods = GoodsModel::query()->whereIn('goods_id', $goods_id)->where(['if_disable'=>1])->select('goods_id', 'goods_name', 'goods_lord_img', 'goods_price', 'goods_size')->get()->toArray();
        $goodsRes = array_column($goods_info,null,'goods_id');
        $failuresArr = array_column($failure_goods,'goods_id','goods_id');
        $failuresRes = array_column($failure_goods,null,'goods_id');
        foreach ($goods as $k=>&$v){
            if (!isset($goodsArr[$v['goods_id']])) {
                unset($goods[$k]);
                continue;
            }
            $v['goods_name']=$goodsRes[$v['goods_id']]['goods_name'];
            $v['goods_lord_img']=$goodsRes[$v['goods_id']]['goods_lord_img'];
            $v['goods_price']=$goodsRes[$v['goods_id']]['goods_price'];
            $v['goods_size']=$goodsRes[$v['goods_id']]['goods_size'];
        }
        foreach ($goods_on as $a=>&$s){
            if (!isset($failuresArr[$s['goods_id']])) {
                unset($goods_on[$a]);
                continue;
            }
            $s['goods_name']=$failuresRes[$s['goods_id']]['goods_name'];
            $s['goods_lord_img']=$failuresRes[$s['goods_id']]['goods_lord_img'];
            $s['goods_price']=$failuresRes[$s['goods_id']]['goods_price'];
            $s['goods_size']=$failuresRes[$s['goods_id']]['goods_size'];
        }
        return ['code' => 0, 'msg' => '成功','data'=>['valid'=>array_values($goods),'failure'=>array_values($goods_on)]];

    }

    //购物车全选反全选
    public function goodsChecked(Request $request){
        $params = $request->all();
        $mid = $params['mid'];
        $is_selected = $params['is_selected'];
        if (!$mid) {
            return ['code' => 500001, 'msg' => '缺少必要参数'];
        }
         CartModel::query()->where(['mid'=>$mid])->update(['is_selected'=>$is_selected]);
        return ['code' => 0, 'msg' => '操作成功','data'=>[]];
    }
}
?>
