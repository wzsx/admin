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

    private $city = '北京';
    // 经典网络 or VPC
    private $networkType = '经典网络';

    private $AccessKeyId = 'LTAI5tG75B8sj8CML1wxYDwo';
    private $AccessKeySecret = 'EgjimTTl02qxEwBpA1u6qBymk32nQS';
    private $ossClient;
    /**
     * 根据条件筛选医生
     * @param string
     */
    public function __construct($isInternal = false)
    {
        if ($this->networkType == 'VPC' && !$isInternal) {
            throw new Exception("VPC 网络下不提供外网上传、下载等功能");
        }
        $this->ossClient = AliyunOSS::boot(
            $this->city,
            $this->networkType,
            $isInternal,
            $this->AccessKeyId,
            $this->AccessKeySecret
        );
    }
    /**
     * 使用外网上传文件
     * @param  array 查询字段名称
     * @param  array 查询条件上传之后的 OSS object 名称
     * @return array
     */
    public static function doctorList($field, $where)
    {
        $list = DoctorInfoModel::query()->from('doctor_info as d')
                ->join('doctor_section as s','s.id','=','d.section_id')
                ->where($where)
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
    /**
     * 使用阿里云内网上传文件
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 上传文件路径
     * @return boolean 上传是否成功
     */
    public static function privateUpload($bucketName, $ossKey, $filePath, $options = [])
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->uploadFile($ossKey, $filePath, $options);
    }
    /**
     * 使用外网直接上传变量内容
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 上传的变量
     * @return boolean 上传是否成功
     */
    public static function publicUploadContent($bucketName, $ossKey, $content, $options = [])
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->uploadContent($ossKey, $content, $options);
    }
    /**
     * 使用阿里云内网直接上传变量内容
     * @param  string bucket名称
     * @param  string 上传之后的 OSS object 名称
     * @param  string 上传的变量
     * @return boolean 上传是否成功
     */
    public static function privateUploadContent($bucketName, $ossKey, $content, $options = [])
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->uploadContent($ossKey, $content, $options);
    }
    /**
     * 使用外网删除文件
     * @param  string bucket名称
     * @param  string 目标 OSS object 名称
     * @return boolean 删除是否成功
     */
    public static function publicDeleteObject($bucketName, $ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->deleteObject($bucketName, $ossKey);
    }
    /**
     * 使用阿里云内网删除文件
     * @param  string bucket名称
     * @param  string 目标 OSS object 名称
     * @return boolean 删除是否成功
     */
    public static function privateDeleteObject($bucketName, $ossKey)
    {
        $oss = new OSS(true);
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->deleteObject($bucketName, $ossKey);
    }
    /**
     * -------------------------------------------------
     *
     *
     *  下面不再分公网内网出 API，也不注释了，大家自行体会吧。。。
     *
     *
     * -------------------------------------------------
     */
    public function copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS();
        return $oss->ossClient->copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }
    public function moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS();
        return $oss->ossClient->moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }
    // 获取公开文件的 URL
    public static function getPublicObjectURL($bucketName, $ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->getPublicUrl($ossKey);
    }
    // 获取私有文件的URL，并设定过期时间，如 \DateTime('+1 day')
    public static function getPrivateObjectURLWithExpireTime($bucketName, $ossKey, DateTime $expire_time)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->getUrl($ossKey, $expire_time);
    }
    public static function createBucket($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->createBucket($bucketName);
    }
    public static function getAllObjectKey($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->getAllObjectKey($bucketName);
    }
    public static function getObjectMeta($bucketName, $ossKey)
    {
        $oss = new OSS();
        return $oss->ossClient->getObjectMeta($bucketName, $ossKey);
    }
}
