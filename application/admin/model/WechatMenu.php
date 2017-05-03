<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class WechatMenu extends Base{
	public function getTypeNameAttr($value,$data){
		return lang('wechat_menu_type')[$data['type']];
	}
}