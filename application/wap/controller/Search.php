<?php
namespace app\home\controller;
use app\admin\model\Content;
use think\Hook;

class Search extends Base{

	/*
	* 检索首页
	*/
	public function content(){
		$key=input('get.key')?input('get.key'):'请输入关键词';
		$this->assign('keys',$key);
		$Model=new Content();
		$map['title']=['like','%'.$key.'%'];
		$count=$Model->where($map)->count();
		$this->assign('count',$count);
		$list=$Model->where($map)->paginate(10,false,['query'=>['key'=>$key]]);
		$this->assign('list',$list);
		return $this->fetch();
	}
}