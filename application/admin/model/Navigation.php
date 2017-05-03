<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Navigation extends Base{

	/*
	* 添加导航
	*/
	public function add($info){
		$this->isUpdate(!empty($info['id']))->save($info);
		return $this->id;
	}

	/*
	* 删除导航
	*/
	public function del($id){
		//启动事务
		Db::startTrans();
		try{
			$navigation=cache('navigation');
			$info=$navigation[$id];
			//删除主表数据
			Db::name('navigation')->where('id',$id)->delete();
			//更新关联数据
			if($info['model']&&$info['project_id']){
				$key=$info['model']=='cate'?'catid':'id';
				Db::name($info['model'])->where($key,$info['project_id'])->setField('navigation', 0);
			}
			//提交事务
			Db::commit();			
			return true;
		} catch (\Exception $e) {
    		// 回滚事务
    		Db::rollback();
    		return false;
		}				
	}
	
	/*
	* 将内容设置为导航
	* $model=string;内容、栏目、网页
	* $id=int;主键
	*/
	public function sets($model,$id){
		switch ($model) {
			//栏目
			case 'cate':
				$catlist=cache('catlist');
				//构造数据
				$info=[
					'url'=>url('home/content/cate',['catid'=>$id]),
					'title'=>$catlist[$id]['catname'],
					'model'=>$model,
					'project_id'=>$id
				];
				//检测是否为子栏目
				if($catlist[$id]['parentid']!=0){
					//检测父栏目是否设为导航
					$where=[
						'model'=>'cate',
						'project_id'=>$catlist[$id]['parentid']
					];
					$parent=Db::name('navigation')->where($where)->field('id')->find();
					$parent&&$info['parentid']=$parent['id'];
				}
				//条件
				$map['catid']=$id;
				break;
			//内容
			case 'content':
				$content=Db::name('content')->where('id',$id)->find();
				//构造数据
				$info=[
					'url'=>url('home/content/show',['id'=>$id]),
					'title'=>$content['title'],
					'model'=>$model,
					'project_id'=>$id
				];
				//检测父栏目是否设为导航
				$where=[
					'model'=>'cate',
					'project_id'=>$content['catid']
				];
				$parent=Db::name('navigation')->where($where)->field('id')->find();	
				$parent&&$info['parentid']=$parent['id'];				
				//条件
				$map['id']=$id;
				break;
		}
		// 启动事务
		Db::startTrans();
		try{
			Db::name('navigation')->insert($info);
			Db::name($model)->where($map)->update(['navigation'=>1]);
		    // 提交事务
		    Db::commit();
		    return $info;
		} catch (\Exception $e) {
		    // 回滚事务
		    Db::rollback();
		    return false;
		}			
	}

	/*
	* 将内容取消设置为导航
	* $model=string;内容、栏目、网页
	* $id=int;主键
	*/
	public function unSets($model,$id){
		switch ($model) {
			//栏目
			case 'cate':
				$map['catid']=$id;
				break;
			case 'content':
				$map['id']=$id;
				break;
		}
		// 启动事务
		Db::startTrans();
		try{
			$info=Db::name('navigation')->where(['project_id'=>$id,'model'=>$model])->find();
			Db::name('navigation')->where(['project_id'=>$id,'model'=>$model])->delete();
			Db::name($model)->where($map)->update(['navigation'=>0]);
		    // 提交事务
		    Db::commit();
		    return $info;
		} catch (\Exception $e) {
		    // 回滚事务
		    Db::rollback();
		    return false;
		}		
	}		
}