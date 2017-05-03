<?php

namespace app\wap\controller;

use think\Controller;

class Base extends Controller{
    public function _initialize(){
    	//全局缓存
        $this->assign('catlist',cache('catlist'));
        $this->assign('cate_data',cache('cate_data'));
        $this->assign('setting',cache('setting'));
        $this->assign('adsense_data',cache('adsense_data'));
        $this->assign('navigation',cache('navigation'));
        $this->assign('navigation_data',cache('navigation_data'));
        $this->assign('link',cache('link'));
    }
}