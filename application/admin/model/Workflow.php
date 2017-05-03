<?php
namespace app\admin\model;

use think\Model;

	
/*
* 推荐位
*/
class Workflow extends Base{

	//类型转换
    protected $type = [
        'admin'      =>  'array'
    ];	

    //获取操作权限
    public function getAction($workflow_id,$admin_id){
    	$workflow=cache('workflow')[$workflow_id];
    	if(!is_array($workflow['admin'])){
    		return false;
    	}
    	foreach ($workflow['admin'] as $k=>$v) {
    		if(in_array($admin_id, $v)){
    			$data[]=[
    				'status'=>$k,
    				'workflow_name'=>lang('workflow_name')[$k]
    			];
    		}
    	}
    	if($data){
    		$data[]=[
    			'status'=>98,
    			'workflow_name'=>lang('workflow_name')[98]
    		];
    		$data[]=[
    			'status'=>99,
    			'workflow_name'=>lang('workflow_name')[99]
    		];    		
    	}
    	return $data;
    }
}