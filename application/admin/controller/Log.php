<?php
namespace app\admin\controller;

use app\admin\model\Log as Model;
use think\Hook;
/*
* 系统日志
*/
class Log extends Base{

	public function index(){
		$Model=new Model();
		$list=$Model->order('id desc')->paginate(20);
		$this->assign('list',$list);		
		return $this->fetch();
	}

	//清除一个月前日志
	public function delete(){
		$Model=new Model();
		if($Model->del()){
			Hook::listen('log_delete');
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

}