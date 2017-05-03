<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use util\Tree;

/**
* 后台控制器基类
*/
class Base extends Controller{
	
	public function _initialize(){
		//站点设置
		$this->assign('setting',cache('setting'));
		//前台url
		if(cache('setting')['site']['host']){
			$this->assign('url',\util\Functions::getDomain(cache('setting')['site']['host']));
		}
		//菜单
		if(!session('menu')){
			$menu=db('menu')->order('sort desc,id asc')->select();
	        $data = Tree::makeTreeForHtml($menu,['primary_key'=>'id']);
	        session('menu',$data);
		}
		$this->assign('menu',session('menu'));
	}

	//获取模板列表
	public function getTpl($type="",$model='home'){
		if($model=='home'){
			$setting=cache('setting');
			$dir=APP_PATH.$model.DS.'view'.DS.$setting['web']['theme'].DS.'content';
		}else{
			$dir=APP_PATH.$model.DS.'view'.DS.'content';
		}
		$data=[];
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					is_file($dir .DS. $file)&&$data[]=$file;
				}
				closedir($dh);
			}
		}
		foreach ($data as $v) {
			//栏目
			if(preg_match('/^cate(\_[a-zA-Z]*)*.html$/',$v)){
				$cate[str_replace(".html","",$v)]=$v;
			}
			//列表
			if(preg_match('/^list(\_[a-zA-Z]*)*.html$/',$v)){
				$list[str_replace(".html","",$v)]=$v;
			}
			//详情
			if(preg_match('/^show(\_[a-zA-Z]*)*.html$/',$v)){
				$show[str_replace(".html","",$v)]=$v;
			}			
		}
		$data=[
			'cate'=>$cate,
			'list'=>$list,
			'show'=>$show
		];
		if($type){
			return $data[$type];
		}else{
			return $data;
		}
	}
	//获取主题列表
	public function getTheme(){
		$dir=APP_PATH.'home'.DS.'view';
		$data=[];
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(is_dir($dir .DS. $file)&&$file!='.'&$file!='..'){
						$data[]=$file;
					}
				}
				closedir($dh);
			}
		}
		return $data;
	}
}