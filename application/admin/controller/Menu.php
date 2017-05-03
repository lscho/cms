<?php
namespace app\admin\controller;

use app\admin\model\Menu as Model;
use think\Hook;
use util\Tree;
/*
* 分类
*/
class Menu extends Base{

	public function index(){
        $list=db('menu')->order('sort desc,id asc')->select();
        //缓存线性数据          
        $data = Tree::makeTreeForHtml($list,['primary_key'=>'id']);
        $this->assign('menu',$data);	
		return $this->fetch();
	}

	public function add(){
		$Model=new Model();
		//get
		if(request()->isGet()){
			//一级菜单列表
			$list=db('menu')->where('parentid',0)->order('sort desc,id asc')->select();
			$this->assign('menu',$list);
			//详情
			if(input('?get.id')){
				$info=$Model->where('id',input('get.id'))->find();
				$this->assign('info',$info);
			}
			//获取模板列表
			$this->assign('tpl',$this->getTpl());
			return $this->fetch();
		}
		//post
		//表单验证
		$info=input('post.info/a');
		$result=$this->validate($info,'Menu');
		if(true !== $result){
			return $this->error($result);
		}
		//添加
		$Model->isUpdate($info['id'])->save($info);
		if($Model->id){
			//更新cache
			Hook::listen('cache_menu');
			//记录动作
			Hook::listen('menu_update',$info,$Model->id);
			//更新权限
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}		
	}

	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();
			$res=Model::destroy(input('get.id'));
			if($res){
				//更新cache
				Hook::listen('cache_menu');
				//记录动作
				Hook::listen('menu_delete',$info->toArray());			
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
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
			Hook::listen('cache_menu');
			$this->success(lang('action_success'));		
		}else{
			$this->error(lang('action_error'));
		}
	}
}