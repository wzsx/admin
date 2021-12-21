<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/index','Web\WebController@index');
Route::delete('/section/delete','Web\WebController@deleteSection');
Route::post('/section/add','Web\WebController@addSection');
Route::post('/section/subscribe','Web\WebController@contactUs');
Route::get('/section/getlist','Web\WebController@getList');
Route::post('/upload','Upload\UploadController@upload');
Route::get('/home/index','Home\IndexController@index');
Route::get('/home/docshow','Home\IndexController@doctorShow');
Route::get('/home/filelist','Home\IndexController@fileList');
Route::post('/home/docdetails','Home\IndexController@doctorDetails');
Route::get('/section/sectionlist','Home\IndexController@sectionList');
Route::post('/section/filterlist','Home\IndexController@filterList');
Route::get('/section/freesectionlist','Home\IndexController@freeSectionList');
Route::post('/sms/sendcode','Sms\SmsLogController@sendCode');
Route::post('/user/register','AppUser\UserController@register');
//商城
Route::get('/category/goodscategorylist','Shop\GoodsCategoryController@goodsCategoryList');
Route::post('/admin/category/insert','Shop\GoodsCategoryController@goodsCategoryInsert');
Route::post('/admin/goods/insert','Shop\GoodsController@goodsInsert');
Route::get('/home/carouselimg','Shop\HomeGoodsController@homeCarouselImg');
Route::get('/home/showimg','Shop\HomeGoodsController@homeShowImg');
Route::get('/home/categoods','Shop\HomeGoodsController@homeCateGoods');
Route::post('/goods/details','Shop\GoodsController@goodsDetails');
Route::get('/goods/all','Shop\GoodsController@allGoods');
Route::post('/goods/search','Shop\GoodsController@goodsNameSelect');
Route::post('/goods/pricerank','Shop\GoodsController@priceRank');
//购物车
Route::post('/cart/add','Cart\CartController@addCart');
Route::post('/cart/update','Cart\CartController@updateCart');
Route::post('/cart/list','Cart\CartController@cartList');
Route::post('/cart/checked','Cart\CartController@goodsChecked');
Route::post('/cart/deleteloses','Cart\CartController@deleteLoseGoods');
//Wx
Route::get('/wx/code','Wx\WxController@codeSession');

Route::get('/wx/session','Wx\WxController@Session');
Route::get('/wx/sa','Wx\WxController@weappLogin');
Route::get('/wx/wxlogin','Wx\WxController@wxLogin');
Route::get('/wx/ade','Wx\WxController@ades');

//预订单
Route::post('/order/beforehand','Order\OrderController@beforehandOrder');
Route::post('/order/ifpay','Order\OrderController@ifpay');
Route::post('/order/list','Order\OrderController@orderStatus');
Route::get('/order/ssa','Order\OrderController@ssa');
Route::get('/goods/ares','Shop\GoodsController@ares');

//
Route::any('/wechat', 'WeChatController@serve');
Route::post('/pay', 'Pay\PayController@pay');
Route::any('/wxpay/pay_action', 'Pay\PayActionController@action');
//后台
Route::post('/admin/orderdetails','Order\AdminOrderController@webOrderdetails');
Route::post('/admin/statusorderlist','Order\AdminOrderController@webStatusOrderList');
Route::post('/admin/deliverystatus','Order\AdminOrderController@webDeliveryStatus');//物流发货
Route::get('/admin/category/list','Shop\GoodsCategoryController@categoryList');
Route::get('/admin/goods/list','Shop\GoodsController@adminGoodsList');
Route::post('/admin/goods/details','Shop\GoodsController@adminGoodsDetails');
Route::post('/admin/goods/ifdisable','Shop\GoodsController@adminIfDisable');
Route::post('/admin/goods/update','Shop\GoodsController@adminUpdateGoods');
Route::post('/admin/goods/delete','Shop\GoodsController@deleteGoods');

//官网
Route::post('/gw/insertdivision','Gw\GwDivisionController@insertDivision');
Route::post('/gw/updatedivision','Gw\GwDivisionController@updateDivision');
Route::get('/gw/divisionlist','Gw\GwDivisionController@divisionList');
Route::post('/gw/deletedivision','Gw\GwDivisionController@deleteDivision');
Route::post('/gw/insertarticle','Gw\GwArticleController@insertArticle');
Route::post('/gw/deletearticle','Gw\GwArticleController@deleteArticle');
Route::post('/gw/updatearticle','Gw\GwArticleController@updateArticle');
Route::get('/gw/articlelist','Gw\GwArticleController@articleList');
Route::post('/gw/catedoctorlist','Gw\GwDoctorController@cateDoctorList');
Route::get('/gw/doctorlist','Gw\GwDoctorController@doctorList');
Route::post('/gw/doctordetails','Gw\GwDoctorController@doctorDetails');
Route::post('/gw/insertdoctor','Gw\GwDoctorController@insertDoctor');
Route::post('/gw/deletedoctor','Gw\GwDoctorController@deleteDoctor');
Route::post('/gw/updatedoctor','Gw\GwDoctorController@updateDoctor');


Route::get('/sda', 'Pay\PayController@sda');
