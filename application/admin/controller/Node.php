<?php
namespace app\admin\controller;

use app\admin\model\Node as Model;
use app\admin\model\AccessCate;
use app\admin\model\AccessMenu;
use think\Hook;
/*
* 权限接点
*/
class Node extends Base{

	public function index(){
        $this->assign('list',cache('node_data'));
		return $this->fetch();
	}

	public function add($type='model'){
		$Model=new Model();
		//get
		if(request()->isGet()){
			$this->assign('type',$type);
			//上级
			if(input('get.parentid')){
				$parent=$Model->where('id',input('get.parentid'))->find();
				$this->assign('parent',$parent);				
			}
			//详情
			if(input('get.id')){
				$info=$Model->where('id',input('get.id'))->find();
				$this->assign('info',$info);
			}
			return $this->fetch();
		}
		//post
		//表单验证
		$info=input('post.info/a');
		//添加节点
		$Model->isUpdate($info['id'])->save($info);
		if($Model->id){
			//更新cache
			Hook::listen('cache_node',$info);
			//记录动作
			Hook::listen('node_update',$info,$Model->id);
			return $this->success(lang('save_success'));
		}else{
			return $this->error(lang('save_error'));
		}
	}

	public function delete(){
		if(input('?get.id')){

			$Model=new Model();
			$info=cache('node')[input('get.id')];
			$res=Model::destroy(input('get.id').','.$info['sonid']);
			if($res){
				//记录动作
				Hook::listen('node_delete',$info);				
				//更新cache
				Hook::listen('cache_node',$info);			
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}
	}
}