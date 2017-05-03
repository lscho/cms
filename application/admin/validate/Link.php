<?php
namespace app\admin\validate;

use think\Validate;

/*
* 友情链接验证器
*/
class Link extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',	//catname必填，最大25字符
        'url' =>  'require|url',
    ];
    //错误提示
    protected $message  =   [
        'title.require' => '站点名称不能为空',
        'url.require'   => '站点地址不能为空',   
        'url.url'       => '站点地址格式不正确',  
    ];    
}