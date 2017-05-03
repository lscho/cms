<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class WechatReply extends Base{
    public function setContentAttr($value,$data){
        if($data['type']==2){	//图文消息类型转换
        	if(strpos($value['PicUrl'], 'http')===false){
                $http_type = \util\Functions::getHttpType(); 
        		$value['PicUrl']=$http_type.$_SERVER['SERVER_NAME'].$value['PicUrl'];
        	}
        	return json_encode($value);
        }else{
        	return $value;
        }
    }

    public function getContentAttr($value,$data){
    	if($data['type']==2){	//图文消息类型转换
    		return json_decode($value,true);
    	}else{
    		return $value;
    	}
    }
}