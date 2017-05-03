<?php
namespace app\home\validate;

use think\Validate;

/*
* 登录验证器
*/
class Login extends Validate{
	//验证规则
    protected $rule = [
        'username'  =>  'require',
        'password'  =>  'require',
    ];
    //错误提示
    public function __construct(){
        $this->message=[
            'username'=>lang('input_tips_username'),
            'password'=>lang('input_tips_password')
        ];
    }       

}