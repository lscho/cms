<?php
namespace app\admin\validate;

use think\Validate;

/*
* 微信回复
*/
class WechatReply extends Validate{
	//验证规则
    protected $rule = [
        'name'  =>  'require',
        'type'	 =>'require',
        'key'   =>'require',
        'content'   =>'require',

    ];
    //错误提示
    protected $message  =   [
        'name.require' => '回复名称不能为空', 
        'type.require' => '回复类型不能为空',
        'key.require' => '触发关键字不能为空',
        'content.require' => '回复内容不能为空',
    ];    
}