<?php
namespace app\admin\model;

use think\Model;
use util\Smtp;

class Email extends Base{

	/*
	* 验证邮箱配置
 	*/
 	public function validate(){
 		$config=cache('setting')['email'];
 		if(!$config||!$config['server']||!$config['port']||!$config['user']||!$config['password']){
 			return lang('email_config_error');
 		}
 		return true;
 	}

 	/*
 	* 发送邮件
 	*/
 	public function post($email,$title,$connenct,$type="HTML"){
 		$config=cache('setting')['email'];
 		$smtp = new Smtp($config['server'],$config['port'],true,$config['user'],$config['password']);
 		$state = $smtp->sendmail($email, $config['username'],$title, $connenct, $type);
 		return $state;
 	}
}