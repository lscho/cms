<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Access extends Base{

	/*
	* 通过role_id获取栏目权限
	*/
	public function getCate($role_id){
		$data=db('access_cate')->where('role_id',$role_id)->select();
		foreach ($data as $v) {
			$catlist[]=$v['cate_id'];
		}
		return $catlist;
	}	
	
	/*
	* 通过role_id获取菜单权限
	*/
	public function getMenu($role_id){
		$data=db('access_menu')->where('role_id',$role_id)->select();
		foreach ($data as $v) {
			$menu[]=$v['menu_id'];
		}
		return $menu;
	}

	/*
	* 通过role_id获取节点权限
	*/	
	public function getNode($role_id){
		$data=db('access_node')->where('role_id',$role_id)->select();
		foreach ($data as $v) {
			$menu[]=$v['node_id'];
		}
		return $menu;		
	}

	/*
	* 添加栏目权限
	*/
	public function add($role_id,$ids,$type='cate'){
		//构造数据
		foreach ($ids as $v) {
			$data[]=[
				'role_id'=>$role_id,
				$type.'_id'=>$v
			];
		}
		// 启动事务
		Db::startTrans();
		try{
			//删除原有数据
			Db::name('access_'.$type)->where('role_id',$role_id)->delete();
			//添加新数据
			Db::name('access_'.$type)->insertAll($data);
			//提交事务
			Db::commit();
			return true;
		} catch (\Exception $e) {
    		// 回滚事务
    		Db::rollback();
    		return false;
		}		

	}
}