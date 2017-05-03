<?php
namespace app\admin\controller;

use app\admin\model\Setting as Model;
use think\Hook;

/**
* 基础设置
*/
class Setting extends Base{
	
	//站点设置
	public function index(){
		$Model=new Model();
		if(request()->isGet()){
			$this->assign('info',cache('setting'));
			//主题
			$this->assign('theme',$this->getTheme());
			return $this->fetch();
		}
		$info=input('post.info/a');
		$model=input('post.model');
		$Setting=$Model->get(1);
		//合并配置
		$config=cache('setting')[$model];
		$info=array_merge($config,$info);

		$rs=$Setting->save([$model=>$info]);
		if($rs){
			//cache更新
			Hook::listen('cache_setting');
			//动作记录
			Hook::listen('setting_update');
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//安全管理
	public function security(){
		$Model=new Model();
		if(request()->isGet()){
			$this->assign('info',cache('setting'));
			//主题
			$this->assign('theme',$this->getTheme());
			return $this->fetch();
		}	
	}

	//手机网站设置
	public function wap(){
		$Model=new Model();
		if(request()->isGet()){
			$this->assign('info',cache('setting'));
			//主题
			$this->assign('theme',$this->getTheme());
			return $this->fetch();
		}	
	}

	//微信相关设置
	public function wechat(){
		$Model=new Model();
		if(request()->isGet()){
			$this->assign('info',cache('setting'));
			return $this->fetch();
		}	
	}
}