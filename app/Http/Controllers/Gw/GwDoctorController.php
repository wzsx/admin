<?php
namespace App\Http\Controllers\Gw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GwDoctorModel;
use App\Model\AdvisoryLogModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class GwDoctorController extends Controller
{
    //分类下医生列表
    public function cateDoctorList(Request $request)
    {
        $params = $request->all();
        if(!isset($params['division_id'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $list = GwDoctorModel::query()->where('division_id',$params['division_id'])->select('doctor_id','doctor_name','doctor_img','doctor_info')->get()->toArray();
        return ['code' => 0, 'msg' => '成功', 'data' => $list];
    }
    //专家团队
    public function doctorList()
    {
        $list = GwDoctorModel::query()->select('doctor_id','doctor_name','doctor_img','doctor_info')->get()->toArray();
        return ['code' => 0, 'msg' => '成功', 'data' => $list];
    }

    //医生详情
    public function doctorDetails(Request $request){
        $params = $request->all();
        if(!isset($params['doctor_id'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $details = GwDoctorModel::query()->where('doctor_id',$params['doctor_id'])->select("*")->first()->toArray();
        return ['code' => 0, 'msg' => '成功', 'data' => $details];
    }

    //添加医生
    public function insertDoctor(Request $request)
    {
        $params = $request->all();
        if(!isset($params['doctor_name']) || !isset($params['doctor_img']) || !isset($params['doctor_info']) || !isset($params['good_cure']) || !isset($params['individual_resume']) || !isset($params['doctor_qr']) || !isset($params['assistant_qr']) || !isset($params['division_id'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $list = GwDoctorModel::query()->where('doctor_name', $params['doctor_name'])->first();
        if ($list) {
            return ['code' => 10001, 'msg' => '添加失败，已有重复的！'];
        }
        $add = GwDoctorModel::query()->insert(['doctor_name' => $params['doctor_name'],'doctor_img' => $params['doctor_img'],'doctor_info' => $params['doctor_info'],'good_cure' => $params['good_cure'],'individual_resume' => $params['individual_resume'],'doctor_qr' => $params['doctor_qr'],'assistant_qr' => $params['assistant_qr'],'division_id' => $params['division_id']]);
        if ($add) {
            return ['code' => 0, 'msg' => '添加成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '添加失败'];

    }

    //删除医生
    public function deleteDoctor(Request $request)
    {
        $doctor = $request->input('doctor_id');
        $delete = GwDoctorModel::query()->where('doctor_id', $doctor)->delete();
        if ($delete) {
            return ['code' => 0, 'msg' => '删除成功'];
        }
        return ['code' => 40001, 'msg' => '删除失败', 'data' => []];

    }

    //修改医生信息
    public function updateDoctor(Request $request){
        $params = $request->all();
        if(!isset($params['doctor_id']) || !isset($params['doctor_name']) || !isset($params['doctor_img'])|| !isset($params['doctor_info']) || !isset($params['good_cure']) || !isset($params['individual_resume']) || !isset($params['doctor_qr']) || !isset($params['assistant_qr']) || !isset($params['division_id'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $list = GwDoctorModel::query()->where('doctor_id','<>',$params['doctor_id'])->where('doctor_name', $params['doctor_name'])->first();
        if ($list) {
            return ['code' => 10001, 'msg' => '修改失败,已有重复的医生！'];
        }
        $add = GwDoctorModel::query()->where('doctor_id',$params['doctor_id'])->update(['doctor_name' => $params['doctor_name'],'doctor_img' => $params['doctor_img'],'doctor_info'=>$params['doctor_info'],'good_cure'=>$params['good_cure'],'individual_resume'=>$params['individual_resume'],'doctor_qr'=>$params['doctor_qr'],'assistant_qr'=>$params['assistant_qr'],'division_id'=>$params['division_id']]);
        if ($add) {
            return ['code' => 0, 'msg' => '修改成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '修改失败'];
    }
}
?>


