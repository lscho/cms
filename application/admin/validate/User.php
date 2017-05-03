<?php
namespace app\admin\validate;

use think\Validate;

/*
* 用户信息验证器
*/
class User extends Validate{
	//验证规则
    protected $rule = [
        'username'   => 'require|unique:user',
        'group_id'  =>'require|gt:0',
        'qq'  =>  'min:5|max:11',
        'email' =>  'email',
    ];
    //错误提示
    protected $message  =   [
        'username.require'=>'用户名不能为空',
        'username.unique' => '用户名已存在',
        'group_id'=>'会员组不能为空',
        'qq.min'        => 'qq号码最短不能低于5位',
        'qq.max'        => 'qq号码最长不能超过11位',
        'email'         => 'email格式错误',   
    ];    
}