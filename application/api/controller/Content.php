<?php

namespace app\api\controller;

use think\Request;
use app\admin\model\Content as Model;

class Content extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $Model=new Model();
        //栏目
        if(input('?get.catid')){
            $map['catid']=input('get.catid');
        }
        //标题
        if(input('?get.title')){
            $map['title']=['like','%'.input('get.title').'%'];
        }
        //分页
        $pageSize=input('?get.pageSize')?input('get.pageSize'):10;
        $list=$Model->where($map)->order('sort desc,id desc')->paginate($pageSize);
        return $this->json($list?collection($list)->toArray():[]);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //内容基础信息
        $Model=new Model();
        $info=$Model->get($id);
        return $this->json($info->id?$info->toArray():[]);
    }
}
