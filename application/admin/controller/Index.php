<?php
namespace app\admin\controller;

use app\admin\model\Log;
use app\admin\model\Admin;
use think\Hook;
use think\Db;

/**
* 后台首页
*/
class Index extends Base{

	public function index(){
		return $this->fetch();
	}
	
	public function home(){
		//服务器信息
		$mysql_version = db()->query("select version() as version");
		$info=[
			'php_version'=>PHP_VERSION,
			'php_os'=>PHP_OS,
			'upload_max_filesize'=>get_cfg_var ("upload_max_filesize")?get_cfg_var ("upload_max_filesize"):"不允许上传附件",
			'max_ex_time'=> ini_get("max_execution_time")?ini_get("max_execution_time")."秒":"获取失败",
			'mysql_server'=>$mysql_version[0]['version']
		];
		$this->assign('info',$info);
		//管理员信息
		$Admin=new Admin();
		$this->assign('admin',$Admin->getThis());
		//内容数量
		$this->assign('num',Db::name('content')->count());
		//栏目
		$this->assign('cate',Db::name('cate')->count());		
		//日志
		$Log=new Log();
		$this->assign('log',$Log->order('id desc')->limit(10)->select());
		//磁盘可用信息
		$this->assign('disk_free_space',round(disk_free_space(getcwd())/1024/1024/1024,2));
		$this->assign('disk_total_space',round(disk_total_space(getcwd())/1024/1024/1024,2));
		$this->assign('disk',(1-$this->view->disk_free_space/$this->view->disk_total_space)*100);
		//缓存目录读写状态
		$this->assign('cache',is_writable(RUNTIME_PATH));
		//上传目录读写状态
		$this->assign('upload',is_writable(APP_PATH.'../public/upload'));
		//数据库大小
		$row = db()->query('SHOW TABLE STATUS FROM '.config('database.database'));
        $size = 0;
        foreach($row as $value) {
            $size += $value["Data_length"] + $value["Index_length"];
        }
        $this->assign('db_size',round(($size/1048576),2));
		return $this->fetch();
	}

	public function sidebar($parentid=1){
		//获取上级ID
		$this->assign('parentid',$parentid);
		//检测当前栏目是否有独立导航模板
		return $this->fetch('public/sidebar');
	}

	public function error(){
		return $this->fetch();
	}
}