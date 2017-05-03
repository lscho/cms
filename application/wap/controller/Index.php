<?php

namespace app\wap\controller;

class Index extends Base{
	
	/*
	* 手机版首页
	*/
	public function index(){
		return $this->fetch();
	}
}