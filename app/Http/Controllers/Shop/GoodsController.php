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
//        $goods_id = session_create_id();
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
//        var_dump($goods_id);
//        var_dump($goods_info['goods_id']);
        foreach ($goods_carousel as $key =>$v) {
//            var_dump($v);
//            var_dump($goods_info['goods_id']);
            $carousel = GoodsCarouselModel::query()->insert(['carousel_id' => $goods_info['goods_id'],'goods_img' => $v]);
        }
            if($carousel || $goods){
                return ['code' => 0, 'msg' => '添加成功','data'=>[]];
            }
//        $carousel = GoodsCarouselModel::query()->insert();
//        return ['code' => 0, 'msg' => '成功','data'=>$data];
    }
    //



    //知名专家与三甲专家义诊首页展示
    public function doctorShow(){
        $field = ['id','doctor_name','doctor_img','doctor_sort','doctor_school'];
        $fields = ['id','doctor_name','doctor_img','doctor_sort','doctor_school','sdo'];
        //名医
        $expertDoc = DoctorInfoModel::query()->where(['sort'=>1,'if_kab'=>1])->select($field)->get()->toArray();
        //义诊
        $msapDoc = DoctorInfoModel::query()->where(['sort'=>2,'if_kab'=>1])->select($fields)->get()->toArray();
        $list = [
            'expertDoc' => $expertDoc,
            'msapDoc' => $msapDoc
        ];
        return ['code' => 0, 'msg' => '成功','data'=>$list];
    }
    //首页医生列表
    public function fileList()
    {
        $field = ['d.id','section_id','doctor_name','doctor_img','doctor_message','doctor_sort','doctor_school',
        'hospital','sdo','praise','evaluate','inquiry_cost','section'];
        $list = DoctorInfoModel::query()->from('doctor_info as d')
            ->join('doctor_section as s','s.id','=','d.section_id')
            ->where('sort','!=',2)
        ->select($field)->get()->toArray();
        $id = array_column($list,'id','id');
        $tag = DoctorTagModel::query()->whereIn('doctor_id',$id)->select('doctor_id','doctor_tag')->get()->toArray();
        $res = array();
        foreach($tag as $item) {
            if(! isset($res[$item['doctor_id']])) $res[$item['doctor_id']] = $item;
            else $res[$item['doctor_id']]['doctor_tag'] .= ',' . $item['doctor_tag'];
        }
        $arr = array_values($res);
        $ass =(array_column($arr,'doctor_tag','doctor_id'));
        foreach ($list as $k=>&$v){
            $v['doctor_tag'] = explode(',',$ass[$v['id']]);
        }
        return ['code' => 0, 'msg' => '成功','data'=>$list];
    }
    //医生详情
    public function doctorDetails(Request $request){
        $params = $request->all();
        $id = $params['id'];
        $field = ['d.id','section_id','doctor_name','doctor_img','doctor_message','doctor_sort',
            'hospital','sdo','work_years','doctor_vita','section','inquiry_cost'];
        $list = DoctorInfoModel::query()->from('doctor_info as d')
            ->join('doctor_section as s','s.id','=','d.section_id')
            ->where('d.id',$id)
            ->select($field)->get()->toArray();
        $id = array_column($list,'id','id');
        $num = UserEvaluateModel::query()->where('doctor_id',$id)->count('*');
        $evaluate = UserEvaluateModel::query()->where('doctor_id',$id)->select('evaluate','datetime')->get()->toArray();
        $list[0]['evaluate_num'] = $num;
        $list[0]['patient_evaluate'] = $evaluate;
        return ['code' => 0, 'msg' => '成功','data'=>$list];
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
