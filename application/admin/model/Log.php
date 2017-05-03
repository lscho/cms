<?php
namespace app\admin\model;

use think\Model;

class Log extends Base{
	
	//自动完成
	protected $auto = ['userid','username','ip','addtime','url'];

    protected function setUseridAttr(){
        return session('admin_id');
    }

    protected function setUsernameAttr(){
        return session('admin_username');
    }    

    protected function setIpAttr(){
        return input('server.REMOTE_ADDR');
    }

    protected function setAddtimeAttr(){
        return time();
    }  

    protected function setUrlAttr(){
        return input('server.HTTP_HOST').input('server.REQUEST_URI');
    }

    //清除一个月前日志
    public function del(){
        return $this->where('addtime','<',time()-86400*30)->delete();
    }  	
}