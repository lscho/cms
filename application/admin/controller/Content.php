<?php
namespace app\admin\controller;

use app\admin\model\Content as Model;
use app\admin\model\Models as Models;
use app\admin\model\ModelsData as ModelsData;
use app\admin\model\Workflow;
use think\Hook;

/**
* 内容模块
*/
class Content extends Base{

    /*
     * 内容模块独立导航
     */
    public function sidebar(){
        $catlist = db('cate')->field('catid,catname,parentid,sort')->order('sort desc,catid asc')->select();
        //权限过滤
        foreach ($catlist as $v) {
        	if(in_array($v['catid'], session('catlist'))){
        		$list[]=$v;
        	}
        }
        $data    = \util\Tree::makeTree($list, ['primary_key' => 'catid', 'children_key' => 'products','primary_index'=>false]);
        $this->assign('catlist',json_encode($data));
        return $this->fetch();
    }

	/*
	* 内容首页
	*/
	public function index($catid=""){
		$catlist=cache('catlist');
		//获取内容模型
        $Models        = new Models();
        $map=[
            'type'=>'content',
            'status'=>1
        ];
        $list          = $Models->where($map)->order('sort desc,id desc')->select();
        $this->assign('models', $list);
		//内容列表
		$Model=new Model();
		$map=[
			'is_delete'=>0,
			'status'=>99
		];
		//栏目ID
		$catid=$catid?$catid:input('get.catid');
		if($catid){
			$this->assign('catid',$catid);
			$map['catid']=$catid;
			//工作流
			if($workflow_id=$catlist[$catid]['workflow']){
				$Workflow=new Workflow();
				$this->assign('workflow',$Workflow->getAction($workflow_id,session('admin_id')));
			}
			//栏目信息
			$cate_info=$catlist[$catid];
			$this->assign('cate',$cate_info);
			//栏目列表
			$this->assign('catlist',$catlist);
			//模型
			$ModelsData=new ModelsData();
			$this->assign('ModelsData',$ModelsData->where('models_id',$cate_info->models_id)->order('sort desc,id asc')->select());
		}else{
			foreach ($catlist as &$val) {
				$val['count']=db('content')->where('catid',['in',$val['catid'].','.$val['sonid']])->count();
			}
			//栏目列表
			$this->assign('catlist',$catlist);
			return $this->fetch('cate');
		}
		//标题
        if(input('?get.title')){
        	$map['title']=['like','%'.input('get.title').'%'];
        }
        //首页
        if(input('?get.is_index')){
        	$map['is_index']=1;
        }
        //栏目
        if(input('?get.is_cate')){
        	$map['is_cate']=1;
        }   
        //状态
        if(input('get.status')){
        	$map['status']=input('get.status');
        }     
        $this->assign('status',$map['status']);
        //排序
        $order=[
        	'0'=>'id desc',
        	'1'=>'id asc',
        	'2'=>'addtime desc',
        	'3'=>'addtime asc',
        	'4'=>'view desc',
        	'5'=>'view asc',

        ];
		$list=$Model->where($map)->order('sort desc,'.$order[input('?get.order')?input('get.order'):0])->paginate(15);
		foreach ($list as &$val) {
			
		}
		$this->assign('list',$list);
		return $this->fetch();
	}

	/*
	* 分词
	*/
	public function analysis(){
		$pa = new \analysis\Analysis();
		$pa->SetSource(input('get.value'));
		//设置分词属性
		$pa->resultType = 2;
		$pa->differMax  = true;
		$pa->StartAnalysis();
		$res=$pa->GetFinallyIndex();
		arsort($res);
		if($res&&is_array($res)){
			return $this->success(lang('action_success'),"",implode(',',array_keys($res)));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	/*
	* 发布内容
	*/	
	public function add($models_id){
		$ModelsData=new ModelsData();
		$Model=new Model();
		if(request()->isGet()){
			//获取内容模型
			$map['models_type']='content';
			$map['models_id']=$models_id;
			$map['status']=1;
			$data=$ModelsData->where($map)->order('sort DESC,id asc')->select();	
			$this->assign('modelsData',$data);
			//获取表单详情
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);
			//模板列表
			$this->assign('tpl',$this->getTpl('show'));
			$this->assign('wapTpl',$this->getTpl('wap'));
			return $this->fetch();
		}
		//表单验证
		$info=input('post.info/a');
		$info['models_id']=$models_id;
		$info['addtime']=$info['addtime']?strtotime($info['addtime']):time();
		$info['username']=$info['username']?$info['username']:session('admin_username');
		$info['tpl']=json_encode($info['tpl']);
		$result=$this->validate($info,'Content');
		if(true !== $result){
			return $this->error($result);
		}			
		$data=input('post.form/a');
		$result=$ModelsData->validate($data,$models_id,'content');
		if(true !== $result['status']){
			return $this->error($result['data']);
		}
		$res=$Model->add($info,$result['data']);
		if($res){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//删除内容
	public function delete(){
		if(input('?get.id')){
			$Model=new Model();
			$content=$Model->where('id',input('get.id'))->find();
			$res=$Model->del(input('get.id'));
			if($res){
				Hook::listen('content_delete',$content->toArray());		
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}

	//放入回收站
	public function recycle(){
		if(input('?get.id')){
			$Model=new Model();
			$content=$Model->where('id',input('get.id'))->find();
			$res=$Model->where('id',input('get.id'))->setField('is_delete',1);
			if($res){
				Hook::listen('content_recycle',$content->toArray());		
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}
	}

	//还原
	public function reduction(){
		if(input('?get.id')){
			$Model=new Model();
			$content=$Model->where('id',input('get.id'))->find();
			$res=$Model->where('id',input('get.id'))->setField('is_delete',0);
			if($res){
				Hook::listen('content_recycle',$content->toArray());		
				$this->success(lang('action_success'));
			}else{
				$this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}

	//内容排序
	public function sort(){
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
		$Model=new Model();
		$rs=$Model->saveAll($list);
		if($rs){
			return $this->success(lang('action_success'));		
		}else{
			return $this->error(lang('action_error'));
		}
	}	

	//批量操作
	public function all($action=""){
		$ids=input('post.ids/a');
		if(!$ids){
			return $this->error(lang('parameter_empty'));
		}
		if(!$action){
			return $this->error(lang('action_error'));
		}
		$Model=new Model();
		$res=$Model->actionAll($action,$ids);
		if($res){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//回收站
	public function recycles(){
		//获取内容模型
        $Models        = new Models();
        $map=[
            'type'=>'content',
            'status'=>1
        ];
        $list          = $Models->where($map)->order('sort desc,id desc')->select();
        $this->assign('models', $list);
		//内容列表
		$Model=new Model();    
		$list=$Model->where('is_delete',1)->order('sort desc,addtime desc')->paginate(15);
		$this->assign('list',$list);
		return $this->fetch();		
	}

	//显示
	public function sets(){
		if(input('?post.id')){
			$Model=new Model();
			//显示
			$rs=$Model->where('id',input('post.id'))->setField('is_show',1);
		}else{
			return $this->error(lang('parameter_empty'));
		}
		if($rs){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}
	}

	//取消显示
	public function unSets(){
		if(input('?post.id')){
			$Model=new Model();
			//取消显示
			$rs=$Model->where('id',input('post.id'))->setField('is_show',0);
		}else{
			return $this->error(lang('parameter_empty'));
		}

		if($rs){
			return $this->success(lang('action_success'));
		}else{
			return $this->error(lang('action_error'));
		}		
	}	
}