<?php
namespace app\admin\behavior;

use app\admin\model\Access;
use app\admin\model\Admin;
use think\Request;
use util\Functions;
load_trait('controller/Jump');

class Role {

    use \traits\controller\Jump;
    //更新权限
    public function update(){
        //获取当前用户栏目权限
        $Access=new Access();
        $catlist=$Access->getCate(session('role_id'));
        //获取当前用户系统权限
        $menu=$Access->getMenu(session('role_id'));
        //获取当前用户系统权限
        $node=$Access->getNode(session('role_id'));        
        //写入缓存
        session('catlist',$catlist);
        session('menu_node',$menu); 
        session('node',$node);       
    }

    //模块初始化
    public function ModuleInit(){
        //后台开放性检测
        $config=cache('setting')['admin'];
        $request=Request::instance()->domain();
        $url=str_replace('https://',"",str_replace('http://',"",$request));
        if($config['close']==1&&Functions::getDomain($config['host'])!=$url){
            return abort(404,'页面不存在');
        }        
        //login模块放行
        if(Request::instance()->controller()=='Login'){
            return true;
        }
        //登录状态检测
        if(!Admin::isLogin())$this->error(lang('admin_login_no'),'Login/index');
        //权限检测
        $map=[
            'model'=>Request::instance()->module(),
            'controller'=>Request::instance()->controller(),
            'action'=>Request::instance()->action()
        ];
        $node_id=db('node')->where($map)->value('id');
        //检测加入权限管理的节点
        if($node_id&&!in_array($node_id,session('node'))){
            return $this->error(lang('access_error'),'admin/index/error');
        }
    }

    //登录初始化
    public function LoginInit(){
        //后台配置
        $config=cache('setting')['admin'];
        //ip
        $ip=input('server.REMOTE_ADDR');
        //错误次数检测
        if($config['number']){
            //检测登录错误IP
            $error=cache('admin_login_error');
            if($error[$ip]>=$config['number']){
                return abort(404,'登录错误次达到：'.$config['number']);
            }
        }
        //禁止IP
        if($config['ip']){
            $list=explode("\r\n", $config['ip']);
            if(in_array($ip, $list)){
                return abort(404,'IP 被限制');
            }
        }
    }

	//登录成功
    public function LoginSuccess(&$user){
        $this->update();
    }

    //栏目权限更新
    public function AccessCateUpdate(&$user){
        $this->update();
    }

    //系统权限更新
    public function AccessMenuUpdate(&$user){
        $this->update();
    } 

    //系统权限更新
    public function AccessNodeUpdate(&$user){
        $this->update();
    }       

    //权限更新
    public function RoleUpdate(&$user){
        $this->update();
    }

    //权限删除
    public function RoleDelete(&$user){
        $this->update();
    }
    
    //后台菜单更新
    public function MenuUpdate($info,$id){
        //新增栏目时触发
        if(!$info['id']){
            //是否为顶级栏目
            if($info['parentid']!=0){
                //获取上级栏目的角色
                $list=db('access_menu')->where('menu_id',$info['parentid'])->field('role_id')->select();
                foreach ($list as &$v) {
                    $v['menu_id']=$id;
                }
                //添加权限记录
                db('access_menu')->insertAll($list);
            }else{
                //添加超级管理员权限记录
                db('access_menu')->insert(['menu_id'=>$id,'role_id'=>1]);
            }
        }
        $this->update();
    }
    //后台菜单删除
    public function MenuDelete($info){
        $list=db('access_menu')->where('menu_id',$info['id'])->delete();
    }

    //栏目更新
    public function CateUpdate($info,$catid){
        //新增栏目时触发
        if(!$info['catid']){
            //是否为顶级栏目
            if($info['parentid']!=0){
                //获取上级栏目的角色
                $list=db('access_cate')->where('cate_id',$info['parentid'])->field('role_id')->select();
                foreach ($list as &$v) {
                    $v['cate_id']=$catid;
                }
                //添加权限记录
                db('access_cate')->insertAll($list);
            }else{
                //添加超级管理员权限记录
                db('access_cate')->insert(['cate_id'=>$catid,'role_id'=>1]);
            }
        }
        $this->update();
    }
    //栏目删除
    public function CateDelete($info){
        $list=db('access_cate')->where('cate_id',$info['catid'])->delete();
    } 

    //节点更新
    public function NodeUpdate($info,$id){
        //新增时触发
        if(!$info['id']){
            //是否为顶级栏目
            if($info['parentid']!=0){
                //获取上级栏目的角色
                $list=db('access_node')->where('node_id',$info['parentid'])->field('role_id')->select();
                foreach ($list as &$v) {
                    $v['node_id']=$id;
                }
                //添加权限记录
                db('access_node')->insertAll($list);
            }else{
                //添加超级管理员权限记录
                db('access_node')->insert(['node_id'=>$id,'role_id'=>1]);
            }
        }
        $this->update();
    }

    //节点删除
    public function NodeDelete($info){
        $list=db('access_node')->where('node_id',$info['id'])->delete();
    }    
}