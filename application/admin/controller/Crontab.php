<?php
namespace app\admin\controller;

use app\admin\model\Crontab as Model;
use think\Hook;
/*
* 定时任务
*/
class Crontab extends Base{

	public function index(){
		$Model=new Model();
		$list=$Model->order('id desc')->paginate(20);
		$this->assign('list',$list);		
		return $this->fetch();
	}

	//添加友情链接
	public function add(){
		$Model=new Model();
		if(request()->isGet()){
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
		$info=input("post.info/a");
		$result=$this->validate($info,'Crontab');
		if(true !== $result){
			return $this->error($result);
		}
		//添加或更新
		$Model->isUpdate($info['id'])->save($info);	
		if($Model->id){
			//更新cache
			Hook::listen('cache_crontab');
			//记录动作
			Hook::listen('crontab_update',$info);
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
				Hook::listen('cache_crontab');
				//记录动作
				Hook::listen('crontab_delete',$info);
				$this->success(lang('action_success'));
			}else{
				$this->success(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}		
}