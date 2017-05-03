<?php
namespace app\admin\model;

use think\Model;
use think\Db;

/*
* Content模型
*/
class Content extends Base{

    protected $type = [
        'addtime'     =>  'timestamp',
        'updatetime'  =>  'timestamp',
        'tpl'  =>  'array'
    ];	

	/*
	* 栏目关联
	* belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
	*/
	public function cate(){
		return $this->belongsTo('Cate','catid','catid');
	}

	/*
	* 模型关联
	* belongsTo('关联模型名','外键名','关联表主键名',['模型别名定义'],'join类型');
	*/
	public function models(){
		return $this->belongsTo('Models','models_id','id');
	}



	/*
	* 发布内容
	* $model=string;内容模型
	* $info=array();基础数据
	* $data=array();扩展数据
	*/
	public function add($info=array(),$data=array()){
		//获取模型信息
		$models=Db::name('models')->where('id',$info['models_id'])->find();
		if(!$models){
			return false;
		}
		// 启动事务
		Db::startTrans();
		try{
			if(!empty($info['id'])){		//更新
				//更新主表数据
				Db::name('content')->where(['id'=>$info['id']])->update($info);
				//检测附表数据是否存在
				$count=Db::name('content_'.$models['name'])->where(['content_id'=>$info['id']])->count();
				if(!$count){
					//写入附表数据
					$data['content_id']=$info['id'];
					Db::name('content_'.$models['name'])->insert($data);
				}else{
					//更新附表数据
					Db::name('content_'.$models['name'])->where(['content_id'=>$info['id']])->update($data);
				}
			}else{							//新增
				//写入主表数据
				$data['content_id']=Db::name('content')->insertGetId($info);
				//写入附表数据
				Db::name('content_'.$models['name'])->insert($data);
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
	* 删除内容
	*/
	public function del($id){
		//启动事务
		Db::startTrans();
		try{
			$info=Db::name('content')->where('id',$id)->field('id,models_id')->find();
			//获取模型信息
			$models=Db::name('models')->where('id',$info['models_id'])->find();			
			//删除副表数据
			Db::name('content_'.$models['name'])->where('content_id',$info['id'])->delete();
			//删除主表数据
			Db::name('content')->where('id',$id)->delete();
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
	* 批量操作
	*/
	public function actionAll($action,$ids){
		//启动事务
		Db::startTrans();
		try{
			switch ($action) {
				//加入回收站
				case 'recycle':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('is_delete',1);
					break;
				//还原
				case 'reduction':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('is_delete',0);
					break;
				//删除					
				case 'delete':
					foreach ($ids as $id) {
						$info=Db::name('content')->where('id',$id)->field('id,models_id')->find();
						//获取模型信息
						$models=Db::name('models')->where('id',$info['models_id'])->find();			
						//删除副表数据
						Db::name('content_'.$models['name'])->where('content_id',$info['id'])->delete();
						//删除主表数据
						Db::name('content')->where('id',$id)->delete();
					}
					break;
				//首页推荐
				case 'index':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('is_index',1);
					break;
				//取消首页推荐
				case 'unindex':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('is_index',0);
					break;
				//栏目推荐
				case 'cate':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('is_cate',1);
					break;
				//取消栏目推荐
				case 'uncate':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('is_cate',0);
					break;
				//退稿	
				case 'refuse':
					Db::name('content')->where(['id'=>['in',$ids]])->setField('status',98);
					break;	
				//审核通过
				case 'adopt':
					foreach ($ids as $id) {
						//获取内容信息
						$info=Db::name('content')->where('id',$id)->field('status,id,catid')->find();
						//获取状态信息
						if($workflow_id=cache('catlist')[$info['catid']]['workflow']){
							if($info['status']==98){
								$status=1;
							}else{
								$workflow=cache('workflow')[$workflow_id];
								$status=$info['status']<$workflow['steps']?$info['status']+1:99;
							}
						}
						//echo $status;
						//die();
						$status&&Db::name('content')->where('id',$id)->setField('status',$status);
					}
					break;							
				default:
					return false;
					break;
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
	* 附表数据关联
	*/
    public function getInfoAttr($value,$data){
    	//获取内容模型信息
    	$models=Db::name('models')->where('id',$data['models_id'])->find();
    	if(!$models){
    		return [];
    	}
    	return Db::name('content_'.$models['name'])->where('content_id',$data['id'])->find();
    }

    /*
    * 获取附表数据
    */
   public function getMore($list){
        foreach (collection($list) as $value) {
            $_data=$value->toArray();
            $_info=db('content_'.$value->models->toArray()['name'])->where('content_id',$_data['id'])->find();
            unset($_info['id']);
            $data[]=array_merge($_data,$_info);
        }
        return $data;
   }    

   public function getMoreInfo($info){
   		if(!$info->id){
   			return false;
   		}
   		$_data=$info->toArray();
   		$_info=db('content_'.$info->models->toArray()['name'])->where('content_id',$_data['id'])->find();
   		unset($_info['id']);
   		$data=array_merge($_data,$_info);
   		return $data;
   }    
}