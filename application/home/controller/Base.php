<?php
namespace app\home\controller;

use think\Controller;
use think\Cache;
use app\admin\model\Crontab;

/**
* 控制器基类
*/
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
        //定时任务
        $this->crontab();
    }

    public function crontab(){
        $crontab=new Crontab();
        $data=Cache::get('crontab');
        try {
            foreach ($data as $val) {
                $check=false;
                if($val['type']==1&&!$val['execution']&&$val['time']<=time()){    //定时任务
                    $check=true;
                }
                if($val['type']==2&&(time()-$val['interval']*86400)>$val['execution']){   //循环任务
                    $check=true;
                }
                if($check){
                    ob_start();
                    $crontab->run($val['action'],$val['id']);
                    ob_get_clean();
                    ob_end_flush();
                }
            }
        } catch (Exception $e) {
            
        }
    }
}