<?php

namespace app\wechat\controller;
use wechat\Wechat;
use app\admin\model\WechatReply;
use app\admin\model\WechatMenu;
use think\Log;

class Connect extends Base{
	
	/*
	* 与微信服务器通讯
	*/
	public function index(){
		//微信认证
		$this->_wechat->valid();
		//事件类型
		$type = $this->_wechat->getRev()->getRevType();
		switch ($type) {
			//文字消息
			case Wechat::MSGTYPE_TEXT:
				$content=$this->_wechat->getRevContent();
				$data=$this->text($content);
				break;
			//事件消息
			case Wechat::MSGTYPE_EVENT:
				$events=$this->_wechat->getRevEvent();
				$data=$this->event($events);
				break;
			//图片消息
			case Wechat::MSGTYPE_IMAGE:
				break;
			default:
				$data=$this->_wechat->text("help info")->reply();
				break;
		}
		return $data;
	}

	/*
	* 文字消息处理
	*/
	public function text($content){
		return $this->reply($content);
	}

	/*
	* 事件消息处理
	*/
	public function event($events){
		Log::write($events);
		switch ($events['event']) {
			case 'CLICK':
				$data=$this->reply($events['key']);
				break;
			default:
				# code...
				break;
		}
		//点击统计
		$map=[
			'type'=>strtolower($events['event']),
			'value'=>$events['key']
		];
		$Model=new WechatMenu();
		$Model->where($map)->setInc('click');
		return $data;
	}

	/*
	* 消息回复
	*/
	public function reply($key){
		$Model=new WechatReply();
		$list=$Model->where('key',$key)->order('sort desc,id asc')->limit(8)->select();
		if(!$list){
			return "";
		}
		//文字回复
		if($list[0]['type']==1){
			return $this->_wechat->text($list[0]['content'])->reply();
		}
		//图文回复
		if($list[0]['type']==2){
			foreach ($list as $val) {
				if($val['type']==2){
					$data[]=$val['content'];
				}
			}
			if($data){
				return $this->_wechat->news($data)->reply();
			}
		}
	}
}