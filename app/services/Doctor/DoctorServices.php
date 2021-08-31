<?php
namespace App\services\Doctor;
use Exception;
use DateTime;
use App\Model\DoctorSectionModel;
use App\Model\AdvisoryLogModel;
use App\Model\DoctorInfoModel;
use App\Model\DoctorTagModel;
use App\Model\SonSectionModel;
use App\Model\UserEvaluateModel;
class DoctorServices {

    /**
     * 查询医生筛选列表
     * @param  array 查询字段名称
     * @param  array 查询条件上
     * @param  array 查询在两个值之间的
     * @return array
     */
    public static function doctorList($field, $where,$whereBetween)
    {
        $list = DoctorInfoModel::query()->from('doctor_info as d')
                ->join('doctor_section as s','s.id','=','d.section_id')
                ->where($where)
                ->whereBetween('inquiry_cost',$whereBetween)
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
            return $list;
    }
}
