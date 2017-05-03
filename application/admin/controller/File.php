<?php
namespace app\admin\controller;

use util\File as Files;

/**
* 文件管理
*/
class File extends Base{

	/*
	* 上传附件管理
 	 */
 	public function file($src="&public&upload"){
 		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
 		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
 		$this->assign('list',Files::get_list($dir));
		//当前路径
		$this->assign('src',$src);
		return $this->fetch();		
 	}	

	/*
	* 静态资源管理
 	 */
 	public function resource($src="&public&static"){
 		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
 		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
 		$this->assign('list',Files::get_list($dir));
		//当前路径
		$this->assign('src',$src);
		return $this->fetch();		
 	}

	/*
	* 模板文件管理
	*/
	public function tpl($src="&application&home&view"){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
		$this->assign('list',Files::get_list($dir));
		//当前路径
		$this->assign('src',$src);
		return $this->fetch();
	}

	/*
	* 备份文件管理
	*/
	public function backup($src="&backup"){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
		$this->assign('list',Files::get_list($dir));
		//当前路径
		$this->assign('src',$src);
		return $this->fetch();
	}	

	/*
	* 文件上传
	*/
	public function upload($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
	    $file = request()->file('upfile');
	    $info = $file->move($dir,'');
	    if($info){
	    	return json_encode(['status'=>1,'msg'=>'上传成功','src'=>$info->getExtension()]);
	    }else{
	    	return json_encode(['status'=>0,'msg'=>$file->getError()]);
	    }
	}

	/*
	* 文件下载
	*/
	public function down($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;	
		$file=input('get.name');
		$content=Files::read_file($dir.$file);
		Header( "Content-type:  application/octet-stream "); 
		Header( "Accept-Ranges:  bytes "); 
		Header( "Accept-Length: " .filesize($dir.$file));
		header( "Content-Disposition:  attachment;  filename= {$file}");
		echo $content;
	}

	/*
	* 文件重命名
	*/
	public function rename($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
		$file=input('get.name');
		$name=input('get.new');
		if(empty($file)||empty($name)){
			return $this->error(lang('parameter_empty'));
		}
		if(rename($dir.$file, $dir.$name)){
			return $this->success(lang('action_success'));
		}else{
			return $this->success(lang('action_error'));
		}
	}

	/*
	* 新建文件夹
	*/
	public function mkdir($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
		$new=input('get.new');
		if(empty($new)){
			return $this->error(lang('parameter_empty'));
		}
		//检测文件是否存在
		if(file_exists($dir.$new)){
			return $this->error(lang('dir_set'));
		}		
		if(mkdir($dir.$new, 0755)){
			return $this->success(lang('action_success'));
		}else{
			return $this->success(lang('action_error'));
		}		
	}

	/*
	* 文件删除
	*/
	public function delete($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
		$file=input('get.name');
		if(empty($file)){
			return $this->error(lang('parameter_empty'));
		}
		//检测文件是否存在
		if(!file_exists($dir.$file)){
			return $this->error(lang('file_empty'));
		}
		//检测类型
		if(is_file($dir.$file)){
			$result = @unlink ($dir.$file);
		}
		if(is_dir($dir.$file)){
			$result = Files::del_dir($dir.$file);
		}
	    if($result){
	    	return $this->success(lang('action_success'));
	    }else{
	    	return $this->success(lang('action_error'));
	    }		
	}
	/*
	* 编辑文件
	*/
	public function edit($src=""){
		//安全检测
		if(strpos($src,'.')!==false){
			return $this->error(lang('action_danger'));
		}		
		$dir=APP_PATH.'..'.str_replace('&',DS,$src).DS;
		if(request()->isGet()){
			if($file=input('request.name')){
				$this->assign('file',$file);
				$this->assign('content',file_get_contents($dir.$file));
			}
			return $this->fetch();
		}
		if(!input('?post.name')){
			return $this->error(lang('parameter_empty'));
		}
		$files = fopen($dir.input('post.name'), "w");
		if(!$files){
			return $this->error(lang('action_danger'));
		}
		try {
			fwrite($files, input('post.content'));
			fclose($files);
			return $this->success(lang('action_success'));
		} catch (Exception $e) {
			return $this->success(lang('action_error'));
		}

	}
}