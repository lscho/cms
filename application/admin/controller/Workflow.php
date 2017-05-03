<?php
namespace app\admin\controller;

use app\admin\model\Workflow as Model;
use app\admin\model\Admin;
use think\Hook;

/**
* 工作流
*/
class Workflow extends Base{

	public function index(){
		$Model=new Model();
		$list=$Model->order('id asc')->select();
		$this->assign('list',$list);	
		return $this->fetch();	
	}

	//添加工作流
	public function add(){
		$Model=new Model();
		if(request()->isGet()){
			//管理员列表
			$Admin=new Admin();
			$admin=$Admin->field('id,username')->select();
			$this->assign('admin',$admin);
			//工作流详情
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
		$info=input("post.info/a");
		if(!$info['title']){
			return $this->error(lang('input_no'),lang('workflow_title'));
		}
		//添加或更新
		$Model->isUpdate($info['id'])->save($info);	
		if($Model->id){
			//更新cache
			Hook::listen('cache_work');
			//记录动作
			Hook::listen('work_update',$info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}
	}
	//删除内容
	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find()->toArray();
			$res=$Model->where('id',input('get.id'))->delete();
			if($res){	
				//更新cache
				Hook::listen('cache_work');
				//记录动作
				Hook::listen('work_update',$info);
				$this->success(lang('action_success'));
			}else{
				$this->success(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}	
}