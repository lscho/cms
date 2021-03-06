<?php
namespace app\admin\model;

use think\Model;

class Admin extends Base{
    protected $type = [
        'logintime'     =>  'timestamp'
    ];	
	/*
	* 检测是否登录
	* return boolean
	*/
	public static function isLogin(){
		return session('?admin_id');
	}

	/*
	* 角色关联
	* belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
	*/
	public function role(){
		return $this->belongsTo('Role','role_id','id');
	}	

	/*
	* 密码修改器
	*/
	public function setPasswordAttr($value){
		return md5(sha1($value.'sp'));
	}

	/*
	* 登录
	* return boolean
	*/
	public function login($info){
		$info['password']=md5(sha1($info['password'].'sp'));
		$user=$this->where($info)->find();
		if($user->id){
			return $user;
		}
		return false;
	}

	/*
	* 设置密码
	* return boolean
	*/
	public function setPassword($password){
		$user=$this->where('id',session('admin_id'))->find();
		$user->password=$password;
		return $user->save();
	}

	/*
	* 获取当前用户
	*/
	public function getThis(){
		return $this->where('id',session('admin_id'))->find();
	}
}