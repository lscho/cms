<?php
namespace app\admin\model;

use think\Model;

class Cate extends Base{
	//类型转换
    protected $type = [
    	'cate_tpl'      =>  'array',
    	'list_tpl'      =>  'array',
    	'show_tpl'      =>  'array',
        'info'      =>  'array',
    ];		
	/*
	* 栏目关联
	* belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
	*/
	public function models(){
		return $this->belongsTo('Models','models_id','id');
	}	
}