<?php
namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Services\OSS;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        //获取上传的文件
        $file = $request->file('file');
//        var_dump($file);
//        return $file;
        //获取上传图片的临时地址
        $tmppath = $file->getRealPath();
        //生成文件名
        $fileName = rand(1000,9999) . $file->getFilename() . time() .date('ymd') . '.' . $file->getClientOriginalExtension();
        //拼接上传的文件夹路径(按照日期格式1810/17/xxxx.jpg)
        $pathName = date('Y-m/d').'/'.$fileName;
        //上传图片到阿里云OSS
        OSS::publicUpload('fxtht', $pathName, $tmppath, ['ContentType' => $file->getClientMimeType()]);
        //获取上传图片的Url链接
        $Url = OSS::getPublicObjectURL('fxtht', $pathName);
        //返回状态给前端，Laravel框架会将数组转成JSON格式
        return ['code' => 0, 'msg' => '上传成功', 'data' => ['src' => $Url]];
    }
}
