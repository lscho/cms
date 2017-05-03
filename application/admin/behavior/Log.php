<?php
namespace app\admin\behavior;

use app\admin\model\Log as Model;

class Log {

    //记录日志
    public function add($action='未知操作'){
        $Model=new Model();
        $Model->save(['action'=>$action]);
    }    

	//登录成功
	public function LoginSuccess($user){
		//记录用户登录信息
		$user->logintime=time();
		$user->loginip=input('server.REMOTE_ADDR');
		$user->save();
		//记录登录日志
		$this->add(lang('login_success'));
	}

    //登录失败
    public function LoginError($info){
        $error=cache('admin_login_error');
        $ip=input('server.REMOTE_ADDR');
        //写入缓存
        $error[$ip]=$error[$ip]?$error[$ip]+1:1;
        cache('admin_login_error',$error);
    }

	//更新栏目
    public function CateUpdate($info){
    	$this->add(lang(empty($info['catid'])?'log_add_cate':'log_update_cate').'-'.$info['catname']);

    }	

	//删除栏目
    public function CateDelete($info){
    	$this->add(lang('log_delete_cate').'-'.$info['catname']);

    }  

    //更新文章
    public function ContentUpdate($info){
    	$this->add(lang(empty($info['id'])?'log_add_content':'log_update_content').'-'.$info['title']);
    }  

    //删除文章
    public function ContentDelete($info){
    	$this->add(lang('log_delete_content').'-'.$info['title']);
    }

    //更新站点设置
    public function SettingUpdate(){
    	$this->add(lang('log_update_setting'));
    }

    //更新后台菜单
    public function MenuUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_menu':'log_update_menu').'-'.$info['title']);
    }

    //删除后台菜单
    public function MenuDelete($info){
        $this->add(lang('log_delete_menu').'-'.$info['title']);
    }

    //清除一个月前日志
    public function LogDelete(){
        $this->add(lang('log_delete'));
    }

    //添加角色
    public function RoleUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_role':'log_update_role').'-'.$info['role_name']);
    }

    //删除角色
    public function RoleDelete($info){
        $this->add(lang('log_delete_role').'-'.$info['role_name']);
    }

    //分配栏目权限
    public function AccessCateUpdate($role_id){
        $role=db('role')->where('id',$role_id)->find();
        $this->add(lang('log_update_access_cate').'-'.$role['role_name']);
    }

    //分配系统权限
    public function AccessMenuUpdate($role_id){
        $role=db('role')->where('id',$role_id)->find();
        $this->add(lang('log_update_access_menu').'-'.$role['role_name']);
    }  

    //分配接点权限
    public function AccessNodeUpdate($role_id){
        $role=db('role')->where('id',$role_id)->find();
        $this->add(lang('log_update_access_node').'-'.$role['role_name']);
    }       

    //管理员更新
    public function AdminUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_admin':'log_update_admin').'-'.$info['username']);
    }

    //管理员删除
    public function AdminDelete($info){
       $this->add(lang('log_delete_admin').'-'.$info['username']);
    }

    //会员更新
    public function UserUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_user':'log_update_user').'-'.$info['username']);
    }

    //回会员删除
    public function UserDelete($info){
       $this->add(lang('log_delete_user').'-'.$info['username']);
    }    

    //设置导航
    public function NavigationSet($info){
        $this->add(lang('log_set_navigation').'-'.$info['title']);
    }

    //取消导航
    public function NavigationUnset($info){
        $this->add(lang('log_unset_navigation').'-'.$info['title']);
    }

    //删除导航
    public function NavigationDelete($info){
        $this->add(lang('log_delete_navigation').'-'.$info['title']);
    } 

    //编辑导航
    public function NavigationUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_navigation':'log_update_navigation').'-'.$info['title']);
    }

    //编辑链接
    public function LinkUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_link':'log_update_link').'-'.$info['title']);
    }

    //删除链接  
    public function LinkDelete($info){
        $this->add(lang('log_delete_link').'-'.$info['title']);
    }

    //编辑广告位
    public function AdsenseUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_adsense':'log_update_adsense').'-'.$info['title']);
    }

    //删除广告位
    public function AdsenseDelete($info){
        $this->add(lang('log_delete_adsense').'-'.$info['title']);
    }    

    //编辑模型
    public function ModelUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_model':'log_update_model').'-'.$info['title']);
    }

    //删除模型
    public function ModelDelete($info){
        $this->add(lang('log_delete_model').'-'.$info['title']);
    }

    //编辑定时任务
    public function CrontabUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_crontab':'log_update_crontab').'-'.$info['title']);
    }

    //删除定时任务
    public function CrontabDelete($info){
        $this->add(lang('log_delete_crontab').'-'.$info['title']);
    }

    //编辑节点
    public function NodeUpdate($info){
        $this->add(lang(empty($info['id'])?'log_add_node':'log_update_node').'-'.$info['title']);
    }

    //删除节点
    public function NodeDelete($info){
        $this->add(lang('log_delete_node').'-'.$info['title']);
    }             
}