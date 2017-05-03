<?php
namespace util;

class Functions{

	//获取域名
	static function getDomain($url){
		return str_replace('https://',"",str_replace('http://',"",$url));
	}

	//获取ssl状态
	static function getHttpType(){
		return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
	}

	//获取文件夹路径
	static function getRoot(){
		$_temp = explode('.php', $_SERVER['PHP_SELF']);
		$_php_self=rtrim(str_replace($_SERVER['HTTP_HOST'], '', $_temp[0] . '.php'), '/');
		$_root = rtrim(dirname($_php_self), '/');
		return (('/' == $_root || '\\' == $_root) ? '' : $_root);
	}
}