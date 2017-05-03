<?php
namespace app\admin\model;

use think\Model;

class Setting extends Base{
	
	//类型转换
    protected $type = [
        'web'      =>  'array',
        'email'    =>  'array',
        'site'	   =>'array',
        'util'		=>'array',
        'admin'		=>'array',
        'upload'	=>'array',
        'wap'       =>'array',
        'jump'      =>'array',
        'wechat'    =>'array',
        'water'     =>'array'
    ];
}