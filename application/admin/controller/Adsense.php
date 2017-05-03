<?php
namespace app\admin\controller;

use app\admin\model\Adsense as Model;
use think\Hook;

/**
* 广告位管理
*/
class Adsense extends Base{

	public function index(){
		
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
		$result=$this->validate($info,'Adsense');
		if(true !== $result){
			return $this->error($result);
		}
		//添加或更新
		$Model->isUpdate($info['id'])->save($info);	
		if($Model->id){
			//更新cache
			Hook::listen('cache_adsense');
			//记录动作
			Hook::listen('adsense_update',$info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}				
	}

	//排序
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
			Hook::listen('cache_adsense');
			return $this->success(lang('action_success'));		
		}else{
			return $this->error(lang('action_error'));
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
				Hook::listen('cache_adsense');
				//记录动作
				Hook::listen('adsense_delete',$info);
				$this->success(lang('action_success'));
			}else{
				$this->success(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}	
}