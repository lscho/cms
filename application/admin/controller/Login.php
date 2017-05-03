<?php
namespace app\admin\controller;

use think\Controller;
use think\Hook;
use app\admin\model\Admin;

/**
* 后台登陆
*/
class Login extends Controller{

	public function index(){
		//登录初始化
		Hook::listen('login_init');
		//登录判断
		if(Admin::isLogin())$this->redirect(url('admin/index/index'));	
			
		if (request()->isGet()){
			return $this->fetch();
		}else{
			if(!input('?post.username')||!input('?post.password'))$this->error(lang('input_no',['info'=>'用户名或密码']));
			$info=[
				'username'=>input('post.username'),
				'password'=>input('post.password')
			];
			$Admin=new Admin();
			$user=$Admin->login($info);
			if($user){
				Hook::listen('login_success',$user);
				$this->success(lang('login_success'),url('admin/index/index'));
			}else{
				Hook::listen('login_error',$info);
				$this->error(lang('login_error'));
			}
		}
	}


	public function out(){
		Hook::listen('login_out');
		$this->success(lang('login_out_success'),url('admin/login/index'));
	}	
}