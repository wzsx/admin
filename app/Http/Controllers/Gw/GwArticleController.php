<?php
namespace App\Http\Controllers\Gw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ArticleInfoModel;

use Illuminate\Routing\Router;
//use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
class GwArticleController extends Controller
{
    //文章列表每页展示3条
    public function articleList()
    {
        $list = ArticleInfoModel::query()->select("*")->orderBy('time_at','DESC')->paginate(3);
        return ['code' => 0, 'msg' => '成功', 'data' => $list];
    }

    //发布文章
    public function insertArticle(Request $request)
    {
        $params = $request->all();
        if(!isset($params['article_title']) || !isset($params['article_img']) || !isset($params['article_content'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $list = ArticleInfoModel::query()->where('article_title', $params['article_title'])->first();
        if ($list) {
            return ['code' => 10001, 'msg' => '文章发布失败,标题已有重复的！'];
        }
        $time = date('Y-m-d H:i:s');
        $add = ArticleInfoModel::query()->insert(['article_title' => $params['article_title'],'article_img' => $params['article_img'],'article_content' => $params['article_content'],'time_at' => $time]);
        if ($add) {
            return ['code' => 0, 'msg' => '发布成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '发布失败,请联系管理人员'];
    }

    //删除文章
    public function deleteArticle(Request $request)
    {
        $article_id = $request->input('article_id');
        $delete = ArticleInfoModel::query()->where('article_id', $article_id)->delete();
        if ($delete) {
            return ['code' => 0, 'msg' => '删除成功'];
        }
        return ['code' => 40001, 'msg' => '删除失败', 'data' => []];
    }

    //修改文章
    public function updateArticle(Request $request){
        $params = $request->all();
        if(!isset($params['article_id']) || !isset($params['article_title']) || !isset($params['article_img']) || !isset($params['article_content'])){
            return ['code' => 30001, 'msg' => '缺少必要参数'];
        }
        $time = date('Y-m-d H:i:s');
        $data = ArticleInfoModel::query()->where('article_id', $params['article_id'])->update(['article_title' => $params['article_title'],'article_img' => $params['article_img'],'article_content' => $params['article_content'],'time_at' => $time]);
        if ($data) {
            return ['code' => 0, 'msg' => '修改成功', 'data' => []];
        }
        return ['code' => 40001, 'msg' => '修改失败,请联系管理人员'];
    }

}
?>


