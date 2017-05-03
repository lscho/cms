<?php
namespace app\admin\controller;

use think\Hook;
use app\admin\model\User as Model;
use app\admin\model\Group;

/**
* 管理员
*/
class User extends Base{
	
	/*
	* 用户列表
	*/
	public function index(){
		$Model=new Model();
		$list=$Model->order('id desc')->paginate(10);
		$this->assign('list',$list);	
		return $this->fetch();
	}

	/*
	* 添加会员
	*/
	public function add(){
		$Model=new Model();
		//get
		if(request()->isGet()){
			//详情
			if(input('?get.id')){
				$info=$Model->where('id',input('get.id'))->find();
				$this->assign('info',$info);
			}
			//获取会员组
			$Group=new Group();
			$this->assign('group',$Group->order('sort desc,id asc')->select());
			return $this->fetch();
		}
		//post
		//表单验证
		$info=input('post.info/a');
		$result=$this->validate($info,'User');
		if(true !== $result){
			return $this->error($result);
		}
		if(!$info['password']){
			if(!$info['id'])return $this->error(lang('input_tips_password'));
			unset($info['password']);
		}
		//添加信息
		$Model->isUpdate($info['id'])->save($info);
		if($Model->id){
			Hook::listen('user_update',$info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}		
	}

	/*
	* 删除会员
	*/
	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();			
			$res=Model::destroy(input('get.id'));
			if($res){
				Hook::listen('user_delete',$info->toArray());			
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}
	}		
}