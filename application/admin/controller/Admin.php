<?php
namespace app\admin\controller;

use app\admin\model\Admin as Model;
use app\admin\model\Role;
use think\Hook;
/*
* 管理员
*/
class Admin extends Base{
	/*
	* 管理员列表
	*/
	public function index(){
		$Model=new Model();
		$list=$Model->order('id desc')->paginate(10);
		$this->assign('list',$list);		
		return $this->fetch();
	}

	/*
	* 添加管理员
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
			//获取角色列表
			$Role=new Role();
			$this->assign('role',$Role->order('sort desc,id desc')->select());
			return $this->fetch();
		}
		//post
		//表单验证
		$info=input('post.info/a');
		$result=$this->validate($info,'Admin');
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
			Hook::listen('admin_update',$info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}		
	}

	/*
	* 删除管理员
	*/
	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();			
			$res=Model::destroy(input('get.id'));
			if($res){
				Hook::listen('admin_delete',$info->toArray());			
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}
	}

	/*
	* 修改个人信息
	*/
	public function info(){
		$Model=new Model();
		if(request()->isGet()){
			$info=$Model->where('id',session('admin_id'))->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
		//字段验证
		$info=input('post.info/a');
		$result=$this->validate($info,'Admin');
		if(true !== $result){
			return $this->error($result);
		}
		$rs=$Model->save($info,['id'=>session('admin_id')]);
		if($rs){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	/*
	* 修改密码
	*/
	public function password(){
		$Model=new Model();
		if(request()->isGet()){
			return $this->fetch();
		}
		$validate = new \think\Validate([
		    'old_password' => 'require',
		    'new_password' => 'require',
		    'cfm_password' => 'require|confirm:new_password'
		],[
    		'old_password' => '旧密码必须填写',
    		'new_password' => '新密码必须填写',
    		'cfm_password.require' => '确认密码必须填写',
    		'cfm_password.confirm' => '两次新密码不匹配'
		]);
		$info=input('post.info/a');
		if(!$validate->check($info)){
			return $this->error($validate->getError());
		}
		$rs=$Model->setPassword($info['new_password']);
		if($rs){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}		
	}	
}