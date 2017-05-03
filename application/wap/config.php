<?php
//配置信息
$setting=cache('setting');
$_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
$base=(('/' == $_root || '\\' == $_root) ? '' : $_root);
return [
    // +----------------------------------------------------------------------
    // | wap 模块配置
    // +----------------------------------------------------------------------
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__ROOT__'=>$setting['site']['host'],
        '__STATIC__' =>$setting['site']['static'].$base.'/static/wap',
        '__UPLOAD__'=>$setting['site']['upload'].$base,
    ],   
    'template'               => [
        //预加载标签库
        'taglib_pre_load'=>'app\common\taglib\Sp',       
    ],
];
