<?php
namespace app\home\behavior;

use think\Request;
use think\Config;

class Theme {

	//多主题实现
    public function ModuleInit(){
        header('auth: https://github.com/lscho'); 
        $default_view_path = Config::get('template.view_path');//获取用户访问的模块所设置的模版目录

         if ($default_view_path != '') {
            //获取当前主题名称
            $setting=cache('setting');
            $theme_path=isset($setting['web']['theme'])?$setting['web']['theme']:'default';
            Config::set('template.view_path', $default_view_path .DS. $theme_path . DS);//根据模块名称和来访设备类型重置模版路径设置          
        }
    }
}