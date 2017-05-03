<?php
namespace app\admin\validate;

use think\Validate;

/*
* 微信菜单
*/
class WechatMenu extends Validate{
	//验证规则
    protected $rule = [
        'parentid'  =>  'require',
        'name'  =>  'require',
        'type'	 =>'require',
        'value'   =>'require'

    ];
    //错误提示
    protected $message  =   [
        'parentid.require' => '上级菜单不能为空',
        'name.require' => '菜单名称不能为空', 
        'type.require' => '菜单类型不能为空',
        'value.require' => '菜单值不能为空'
    ];    
}