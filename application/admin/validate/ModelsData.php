<?php
namespace app\admin\validate;

use think\Validate;

/*
* 模型验证器
*/
class ModelsData extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',
        'name'  =>  'require|alpha',
        'type'	 =>'require',
        'lang'  =>'require'

    ];
    //错误提示
    protected $message  =   [
        'title.require' => '字段名称不能为空',
        'name.require' => '英文名称不能为空',    
        'name.alpha' => '英文名称必须是英文字母',    
        'type.require' => '数据类型不能为空',
        'lang.require'=>'长度不能为空'
    ];    
}