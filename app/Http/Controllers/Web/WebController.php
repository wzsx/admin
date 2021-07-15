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
        $list = DoctorSectionModel::query()->get()->toArray();
        return $list;
    }
}
?>
