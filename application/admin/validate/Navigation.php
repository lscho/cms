<?php
namespace app\admin\validate;

use think\Validate;

/*
* 友情链接验证器
*/
class Navigation extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',
        'url' =>  'require',
    ];
    //错误提示
    protected $message  =   [
        'title.require' => '导航名称不能为空',
        'url.require'   => '导航地址不能为空'  
    ];    
}