<?php
namespace app\admin\controller;

use app\admin\model\Email as Model;

/**
* 邮件
*/
class Email extends Base{

	/*
	* 发送测试邮件
	*/
	public function test(){
		$Model=new Model();
		$result=$Model->validate();
		if(true!=$result){
			return $this->error($result);
		}
		if(!input('?post.email')){
			return $this->error(lang('parameter_empty'));
		}
		$pattern="/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
		if(!preg_match($pattern,input('post.email'))){
			return $this->error(lang('type_error'));
		}
		$rs=$Model->post(input('post.email'),'测试邮件','测试邮件');
		if($rs!=""){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}
}