<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Models extends Base{

	//获取器
    public function getTypeValueAttr($value,$data){
        return lang('models')[$data['type']];
    }

    //更新模型
    public function add($info){
		// 启动事务
		Db::startTrans();
		try{
	    	if(!empty($info['id'])){
	    		//获取原始数据
	    		$old_info=Db::name('models')->where('id',$info['id'])->field('name')->find();
	    		//更新模型
	    		Db::name('models')->where(['id'=>$info['id']])->update($info);
	    		//更新数据库信息
	    		if($old_info['name']!=$info['name']){
	    			$old_table_name=config('database.prefix').$info['type'].'_'.$old_info['name'];
	    			$table_name=config('database.prefix').$info['type'].'_'.$info['name'];
	    			$sql="rename table $old_table_name to $table_name;";
	    			Db::query($sql);
	    		}	    		
	    	}else{
	    		//新增模型
	    		$models_id=Db::name('models')->insertGetId($info);
	    		//添加数据表
	    		$table_name=config('database.prefix').$info['type'].'_'.$info['name'];
	    		$sql="create table $table_name(id INT NOT NULL AUTO_INCREMENT,{$info['type']}_id INT NOT NULL,PRIMARY KEY ( id ));";
	    		Db::query($sql);
	    		//添加系统默认字段
	    		if($info['type']=='content'){
		    		$list=[
		    			//内容标题
		    			['models_id'=>$models_id,'models_type'=>'content','title'=>lang('content_title'),'name'=>'title','type'=>1,'lang'=>'150','required'=>'1','admin_show'=>'1','is_system'=>1],
						//关键词
		    			['models_id'=>$models_id,'models_type'=>'content','title'=>lang('content_keywords'),'name'=>'keywords','type'=>1,'lang'=>'300','required'=>'0','admin_show'=>'1','is_system'=>1],	
		    			//作者
						['models_id'=>$models_id,'models_type'=>'content','title'=>lang('content_username'),'name'=>'username','type'=>1,'lang'=>'300','required'=>'0','admin_show'=>'1','is_system'=>1,'default'=>'admin'],	    			
		    			//发布时间
		    			['models_id'=>$models_id,'models_type'=>'content','title'=>lang('content_addtime'),'name'=>'addtime','type'=>7,'lang'=>'10','required'=>'0','admin_show'=>'1','is_system'=>1],
		    			//所属栏目
		    			['models_id'=>$models_id,'models_type'=>'content','title'=>lang('content_cate'),'name'=>'catid','type'=>13,'lang'=>'10','required'=>'1','admin_show'=>'1','is_system'=>1],
		    			//缩略图
		    			['models_id'=>$models_id,'models_type'=>'content','title'=>lang('content_thumb'),'name'=>'thumb','type'=>10,'lang'=>'200','required'=>'0','admin_show'=>'0','is_system'=>1]		    			

		    		];
		    		Db::name('models_data')->insertAll($list);
	    		}
	    	}
		    // 提交事务
		    Db::commit();
	    	return ['status'=>true];
		} catch (\Exception $e) {
    		// 回滚事务
    		Db::rollback();
    		return ['status'=>false,'error'=>$e->getMessage()];
		}
    }

    //删除模型
    public function del($id){
		// 启动事务
		Db::startTrans();   
		try{
			Db::name('models')->where('id',$id)->delete();
			Db::name('models_data')->where('models_id',$id)->delete();
		    // 提交事务
		    Db::commit();	
		    return true;		
		}  catch (\Exception $e) {
    		// 回滚事务
    		Db::rollback();
    		return false;			
		}
    }
}