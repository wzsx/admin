<?php
namespace App\Http\Controllers\AppUser;
use App\Http\Controllers\Controller;
use App\Model\DoctorInfoModel;
use App\Model\DrugSpecificationModel;
use App\services\Sms\SmsLog;
use Illuminate\Http\Request;
use App\Model\DoctorSectionModel;
use Illuminate\Support\Facades\Redis;

class InfoController extends Controller
{
   /**
    * 设置个人信息
    */
   public function profile(Request $request){
       $params = $request->all();
//        $doctor_name = $params['doctor_name'];
       $phone = $params['phone'];
       $code = $params['code'];
       $redis = Redis::connection('default');
   }

   /**
    * 更改认证手机号
    */
   public function updatePhone(Request $request){
       $params = $request->all();
       $phone = $params['phone'];
       $code = $params['code'];
       $redis = Redis::connection('default');
       $new_phone = $params['new_phone'];
       $ack_phone = $params['sck_phone'];
       if($code==$redis->get($phone)){
           $user = DoctorInfoModel::query()->where(['phone'=>$phone])->select('*')->first();
           if($user || $new_phone==$ack_phone){
                DoctorInfoModel::query()->where(['phone'=>$phone])->update(['phone'=>$new_phone]);
               return ['code' => 0, 'msg' => '修改成功','data'=>[]];
           }elseif ($user==null){
               return ['code' => 200003, 'msg' => '请填写正确手机号'];
           }elseif ($new_phone!=$ack_phone){
               return ['code' => 200004, 'msg' => '手机号不一致'];
           }
       }
       return ['code' => 200001, 'msg' => '验证码错误'];

   }

   /**
    * 添加药品规格
    */
        public function insert(Request $request){
            $params = $request->all();
            $drug_name = $params['drug_name'];
            $drug_size = $params['drug_size'];
            $drug_price = $params['drug_price'];
            $res = DrugSpecificationModel::query()->where(['drug_name'=>$drug_name])->select('*')->first();
            if($res){
                return ['code' => 400001, 'msg' => '该药品已存在'];
            }
            $arr = DrugSpecificationModel::query()->insert(['drug_name'=>$drug_name,'drug_size'=>$drug_size,'drug_price'=>$drug_price]);
            if($arr){
                return ['code'=>0,'msg'=>'添加成功'];
            }
        }

    /**
     * 删除药品
     */
        public function delete(Request $request){
            $params = $request->all();
            $drug_name = $params['drug_name'];
            $res = DrugSpecificationModel::query()->where(['drug_name'=>$drug_name])->delete();
            if($res){
                return ['code'=>0,'msg'=>'添加成功'];
            }
            return ['code' => 400002, 'msg' => '删除失败'];
        }

    /**
     * 修改药品
     */
        public function update(Request $request){
            $params = $request->all();
            $drug_id = $params['id'];
            $drug_name = $params['drug_name'];
            $drug_size = $params['drug_size'];
            $drug_price = $params['drug_price'];
            $res = DrugSpecificationModel::query()->where(['id'=>$drug_id])->update(['drug_name'=>$drug_name,'drug_size'=>$drug_size,'drug_price'=>$drug_price]);
            if($res){
                return ['code'=>0,'msg'=>'修改成功'];
            }
            return ['code' => 400003, 'msg' => '修改失败'];
        }

    /**
     * 查询药品
     */
        public function select(){
            $res = DrugSpecificationModel::query()->select('*')->get()->toArray();
            return ['code' => 0, 'msg' => '查询成功','data'=>$res];
        }
   /**
    * 创建药品模板
    */

   /**

    */
}

