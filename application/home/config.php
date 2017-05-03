<?php
//配置信息
$setting=cache('setting');
$_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
$base=(('/' == $_root || '\\' == $_root) ? '' : $_root);
//模板信息
$theme_path=isset($setting['web']['theme'])?$setting['web']['theme']:'default';
$theme_path.='/';
return [
    // +----------------------------------------------------------------------
    // | 前台模块配置
    // +----------------------------------------------------------------------
    //默认错误跳转对应的模板文件
    'dispatch_error_tmpl' => APP_PATH.'home/view/'.$theme_path.'public/error.html',
    //默认成功跳转对应的模板文件
    'dispatch_success_tmpl' => APP_PATH.'home/view/'.$theme_path.'public/success.html',	
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__ROOT__'=>$setting['site']['host'],
        '__STATIC__' =>$setting['site']['static'].$base.'/static/home',
        '__UPLOAD__'=>$setting['site']['upload'].$base,
    ],   
    'template'               => [
        // 模板路径
        'view_path'    => APP_PATH.'home'.DS.'view', 
        //预加载标签库
        'taglib_pre_load'=>'app\common\taglib\Sp',       
    ],
];
