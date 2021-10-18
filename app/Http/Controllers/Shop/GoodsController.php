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
        $goods_carousel= ['https://image.kuaiqitong.com/3598phpjQARoC1634086598211013.png','https://image.kuaiqitong.com/3598phpjQARoC1634086598211013.png','https://image.kuaiqitong.com/3598phpjQARoC1634086598211013.png'];
        $goods_name = $params['goods_name'];
        $goods_lord_img = $params['goods_lord_img'];
//        $goods_carousel = $params['goods_carousel'];//轮播图
        $goods_about = $params['goods_about'];
        $goods_details_img = $params['goods_details_img'];
        $goods_price = $params['goods_price'];
        $goods_cate = $params['goods_cate'];
        $if_show = $params['if_show'];
        $created_at = date('Y-m-d H:i:s');
        $info = GoodsModel::query()->where(['goods_name'=>$goods_name])->select('*')->get()->toArray();
        if($info){
            return ['code' => 200001, 'msg' => '添加失败,该商品已存在'];
        }
        $goods = GoodsModel::query()->insert(['goods_name'=>$goods_name,'goods_lord_img'=>$goods_lord_img,'goods_about'=>$goods_about,'goods_details_img'=>$goods_details_img,'goods_price'=>$goods_price,'goods_cate'=>$goods_cate,'if_show'=>$if_show,'created_at'=>$created_at]);
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
        $field = ['goods_id','goods_name','goods_about','goods_details_img','goods_price'];
        $list = GoodsModel::query()->where(['goods_id'=>$goods_id])->select($field)->first()->toArray();
        $carousel = GoodsCarouselModel::query()->where(['carousel_id'=>$goods_id])->select('goods_img')->get()->toArray();
        $data = [
            'goods_id'=>$list['goods_id'],
            'goods_name'=>$list['goods_name'],
            'goods_about'=>$list['goods_about'],
            'goods_price'=>$list['goods_price'],
        ];
        $data['carousel'] = array_column($carousel,'goods_img');
        $data['sort'] = [['goods_details'=>'商品详情','goods_details_img'=>[$list['goods_details_img']]],['reviews'=>'商品评价','evaluate'=>['暂无评价']]];

        return ['code' => 0, 'msg' => '成功','data'=>$data];
    }
    //全部商品
    public function allGoods(){
        $field = ['goods_id','goods_name','goods_lord_img','goods_price','goods_cate'];
        $goods = GoodsModel::query()->whereNotIn('goods_cate',[4,5])->select($field)->get()->toArray();
        return ['code' => 0, 'msg' => '成功','data'=>$goods];
    }
    //科室列表
    public function sectionList(){
        $field = ['d.id','d.section','s.s_id','s.section_id','s.son_section_name'];
        $list = DoctorSectionModel::query()->from('doctor_section as d')
            ->join('son_section as s','s.section_id','=','d.id')
            ->select($field)->get()->toArray();
        $arr = [];
        foreach ($list as $item){

            if(isset($arr[$item['id']])){
                $arr[$item['id']]['sub'][]= ['s_id'=>$item['s_id'],'son_section_name'=>$item['son_section_name']];
            }else{
                $arr[$item['id']]['id'] = $item['id'];
                $arr[$item['id']]['section'] = $item['section'];
                $arr[$item['id']]['sub'][]=['s_id'=>$item['s_id'],'son_section_name'=>$item['son_section_name']];
            }
        }
        return ['code' => 0, 'msg' => '成功','data'=>array_values($arr)];
    }
    //筛选医生列表
    public function filterList(Request $request){
        $params = $request->all();
        $section_id = $params['section_id']??null;
        $s_id = $params['s_id']??null;
        $price = json_decode($params['price'],true)??null;
        $doctor_sort = $params['doctor_sort']??null;
        $field = ['d.id','section_id','doctor_name','doctor_img','doctor_message','doctor_sort','doctor_school',
            'hospital','sdo','praise','evaluate','inquiry_cost','section'];
        if($section_id!=null){
            if (!$s_id && $price) {
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id],$price);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }elseif ($s_id && !$price){
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id,'s_id'=>$s_id],[0,1000]);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }elseif(!$s_id && !$price && !$doctor_sort){
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id],[0,1000]);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }elseif ($s_id && $price){
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id,'s_id'=>$s_id],[$price]);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }elseif (!$s_id && !$price && $doctor_sort){
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id,'doctor_sort'=>$doctor_sort],[0,1000]);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }elseif (!$s_id && $price && $doctor_sort){
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id,'doctor_sort'=>$doctor_sort],$price);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }elseif ($s_id && $price && $doctor_sort){
                $list = DoctorServices::doctorList($field,['section_id'=>$section_id,'s_id'=>$s_id,'doctor_sort'=>$doctor_sort],$price);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }
        }
        if($price!=null){
            if($doctor_sort){
                $list = DoctorServices::doctorList($field,['doctor_sort'=>$doctor_sort],$price);
                return ['code' => 0, 'msg' => '成功','data'=>$list];
            }
            $list = DoctorServices::doctorList($field,[],$price);
            return ['code' => 0, 'msg' => '成功','data'=>$list];
        }
        if($doctor_sort!=null){
            $list = DoctorServices::doctorList($field,['doctor_sort'=>$doctor_sort],[0,1000]);
            return ['code' => 0, 'msg' => '成功','data'=>$list];
        }
        }
        //义诊科室列表
        public function freeSectionList(){
            $list = DoctorServices::freeSectionList();
            return ['code' => 0, 'msg' => '成功','data'=>$list];
        }
}
?>
