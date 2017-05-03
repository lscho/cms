<?php
namespace app\home\controller;
use think\Request;

class Index extends Base{

    public function index(){
    	if($this->view->setting['jump']['index']&&Request::instance()->isMobile()){
    		return $this->redirect('wap/index/index');
    	}
    	return $this->fetch();
    }
}
