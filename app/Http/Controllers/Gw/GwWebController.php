<?php
namespace App\Http\Controllers\Gw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DivisionModel;
use App\Model\AdvisoryLogModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class GwWebController extends Controller
{
    public function index()
    {
//        $list = DoctorSectionModel::query()->select('id','section')->get()->toArray();
        $list = DoctorSectionModel::query()->pluck('section')->toArray();
        return $list;
//        var_dump($list);
//        $data = [
//          'id' =>$list['id'],
//          'section' =>$list['section']
//        ];
//        return $data;
    }

    public function divisionList()
    {
        $list = DivisionModel::query()->select("*")->get()->toArray();
        return ['code' => 0, 'msg' => '成功', 'data' => $list];
    }

    //添加科室
    public function insertDivision(Request $request)
    {
        $params = $request->all();
        if(!isset($params['division_name']) || !isset($params['division_img'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $list = DivisionModel::query()->where('division_name', $params['division_name'])->first();
        if ($list) {
            return ['code' => 10001, 'msg' => '添加失败，已有重复的！'];
        }
        $add = DivisionModel::query()->insert(['division_name' => $params['division_name'],'division_img' => $params['division_img']]);
        if ($add) {
            return ['code' => 0, 'msg' => '添加成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '添加失败'];
    }

    //删除科室
    public function deleteSection(Request $request)
    {
        $section = $request->input('section');
        $delete = DoctorSectionModel::query()->where('section', $section)->delete();
        if ($delete) {
            return ['code' => 40001, 'msg' => '删除失败', 'data' => []];
        }
        return ['code' => 0, 'msg' => '删除成功'];
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
            return ['code' => 0, 'msg' => '预约成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '预约失败,请重试'];
    }

    //消息列表
    public function getList()
    {
        $list = AdvisoryLogModel::query()->get()->toArray();
        $data = [
        'list'=>$list
        ];
        return view('web.list',$data);

    }
}
?>


