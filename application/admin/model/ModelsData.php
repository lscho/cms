<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class ModelsData extends Base{

	//获取器
    public function getTypeValueAttr($value,$data){
        return lang('dataType')[$data['type']][0];
    }

    //更新栏目模型
    public function addCate($info){
    	$info['models_type']='cate';
		// 启动事务
		Db::startTrans();
		try{
	    	if(!empty($info['id'])){
	    		//更新模型
	    		Db::name('models_data')->where(['id'=>$info['id']])->update($info);	    		
	    	}else{
	    		//新增字段
	    		Db::name('models_data')->insert($info);
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

    //更新内容模型
    public function addContent($info){
    	$info['models_type']='content';
    	//模型信息
    	$models=Db::name('models')->where('id',$info['models_id'])->field('name,type')->find();
		// 启动事务
		Db::startTrans();
		try{
	    	if(!empty($info['id'])){
	    		//获取原始数据
	    		$old_info=Db::name('models_data')->where('id',$info['id'])->field('name')->find();
	    		//更新模型
	    		Db::name('models_data')->where(['id'=>$info['id']])->update($info);
	    		//更新字段信息
	    		$table_name=config('database.prefix').$models['type'].'_'.$models['name'];
	    		//是否修改表名
	    		if($old_info['name']!=$info['name']){
	    			$sql="alter table `$table_name` change ".$old_info['name']." ";
	    		}else{
	    			$sql="alter table `$table_name` modify ";
	    		}
	    		//数据类型
	    		$sql.=$info['name']." ".lang('dataType')[$info['type']][1]."(".$info['lang'].")";	    		
	    	}else{
	    		//新增字段
	    		Db::name('models_data')->insert($info);
	    		//添加字段
	    		$table_name=config('database.prefix').$models['type'].'_'.$models['name'];
	    		$sql="alter table `$table_name` Add ".$info['name']." ".lang('dataType')[$info['type']][1]."(".$info['lang'].")";
	    	}
	    	//是否非空
		    if($info['required']){
		    	$sql.="  not null";
		    }else{
	    		$sql.="  null";
	    	}
	    	//默认值
		    if($info['default']!=""){
	    		$sql.="  default ".$info['default'];
		    }
		    if($info['is_system']!=1){
		    	Db::query($sql);
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

    //删除字段
    public function del($id,$info){
		// 启动事务
		Db::startTrans();
		try{
			//删除数据
			Db::name('models_data')->where('id',$info['id'])->delete();
			if($info['models_type']=='content'){
		    	//模型信息
		    	$models=Db::name('models')->where('id',$info['models_id'])->field('name,type')->find();
		    	$table_name=config('database.prefix').$models['type'].'_'.$models['name'];
				$sql="alter table `$table_name` drop ".$info['name'];
				Db::query($sql);
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

    //数据验证
    //数据验证
    public function validate($data,$models_id,$models_type='content'){
    	//获取模型字段信息
    	$info=$this->where(['models_id'=>$models_id,'models_type'=>$models_type,'is_system'=>0])->order('sort DESC')->select();
    	if(!$info){
    		return lang('models_empty');
    	}
    	foreach ($info as $v) {
    		$models_data=$v->toArray();
    		$value=$data[$models_data['name']];
    		//数据转换
    		switch ($v['type']) {
    			case '7':	//日期
    				$data[$models_data['name']]=strtotime($value);
    				break;
    			case '8':	//复选框
    				$data[$models_data['name']]=empty($value)?0:1;
    				break;
    			case '15':
    				$data[$models_data['name']]=strtotime($value);
    				break;
    		}
    		//检测是否为空
    		if($v->required==1&&empty($data[$models_data['name']])){
    			return ['status'=>false,'data'=>lang('input_no',['info'=>$models_data['title']])];
    		}
    		//检测长度是否符合
    		if(strlen($data[$models_data['name']])>$v->lang){
    			return ['status'=>false,'data'=>lang('lang_no',['info'=>$models_data['title'],'lang'=>$v->lang])];
    		}
    	}
    	return ['status'=>true,'data'=>$data];
    }

    //数据反向转换
    public function show($info,$value){
		switch ($info['type']) {
			case '7':
				$data=date('Y-m-d H:i:s',$value);
				break;
			case '10':
				# 返回图片地址
				$data="<img style='width:50px;height:30px;' src='".$value."' />";
				break;
			default:
				# 默认原样输出
				$data=$value;
				break;
		}
		return $data;  	
    }
}