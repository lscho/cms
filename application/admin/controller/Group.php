<?php
namespace app\admin\controller;

use app\admin\model\Group as Model;
use think\Hook;
/*
* 会员组
*/
class Group extends Base{

	public function index(){
		$Model=new Model();
		$list=$Model->order('sort desc,id asc')->paginate(20);
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
		if(empty($info['group_name'])){
			return $this->error(lang('input_no',['info'=>lang('group_name')]));
		}
		//添加分类
		$Model->isUpdate($info['id'])->save($info);
		if($Model->id){
			//更新cache
			Hook::listen('group_update',$info);
			return $this->success(lang('save_success'));
		}else{
			return $this->error(lang('save_error'));
		}
		return $this->fetch();
	}

	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();
			$res=Model::destroy(input('get.id'));
			if($res){
				//更新cache
				Hook::listen('group_delete',$info->toArray());			
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