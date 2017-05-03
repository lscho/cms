<?php
namespace app\admin\behavior;

class Session {

	//登录成功更新session
    public function LoginSuccess(&$user){
    	session('admin_id',$user->id);
    	session('admin_username',$user->username);
        session('role_id',$user->role_id);      
    }

    //退出登录删除session
    public function LoginOut(){
    	session('admin_id',null);
    	session('admin_username',null);
        session('role_id',null);      
    }
}