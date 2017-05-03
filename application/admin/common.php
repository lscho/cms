<?php

//关闭错误警告
error_reporting(E_ERROR | E_PARSE );

/*
* 模型表单模板
* $info 表单信息,$value 当前值
*/
function tpl($info,$value){
	$view = new \think\View([],config('view_replace_str'));
	$prefix=isset($info['prefix'])?$info['prefix']:($info['is_system']?'info':'form');
	$data=[
		'id'=>($info['is_system']?'info':'form').'_'.$info['name'],
		'name'=>$prefix.'['.$info['name'].']',
		'value'=>$value?$value:$info['default'],
		'tips'=>$info['tips']
	];
	//自定义ID
	if(!empty($info['prefix_id'])){
		$data['id']=$info['prefix_id'];
	}
	//样式
	$style="";
	if($info['width']){
		$style.="width:".$info['width'].";";
	}
	if($info['height']){
		$style.="height:".$info['height'].";";
	}
	$data['style']=$style;
	//数据处理
	switch ($info['type']) {
		case '6':
			$arr=explode("\n", $info['data']);
			if(is_array($arr)){
				foreach ($arr as $v) {
					$data['data'][]=explode('|',$v);
				}
			}
		break;
		case '14':
			if($value){
				$data['value_data']=explode(',',rtrim($value,','));
			}
		break;
	}
	// 渲染模板输出 并赋值模板变量
	return $view->fetch('html/tpl_'.$info['type'],$data);	
}

/*
* 模型输出信息
* $info 表单信息,$value 当前值
*/
function show($info,$value){
	$ModelsData=new app\admin\model\ModelsData();
	return $ModelsData->show($info,$value);
}