<?php
namespace app\admin\validate;

use think\Validate;

/*
* 广告位验证器
*/
class Adsense extends Validate{
	//验证规则
    protected $rule = [
        'title'  =>  'require',
        'width' =>  'number',
        'height' =>  'number',
    ];
    //错误提示
    protected $message  =   [
        'title.require' => '广告位不能为空',
        'width.number' => '宽度只能为数字',
        'height.number' => '高度只能为数字',
    ];    
}