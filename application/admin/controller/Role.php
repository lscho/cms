<?php
namespace app\admin\controller;

use app\admin\model\Role as Model;
use app\admin\model\AccessCate;
use app\admin\model\Access;
use think\Hook;
/*
* 角色
*/
class Role extends Base{

	public function index(){
		$Model=new Model();
		$list=$Model->order('sort desc,id asc')->select();
		$this->assign('list',$list);		
		return $this->fetch();
	}

	public function add(){
		$Model=new Model();
		//get
		if(request()->isGet()){
			//详情
			if(input('?get.id')){
				$info=$Model->where('id',input('get.id'))->find();
				$this->assign('info',$info);
			}
			return $this->fetch();
		}
		//post
		//表单验证
		$info=input('post.info/a');
		if(empty($info['role_name'])){
			return $this->error(lang('input_no',['info'=>lang('role_name')]));
		}
		//添加分类
		$Model->isUpdate($info['id'])->save($info);
		if($Model->id){
			//更新cache
			Hook::listen('role_update',$info);
			return $this->success(lang('save_success'));
		}else{
			return $this->error(lang('save_error'));
		}
	}

	//分配栏目权限
	public function cate(){
		$Access= new Access();
		if(request()->isGet()){
			//获取栏目
			$data=$Access->getCate(input('get.id'));
			$this->assign('catlist',$data);
			return $this->fetch();
		}

		if(request()->isPost()){
			$catid=input('post.catid/a');
			if(!input('?post.catid')){
				return $this->error(lang('access_cate_no'));
			}
			$role_id=input('post.role_id');
			$res=$Access->add($role_id,$catid,'cate');
			if($res){
				Hook::listen('access_cate_update',$role_id);
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}
	}

	//分配菜单权限
	public function menu(){
		$Access= new Access();
		if(request()->isGet()){
			//获取栏目
			$data=$Access->getMenu(input('get.id'));
			$this->assign('menu',$data);
			return $this->fetch();
		}

		if(request()->isPost()){
			$id=input('post.id/a');
			if(!input('?post.id')){
				return $this->error(lang('access_cate_no'));
			}
			$role_id=input('post.role_id');
			$res=$Access->add($role_id,$id,'menu');
			if($res){
				Hook::listen('access_menu_update',$role_id);
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}		
	}

	//分配操作权限
	public function node(){
		$Access= new Access();
		if(request()->isGet()){
			$data=$Access->getNode(input('get.id'));
			$this->assign('node',$data);
			return $this->fetch();
		}
		if(request()->isPost()){
			$id=input('post.id/a');
			if(!input('?post.id')){
				return $this->error(lang('access_cate_no'));
			}
			$role_id=input('post.role_id');
			$res=$Access->add($role_id,$id,'node');
			if($res){
				Hook::listen('access_node_update',$role_id);
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}		
	}

	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();
			$res=Model::destroy(input('get.id'));
			if($res){
				//更新cache
				Hook::listen('role_delete',$info->toArray());			
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}
	}

	public function sort(){
		if(!input('?post.sort')){
			return $this->error(lang('parameter_empty'));
		}
		$sort=input('post.sort/a');
		foreach ($sort as $key => $value) {
			$list[]=[
				'id'=>$key,
				'sort'=>$value
			];
		}
		$Model=new model();
		$rs=$Model->saveAll($list);
		if($rs){
			//更新cache
			Hook::listen('menu_update');
			return $this->success(lang('action_success'));		
		}else{
			return $this->error(lang('action_error'));
		}
	}
}