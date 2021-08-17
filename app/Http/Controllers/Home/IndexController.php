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
        return ['code' => 20003, 'msg' => '成功','data'=>$list];
    }
    //知名专家与三甲专家义诊首页展示
    public function doctorShow(){
        $field = ['id','doctor_name','doctor_img','doctor_sort','doctor_school'];
        //名医
        $expertDoc = DoctorInfoModel::query()->where('sort',1)->select($field)->get()->toArray();
        //义诊
        $msapDoc = DoctorInfoModel::query()->where('sort',2)->select($field)->get()->toArray();
        $list = [
            'expertDoc' => $expertDoc,
            'msapDoc' => $msapDoc
        ];
        return ['code' => 20001, 'msg' => '成功','data'=>$list];
    }
    //首页医生列表
    public function fileList()
    {
        $field = ['d.id','section_id','doctor_name','doctor_img','doctor_message','doctor_sort','doctor_school',
        'hospital','sdo','praise','evaluate','patient_num','inquiry_cost','sort','section'];
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
        return ['code' => 20002, 'msg' => '成功','data'=>$list];
    }

    //添加科室
    public function addSection(Request $request)
    {
        $section = $request->input('section');
        $list = DoctorSectionModel::query()->where('section', $section)->first();
        if ($list) {
            return ['code' => 10001, 'msg' => '添加失败，已有重复的！'];
        }
        $add = DoctorSectionModel::query()->insert(['section' => $section]);
        if ($add) {
            return ['code' => 200, 'msg' => '添加成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '添加失败'];
    }

    //删除科室
    public function deleteSection(Request $request)
    {
        $section = $request->input('section');
        $delete = DoctorSectionModel::query()->where('section', $section)->delete();
        if ($delete) {
            return ['code' => 200, 'msg' => '删除失败', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '删除成功'];
    }

    //添加联系我们
    public function contactUs(Request $request){
        $params = $request->all();
        $userName = $params['user_name'];
        $tel = $params['tel'];
        $section = $params['section'];
        $info = $params['info'];
        $time = date("Y-m-d H:i:s");
        if (!$userName || !$tel || !$section || !$info) {
            return ['code' => 10001, 'msg' => '缺少必要参数,请按规则填写'];
        }
        $list = AdvisoryLogModel::query()->insert(['user_name'=>$userName,'tel'=>$tel,'section'=>$section,'info'=>$info,'datetime'=>$time]);
        if($list){
            return ['code' => 200, 'msg' => '预约成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '预约失败,请重试'];
    }

    //消息列表
    public function getList()
    {
        $list = AdvisoryLogModel::query()->get()->toArray();
//        var_dump($list);
//        $a = 1111;
        $data = [
//            'list'  => $list
            'list'=>$list
        ];
        return view('web.list',$data);

    }
}
?>
