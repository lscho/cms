<?php

namespace app\api\controller;

use think\Controller;

class Base extends Controller{

	public function json($data,$msg){
		if($data){
			return json(['status'=>1,'msg'=>$msg?$msg:'success','data'=>$data]);
		}else{
			return json(['status'=>0,'msg'=>$msg?$msg:'error'],404);
		}
	}
}