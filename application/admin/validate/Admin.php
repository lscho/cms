<?php
namespace app\admin\validate;

use think\Validate;

/*
* 内容模型验证器
*/
class Admin extends Validate{
	//验证规则
    protected $rule = [
        'username'   => 'unique:admin',
        'qq'  =>  'min:5|max:11',
        'email' =>  'email',
    ];
    //错误提示
    protected $message  =   [
        'username'      => '用户名已存在',
        'qq.min'        => 'qq号码最短不能低于5位',
        'qq.max'        => 'qq号码最长不能超过11位',
        'email'         => 'email格式错误',   
    ];    
}