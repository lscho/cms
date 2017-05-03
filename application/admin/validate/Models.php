<?php
namespace app\admin\validate;

use think\Validate;

/*
* 模型验证器
*/
class Models extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require|unique:models',
        'name'  =>  'require|unique:models|alpha',
        'type'	 =>'require'

    ];
    //错误提示
    protected $message  =   [
        'title.unique'  =>'模型名称已存在',
        'title.require' => '模型名称不能为空',
        'name.unique'  =>'英文名称已存在',
        'name.require' => '英文名称不能为空',    
        'name.alpha' => '英文名称必须是英文字母',    
        'type.require' => '模型类型不能为空',
    ];    
}