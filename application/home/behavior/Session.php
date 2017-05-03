<?php
namespace app\home\behavior;

class Session {

	//登录成功更新session
    public function LoginSuccess(&$user){
    	session('id',$user->id);
    	session('username',$user->username);
        session('group_id',$user->group_id);      
    }

    //退出登录删除session
    public function LoginOut(){
    	session('id',null);
    	session('username',null);
        session('group_id',null);      
    }
}