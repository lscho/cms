<?php
use util\Functions;
use think\Route;
use think\Url;
//子目录兼容
if(Functions::getRoot()){
	Url::root(Functions::getRoot().'/index.php');
}
if(!cache('setting')){
	$setting=\app\admin\model\Setting::get(1)->toArray();
	cache('setting',$setting);
}
//配置信息
$setting=cache('setting');
//绑定后台独立域名
if($setting['admin']['host']){
	Route::domain(Functions::getDomain($setting['admin']['host']),'admin');
}
//绑定手机版域名
if($setting['wap']['host']){
	Route::domain(Functions::getDomain($setting['wap']['host']),'wap');
}
//绑定微信版域名
if($setting['wechat']['host']){
	Route::domain(Functions::getDomain($setting['wap']['host']),'wap');
}