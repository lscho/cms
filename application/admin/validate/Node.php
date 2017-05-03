<?php
namespace app\admin\validate;

use think\Validate;

/*
* 权限接点验证器
*/
class Node extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',
        'model'	 =>'require',
        'controller'	 =>'require'

    ];
    //错误提示
    protected $message  =   [
        'title.require' => '接点名称不能为空',
        'model.require' => '模块名称不能为空',
        'controller.require' => '文件名称不能为空'
    ];    
}