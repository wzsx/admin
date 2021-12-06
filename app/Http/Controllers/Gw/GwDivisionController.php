<?php
namespace App\Http\Controllers\Gw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DivisionModel;
use App\Model\GwDoctorModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class GwDivisionController extends Controller
{
    //科室列表
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

    //修改科室信息
    public function updateDivision(Request $request)
    {
        $params = $request->all();
        if(!isset($params['division_id']) || !isset($params['division_name']) || !isset($params['division_img'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $list = DivisionModel::query()->where('division_id','<>',$params['division_id'])->where('division_name', $params['division_name'])->first();
        if ($list) {
            return ['code' => 10001, 'msg' => '修改失败,已有重复的科室！'];
        }
        $add = DivisionModel::query()->where('division_id',$params['division_id'])->update(['division_name' => $params['division_name'],'division_img' => $params['division_img']]);
        if ($add) {
            return ['code' => 0, 'msg' => '修改成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '修改失败'];
    }

    //删除科室
    public function deleteDivision(Request $request)
    {
        $params = $request->all();
        if(!isset($params['division_id'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $data = GwDoctorModel::query()->where('division_id',$params['division_id'])->select('*')->first();
        if($data){
            return ['code' => 30002, 'msg' => '该科室下存在医生,无法直接删除'];
        }
        $delete = DivisionModel::query()->where('division_id', $params['division_id'])->delete();
        if ($delete) {
            return ['code' => 0, 'msg' => '删除成功'];
        }
        return ['code' => 40001, 'msg' => '删除失败', 'data' => []];
    }
}
?>


