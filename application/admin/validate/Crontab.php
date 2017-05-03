<?php
namespace app\admin\validate;

use think\Validate;

/*
* 定时任务验证器
*/
class Crontab extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',
        'type' =>  'require',
        'action' =>  'require',
    ];
    //错误提示
    protected $message  =   [
        'title.require' => '任务名称不能为空',
        'type.require'   => '任务类型不能为空',   
        'action.require'       => '执行动作不能为空',  
    ];    
}