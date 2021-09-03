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
    /**
     * 义诊科室及医生列表
     * @return array
     */
    public static function freeSectionList(){
        $section = DoctorSectionModel::query()->select('id','section')->get()->toArray();
        $field = ['d.id','section_id','doctor_name','doctor_img','doctor_message','doctor_sort','doctor_school',
            'hospital','sdo','praise','evaluate','inquiry_cost','section','free_cost','if_kab'];
        $doctor_list = DoctorInfoModel::query()->from('doctor_info as d')
            ->join('doctor_section as s','s.id','=','d.section_id')
            ->where(['sort'=>2])
            ->select($field)->get()->toArray();
        $id = array_column($doctor_list,'id','id');
        $tag = DoctorTagModel::query()->whereIn('doctor_id',$id)->select('doctor_id','doctor_tag')->get()->toArray();
        $res = array();
        foreach($tag as $item) {
            if(! isset($res[$item['doctor_id']])) $res[$item['doctor_id']] = $item;
            else $res[$item['doctor_id']]['doctor_tag'] .= ',' . $item['doctor_tag'];
        }
        $arr = array_values($res);
        $ass =(array_column($arr,'doctor_tag','doctor_id'));
        foreach ($doctor_list as $k=>&$v){
            $v['doctor_tag'] = explode(',',$ass[$v['id']]);
        }
        $free_doctor_list = $doctor_list;
        $arrlist = [];
        foreach ($doctor_list as $item){
            if(isset($arrlist[$item['section_id']])){
                $arrlist[$item['section_id']]['sub'][]=
                    ['id'=>$item['id'],
                        'section_id'=>$item['section_id'],
                        'doctor_name'=>$item['doctor_name'],
                        'doctor_img'=>$item['doctor_img'],
                        'doctor_message'=>$item['doctor_message'],
                        'doctor_sort'=>$item['doctor_sort'],
                        'doctor_school'=>$item['doctor_school'],
                        'hospital'=>$item['hospital'],
                        'sdo'=>$item['sdo'],
                        'praise'=>$item['praise'],
                        'evaluate'=>$item['evaluate'],
                        'inquiry_cost'=>$item['inquiry_cost'],
                        'section'=>$item['section'],
                        'free_cost'=>$item['free_cost'],
                        'if_kab'=>$item['if_kab']
                    ];
            }else{
                $arrlist[$item['section_id']]['id'] = $item['section_id'];
                $arrlist[$item['section_id']]['section'] = $item['section'];
                $arrlist[$item['section_id']]['sub'][]=
                    ['id'=>$item['id'],
                        'section_id'=>$item['section_id'],
                        'doctor_name'=>$item['doctor_name'],
                        'doctor_img'=>$item['doctor_img'],
                        'doctor_message'=>$item['doctor_message'],
                        'doctor_sort'=>$item['doctor_sort'],
                        'doctor_school'=>$item['doctor_school'],
                        'hospital'=>$item['hospital'],
                        'sdo'=>$item['sdo'],
                        'praise'=>$item['praise'],
                        'evaluate'=>$item['evaluate'],
                        'inquiry_cost'=>$item['inquiry_cost'],
                        'section'=>$item['section'],
                        'free_cost'=>$item['free_cost'],
                        'if_kab'=>$item['if_kab']
                    ];
            }
        }
        $aes =(array_column($arrlist,'sub','id'));
        foreach ($section as $k=>&$v){
            if(isset($aes[$v['id']])){
                $v['free_section_list'] = $aes[$v['id']];
            }else{
                $v['free_section_list'] = [];
            }

        }
        array_unshift($section,['id'=>0,'section'=>'综合','free_section_list'=>$free_doctor_list]);
        return $section;
    }

    public static function freeDoctorList()
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
