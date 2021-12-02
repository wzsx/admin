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


//
Route::any('/wechat', 'WeChatController@serve');

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
Route::post('/gw/insertdivision','Gw\GwWebController@insertDivision');
Route::get('/gw/divisionlist','Gw\GwWebController@divisionList');
