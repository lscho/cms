<?php
namespace app\admin\controller;

use app\admin\model\WechatMenu as Model;
use app\admin\model\WechatReply;
use think\Hook;
/**
* 微信模块
*/
class Wechat extends Base{

	 public function _initialize(){
        //微信配置
        $this->_config=cache('setting')['wechat'];
        //微信SDK
        $this->_wechat=new \wechat\Wechat($this->_config);	 	
	 }
	
	/*
	* 菜单列表
	*/
	public function menu(){
		$Model=new Model();
        $list = $Model->order('sort desc,id asc')->select();
        if(request()->isGet()){
        	$data    = \util\Tree::makeTreeForHtml(collection($list), ['primary_key' => 'id']);
	        $this->assign('list',$data);
	        return $this->fetch();
        }else{
        	//发布到微信服务器
        	foreach ($list as $val) {
        		$_data=$val->toArray();
        		switch ($_data['type']) {
        			case 'view':
        				$_data['url']=$_data['value'];
        				break;
        			default:
        				$_data['key']=$_data['value'];
        				break;
        		}
        		$arr[]=$_data;
        	}
        	//数据转换
        	$data    = \util\Tree::makeTree($arr, ['primary_key' => 'id','children_key'=>'sub_button','primary_index'=>false]);
        	$rs=$this->_wechat->createMenu(['button'=>$data]);
        	if($rs){
        		$this->success(lang('action_success'));
        	}else{
        		$this->error(lang('action_error'));
        	}
        }	
	}

	/*
	* 菜单编辑
	*/
	public function menuAdd(){
		$Model=new Model();
		if(request()->isGet()){
			//菜单详情
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);	
			//一级菜单列表
			$list=$Model->where('parentid',0)->order('sort desc,id asc')->select();
			$this->assign('list',$list);
			return $this->fetch();
		}
		$info=input("post.info/a");
		$result=$this->validate($info,'WechatMenu');
		if(true !== $result){
			return $this->error($result);
		}
		//添加或更新
		$Model->isUpdate($info['id'])->save($info);	
		if($Model->id){
			//记录动作
			Hook::listen('wechat_menu_update',$info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}			
	}

	/*
	* 删除菜单
	*/
	public function menuDelete(){
		if(input('?get.id')){
			$Model=new Model();
			$info=$Model->where('id',input('get.id'))->find();
			$res=Model::destroy(input('get.id'));
			if($res){
				//删除子菜单
				$Model->where('parentid',input('get.id'))->delete();
				//更新cache
				Hook::listen('wechat_menu_delete',$info->toArray());			
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}

	/*
	* 回复列表
	*/
	public function reply($type=1){
		$Model=new WechatReply();
		$this->assign('type',$type);

		$list = $Model->where('type',$type)->order('sort desc,id asc')->select();
		$this->assign('list',$list);
		//模板
		$tpl=[
			'1'=>'reply_text',
			'2'=>'reply_img_url'
		];
		return $this->fetch($tpl[$type]);
	}

	/*
	* 添加回复
	*/
	public function replyAdd($type=1){
		$Model=new WechatReply();
		if(request()->isGet()){
			$info=$Model->where('id',input('get.id'))->find();
			$this->assign('info',$info);	
			$this->assign('type',$type);
			$tpl=[
				'1'=>'reply_text_add',
				'2'=>'reply_img_url_add'
			];		
			return $this->fetch($tpl[$type]);
		}
		$info=input("post.info/a");
		$info['type']=$type;
		$result=$this->validate($info,'WechatReply');
		if(true !== $result){
			return $this->error($result);
		}
		//检测相同关键词数量
		if(!$info['id']){
			$max=$type==2?8:1;
			$count=$Model->where(['key'=>$info['key'],'type'=>$type])->count();
			if($count>=$max){
				return $this->error(lang('wechat_reply_max')[$type]);
			}
		}
		//添加或更新
		$Model->isUpdate($info['id'])->save($info);	
		if($Model->id){
			//记录动作
			Hook::listen('wechat_reply_update',$info);
			$this->success(lang('save_success'));
		}else{
			$this->error(lang('save_error'));
		}			
	}

	/*
	* 回复排序
	*/
	public function replySort(){
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
		$Model=new WechatReply();
		$rs=$Model->saveAll($list);
		if($rs){
			//更新cache
			Hook::listen('cache_link');
			return $this->success(lang('action_success'));		
		}else{
			return $this->error(lang('action_error'));
		}
	}

	/*
	* 删除回复
	*/
	public function replyDelete(){
		if(input('?get.id')){
			$Model=new WechatReply();
			$info=$Model->where('id',input('get.id'))->find();
			$res=WechatReply::destroy(input('get.id'));
			if($res){
				//更新cache
				Hook::listen('wechat_reply_delete',$info->toArray());			
				return $this->success(lang('action_success'));
			}else{
				return $this->error(lang('action_error'));
			}
		}else{
			return $this->error(lang('parameter_empty'));
		}		
	}	

	/*
	* 获取内容列表
	*/
	public function getContent(){
		$title=input('get.value');
		if(!$title){
			return json();
		}
		$map['title']=['like','%'.$title.'%'];
		$map['is_show']=1;
		$map['is_delete']=0;
		//获取文章列表
		$list=db('content')->field('id,title,thumb,description')->where($map)->select();
		if(!$list){
			return json();
		}
		//微信域名获取
		$setting=cache('setting');
		$host=$setting['wechat']['host']?$setting['wechat']['host']:$setting['site']['host'];
		$host=\util\Functions::getDomain($host);
		$http_type = \util\Functions::getHttpType(); 	
		foreach ($list as &$v) {
			$v['value']=$v['title'];
			$v['url']=url('/wechat/content/show',['id'=>$v['id']],"",$host);
			if($v['thumb']){
				$v['thumb']=strrpos($v['thumb'],'http')!==false?$v['thumb']:$http_type.$host.$v['thumb'];
			}
			
		}
		return json($list);
	}
}