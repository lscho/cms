<?php
namespace app\admin\model;

use think\Model;

class Form extends Base{
	//类型转换
    protected $type = [
        'info'      =>  'array',
        'addtime'	=>	'timestamp'
    ];	

	/*
	* 模型关联
	* belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
	*/
	public function models(){
		return $this->belongsTo('Models','models_id','id');
	}

	/*
	* 模型关联
	* belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
	*/
	public function modelsData(){
		return $this->hasMany('ModelsData','models_id','models_id');
	}	
}