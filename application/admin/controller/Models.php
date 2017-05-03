<?php
namespace app\admin\controller;

use app\admin\model\Models as Model;
use app\admin\model\ModelsData as ModelData;
use think\Hook;

/**
*  模型管理
*/
class Models extends Base{

	/*
	* 添加表单模型
	*/
	public function add(){
		$Model=new Model();
		if(request()->isGet()){	
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);					
			return $this->fetch();
		}
		//表单验证
		$info=input('post.info/a');
		$result=$this->validate($info,'Models');
		if(true !== $result){
			return $this->error($result);
		}
		//添加或更新
		$info['status']=$info['status']?$info['status']:0;
		$res=$Model->add($info);	
		if($res['status']){
			//记录动作
			Hook::listen('model_update',$info,$old_info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error').':'.$res['error']);
		}
	}

	/*
	* 模型列表
	*/
	public function index(){
		$Model=new Model();
		$list=$Model->order('sort desc,id desc')->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	/*
	* 模型内容
	*/
	public function content($type,$id){
		$Model=new Model();
		$ModelData=new ModelData();
		if(!$id){
			$this->error(lang('parameter_empty'));
		}
		//模型信息
		$info=$Model->get($id);
		$this->assign('info',$info);
		//数据列表
		$map['models_id']=$id;			//模型ID
		$map['models_type']=$type;		//模型类型
		$list=$ModelData->where($map)->order('sort desc,id asc')->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	/*
	* 模型内容字段
	*/
	public function field($type,$id){
		$Model=new Model();
		$ModelData=new ModelData();
		if(request()->isGet()){
			$info=$ModelData->where('id',input('get.sid'))->find();
			$this->assign('info',$info);
			//模型信息
			$models=$Model->get($id);
			$this->assign('models',$models);							
			return $this->fetch();
		}
		//表单验证
		$info=input('post.info/a');
		$result=$this->validate($info,'ModelsData');
		if(true !== $result){
			return $this->error($result);
		}
		//检测标题名称是否存在
		if(!$info['id']){
			$map=['models_type'=>$type,'models_id'=>$info['models_id'],'name'=>$info['name'],'is_system'=>0];
			if($ModelData->where($map)->count()){
				return $this->error(lang('models_title_has'));
			}
		}
		//添加或更新
		$info['required']=$info['required']?$info['required']:0;
		//更新栏目模型或内容模型
		$func=[
			'content'=>'addContent',
			'cate'=>'addCate'
		];
		$res=$ModelData->$func[$type]($info);
		if($res['status']){
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error').':'.$res['error']);
		}
	}

	/*
	* 排序
	*/
	public function sort($type=1){
		if(!input('?post.sort')){
			return $this->error(lang('parameter_empty'));
		}
		$sort=input('post.sort/a');
		foreach ($sort as $key => $value) {
			$list[]=[
				'id'=>$key,
				'sort'=>$value
			];
		}
		if($type==1){
			$Model=new Model();
		}elseif($type==2){
			$Model=new ModelData();
		}
		$rs=$Model->saveAll($list);
		if($rs){
			return $this->success(lang('action_success'));		
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//删除内容
	public function delete($type=1){
		if(input('?get.id')){
			if($type==1){
				$Model=new Model();
			}elseif($type==2){
				$Model=new ModelData();
			}
			$info=$Model->where('id',input('get.id'))->find()->toArray();
			$res=$Model->del(input('get.id'),$info);
			if($res){	
				//记录动作
				$type==1&&Hook::listen('model_delete',$info);
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}

	//设置状态
	public function sets($type=1){
		if(input('?post.id')){
			if($type==1){
				$Model=new Model();
			}elseif($type==2){
				$Model=new ModelData();
			}
			$res=$Model->where('id',input('post.id'))->setField('status',1);
			if($res){
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}

	//设置状态
	public function unsets($type=1){
		if(input('?post.id')){
			if($type==1){
				$Model=new Model();
			}elseif($type==2){
				$Model=new ModelData();
			}
			$res=$Model->where('id',input('post.id'))->setField('status',0);
			if($res){
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}	
}