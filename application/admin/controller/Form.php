<?php
namespace app\admin\controller;

use think\Hook;
use app\admin\model\Models as Models;
use app\admin\model\ModelsData as ModelsData;
use app\admin\model\Form as Model;

/*
* 表单管理
*/
class Form extends Base{

    /*
     * 内容模块独立导航
     */
    public function sidebar(){
		//表单列表
		$Model=new Model();
		$list=$Model->order('sort desc,id desc')->select();
		$this->assign('list',$list);
        return $this->fetch();
    }	

	/*
	* 表单首页
	*/
	public function index(){
        //模型列表
        $Models        = new Models();
        $map=[
            'type'=>'form',
            'status'=>1
        ];
        $list          = $Models->where($map)->order('sort desc,id desc')->select();
        $this->assign('models', $list);
		//表单列表
		$Model=new Model();
		$list=$Model->order('sort desc,id desc')->paginate(10);
		$this->assign('list',$list);
		return $this->fetch();
	}

	/*
	* 新增表单
	*/
	public function add($models_id){
		$ModelsData=new ModelsData();
		$Model=new Model();
		if(request()->isGet()){
			//获取表单栏目模型
			$map['models_type']='cate';
			$map['models_id']=$models_id;
			$form=$ModelsData->where($map)->order('sort DESC,id asc')->select();
			if(!$form){
				return $this->error(lang('models_empty'));
			}			
			$this->assign('form',$form);
			//获取表单详情
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
		//表单验证
		$info=input('post.info/a');
		$info['models_id']=$models_id;
		if(!$info['title']){
			return $this->error(lang('input_no',['info'=>lang('form_title')]));
		}		
		$data=input('post.form/a');
		$result=$ModelsData->validate($data,$models_id,'cate');
		if(true !== $result['status']){
			return $this->error($result['data']);
		}
		$info['info']=$result['data'];
		//写入记录
		$Model->isUpdate($info['id'])->save($info);
		if($Model->id){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//设置状态
	public function sets(){
		if(input('?post.id')){
			$Model=new Model();
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
	public function unsets(){
		if(input('?post.id')){
			$Model=new Model();
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

	//删除表单
	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$res=$Model->where('id',input('get.id'))->delete();
			if($res){	
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}

	//数据列表
	public function datas($id){
		//获取表单信息
		$Model=new Model();
		$form=$Model->get($id);
		if(!$form){
			return $this->error(lang('form_empty'));
		}
		$this->assign('form',$form);
		//获取表单数据
		$db=db('form_'.$form->models->name);
		$list=$db->where('form_id',$id)->limit(10)->page(input('?get.p')?input('?get.p'):1)->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	//数据详情
	public function data($form_id){
		//表单信息
		$Model=new Model();
		$form=$Model->get($form_id);
		if(!$form){
			return $this->error(lang('form_empty'));
		}
		$this->assign('form',$form);
		//表单
		$db=db('form_'.$form->models->name);
		if(request()->isGet()){
			//数据详情
			$info=$db->where('id',input('get.id'))->find();
			$this->assign('info',$info);
			return $this->fetch();
		}

		//表单验证
		$ModelsData=new ModelsData();
		$data=input('post.form/a');
		$result=$ModelsData->validate($data,$form->models_id);
		if(true !== $result['status']){
			return $this->error($result['data']);
		}
		$data['catid']=$form_id;
		//写入记录
		if(empty($data['id'])){
			$res=$db->insert($result['data']);
		}else{
			$res=$db->update($result['data']);
		}
		if($res){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//删除数据
	public function delete_data($form_id){
		if(!input('?get.id')){
			return $this->error(lang('parameter_empty'));
		}
		//表单信息
		$Model=new Model();
		$form=$Model->get($form_id);
		if(!$form){
			return $this->error(lang('form_empty'));
		}
		//表单
		$db=db('form_'.$form->models->name);				
		$res=$db->where('id',input('get.id'))->delete();
		if($res){	
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}					
	}	
}