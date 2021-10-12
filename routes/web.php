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
Route::post('/category/insert','Shop\GoodsCategoryController@goodsCategoryInsert');
Route::post('/goods/insert','Shop\GoodsController@goodsInsert');
//Wx
Route::post('/wx/code','Wx\WxController@codeSession');

Route::post('/wx/session','Wx\WxController@Session');
Route::get('/wx/sa','Wx\WxController@aaad');
