<?php
namespace App\Http\Controllers\Home;
use App\Model\DoctorInfoModel;
use App\Model\DoctorTagModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DoctorSectionModel;
use App\Model\AdvisoryLogModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{
    //首页科室列表
    public function index()
    {
        $list = DoctorSectionModel::query()->select('*')->get()->toArray();
        return ['code' => 0, 'msg' => '成功','data'=>$list];
    }
    //知名专家与三甲专家义诊首页展示
    public function doctorShow(){
        $field = ['id','doctor_name','doctor_img','doctor_sort','doctor_school'];
        $fields = ['id','doctor_name','doctor_img','doctor_sort','doctor_school','sdo'];
        //名医
        $expertDoc = DoctorInfoModel::query()->where('sort',1)->select($field)->get()->toArray();
        //义诊
        $msapDoc = DoctorInfoModel::query()->where('sort',2)->select($fields)->get()->toArray();
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
}
?>
