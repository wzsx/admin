<?php
namespace App\Http\Controllers\Web;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DoctorSectionModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class WebController extends Controller
{
    public function index(){
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
    public function doc(){
        $list = DoctorSectionModel::query()->join('doctor_info')->sum();
    }
}
?>


