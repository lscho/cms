<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\admin\model\Form as Model;
use app\admin\model\ModelsData as ModelsData;

class Formdata extends Base{
    /**
     * 显示资源列表
     *
     */
    public function index($form_id){
		//获取表单信息
		$Model=new Model();
		$form=$Model->get($form_id);
		if(!$form){
			return $this->json([]);
		}
		//获取表单数据
		$db=db('form_'.$form->models->name);
		$pageSize=input('?get.pageSize')?input('get.pageSize'):10;
		$map['catid']=$form_id;
		//条件
		if(input('?get.map')){
			$_map=explode('_', input('get.map'));
			if(count($_map)==3){
				$map[$_map[0]]=[$_map[1],$_map[2]];
			}
		}
		//排序
		if(input('?get.order')){
			$_order=explode('_', input('get.order'));
			if(count($_order)==2){
				$order=$_order[0].' '.$_order[1];
			}
		}		
		$list=$db->where($map)->order($order)->paginate($pageSize);
        return $this->json($list?$list:[]);
    }

    /**
     * 显示资源详情
     *
     */
    public function read($form_id,$id){
		//获取表单信息
		$Model=new Model();
		$form=$Model->get($form_id);
		if(!$form){
			return $this->json([]);
		}
		//获取表单数据
		$db=db('form_'.$form->models->name);
		$info=$db->where('id',$id)->find();
        return $this->json($info?$info:[]);
    }


    /**
     * 创建资源
     *
     */
    public function save($form_id){
    	\think\Lang::load(APP_PATH . 'admin\lang\zh-cn.php');

		$ModelsData=new ModelsData();
		$Model=new Model();
		$data=$_POST;
		$form=$Model->get($form_id);
		if(!$form){
			return $this->json([],'表单不存在');
		}
		//表单验证
		$result=$ModelsData->validate($data,$form->models_id);
		if(true !== $result){
			return $this->json([],$result);
		}
		$data['catid']=$form_id;

		$db=db('form_'.$form->models->name);
		$res=$db->insertGetId($data);
		return $this->json($res?['id'=>$res]:[]);					        
    }    
}