<?php
namespace app\admin\validate;

use think\Validate;

/*
* 友情链接验证器
*/
class Menu extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',
        'model'	 =>'require',
        'controller'	 =>'require',
        'action'	 =>'require',

    ];
    //错误提示
    protected $message  =   [
        'title.require' => '菜单名称不能为空',
        'model.require' => '模块名称不能为空',
        'controller.require' => '文件名称不能为空',
        'action.require' => '方法名称不能为空',
    ];    
}