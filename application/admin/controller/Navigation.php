<?php
namespace app\admin\controller;

use app\admin\model\Navigation as Model;
use think\Hook;
use app\admin\model\Cate;
use util\Tree;

/**
* 后台首页
*/
class Navigation extends Base{
	
	public function index(){
        $list=db('navigation')->order('sort desc,id asc')->select();
        //缓存线性数据    
        $data = Tree::makeTreeForHtml($list,['primary_key'=>'id']);
        $this->assign('navigation',$data);
		return $this->fetch();
	}

	//添加导航
	public function add(){
		$Model=new Model();
		//get
		if(request()->isGet()){
			$list=db('navigation')->order('sort desc,id asc')->select();
			$this->assign('navigation',$list);
			//详情
			$navigation=cache('navigation');
			$this->assign('info',$navigation[input('get.id')]);
			return $this->fetch();
		}

		//post
		$info=input('post.info/a');
		$result=$this->validate($info,'Navigation');
		if(true !== $result){
			return $this->error($result);
		}		
		$rs=$Model->add($info);
		if($rs){
			//更新cache
			Hook::listen('cache_navigation');
			Hook::listen('cache_cate');
			//动作记录
			Hook::listen('navigation_update',$info);
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}		

	}

	//删除导航
	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();
			$res=$Model->del(input('get.id'));
			if($res){
				//更新cache
				Hook::listen('cache_navigation');
				Hook::listen('cache_cate');
				//动作记录				
				Hook::listen('navigation_delete',$info->toArray());		
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}			
	}

	//设置导航
	public function sets(){
		if(input('?post.model')){
			$Model=new Model();
			//设置为导航
			$rs=$Model->sets(input('post.model'),input('post.id'));
		}else{
			return $this->error(lang('parameter_empty'));
		}

		if($rs){
			//更新cache
			Hook::listen('cache_navigation');
			Hook::listen('cache_cate');
			//动作记录			
			Hook::listen('navigation_set',$rs);
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//取消导航
	public function unSets(){
		if(input('?post.model')){
			$Model=new Model();
			//取消为导航
			$rs=$Model->unSets(input('post.model'),input('post.id'));
		}else{
			return $this->error(lang('parameter_empty'));
		}

		if($rs){
			//更新cache
			Hook::listen('cache_navigation');
			Hook::listen('cache_cate');
			//动作记录			
			Hook::listen('navigation_unset',$rs);
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}		
	}

	//导航排序
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
		$Model=new Model();
		$rs=$Model->saveAll($list);
		if($rs){
			//更新cache
			Hook::listen('cache_navigation');
			return $this->success(lang('action_success'));		
		}else{
			return $this->error(lang('action_error'));
		}		
	}
}