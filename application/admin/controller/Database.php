<?php
namespace app\admin\controller;

use think\Hook;

/**
* 数据库管理
*/
class Database extends Base{

	/*
	* 数据库管理
	*/
	public function index(){
		$row = db()->query('SHOW TABLE STATUS FROM '.config('database.database'));
		$this->assign('list',$row);
		return $this->fetch();
	}

	/*
	* 优化表
	*/
	public function optimize(){
		$name=input('get.name');
		if(empty($name)){
			return $this->error(lang('parameter_empty'));
		}
		$info=db()->query('OPTIMIZE TABLE `'.$name.'`');
		if($info){
			return $this->success(lang('action_success'),'',$info);
		}else{
			return $this->error(lang('action_error'));
		}
	}

	/*
	* 修复表
	*/
	public function repai(){
		$name=input('get.name');
		if(empty($name)){
			return $this->error(lang('parameter_empty'));
		}
		$info=db()->query('REPAIR TABLE `'.$name.'`');
		if($info){
			return $this->success(lang('action_success'),'',$info);
		}else{
			return $this->error(lang('action_error'));
		}
	}

	/*
	* 数据库备份
	*/
	public function backup(){
		$config=config('database');
		$db = new \util\Manage ( $config['hostname'].':'.($config['hostport']?$config['hostport']:'3306'), $config['username'], $config['password'], $config['database'], 'utf8' );
		$db->backup (input('?post.tablename')?input('post.tablename'):"",APP_PATH.'..'.DS.'backup'.DS,10000);
	}

	/*
	* 数据库还原
	*/
	public function restore($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;	
		$file=input('get.name');				
		$config=config('database');
		$db = new \util\Manage ( $config['hostname'], $config['username'], $config['password'], $config['database'], 'utf8' );
		$db->restore ($dir.$file);		
	}
}