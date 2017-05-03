<?php
namespace app\home\controller;
use app\admin\model\User;
use think\Hook;

class Login extends Base{

	/*
	* 会员登录
	*/
    public function index(){
    	if(request()->isGet()){
    		return $this->fetch();
    	}else{
    		$info['username']=input('username');
    		$info['password']=input('password');
    		$result = $this->validate($info,'Login');
			if(true !== $result){
			    return $this->error($result);
			}
			//执行登录
			$User=new User();
			$userinfo=$User->login($info);
			if($userinfo){
				//登录成功标签位
				Hook::listen('login_success',$userinfo);
				return $this->success(lang('login_success'));				
			}else{
				return $this->error(lang('login_error'));
			}
    	}
    }

    /*
    * 退出登录
    */
   public function out(){
		Hook::listen('login_out');
		$this->success(lang('login_out_success'));   		
   }
}
