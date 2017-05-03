<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use app\admin\model\Form as Model;

class Form extends Base{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(){
        //表单列表
        $Model=new Model();
        $pageSize=input('?get.pageSize')?input('get.pageSize'):10;
        $list=$Model->order('id desc')->paginate($pageSize);
        return $this->json($list?collection($list)->toArray():[]);
    } 
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     */
    public function read($id){
        //内容基础信息
        $Model=new Model();
        $info=$Model->get($id);
        return $this->json($info->id?$info->toArray():[]);
    }
}
