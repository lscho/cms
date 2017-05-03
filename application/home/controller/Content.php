<?php
namespace app\home\controller;
use app\admin\model\Content as Model;
use think\Request;

class Content extends Base{

	//栏目
    public function cate($catid=0){
        $catlist=cache('catlist');
        $type=$catlist[$catid]['sonid']?'cate':'list';
        if($this->view->setting['jump'][$type]&&Request::instance()->isMobile()){
            return $this->redirect('wap/content/'.$type,['catid'=>$catid]);
        }        
    	if(!$catid){
    		return $this->error(lang('parameter_empty'));
    	}
        //基础信息
        $this->assign('catid',$catid);    
        $this->assign('parentid',$catlist[$catid]['parentid']);
        //文章列表
        $Model=new Model();
        $map=[
            'is_show'=>1,
            'is_delete'=>0,
            'status'=>99
        ];
        //栏目ID
        if($catid){
            $map['catid']=['in',$catid.','.$catlist[$catid]['sonid']];
        }
        //标题
        if(input('?get.title')){
            $map['title']=['like','%'.input('get.title').'%'];
        }
        //首页
        if(input('?get.is_index')){
            $map['is_index']=1;
        }
        //栏目
        if(input('?get.is_cate')){
            $map['is_cate']=1;
        }   
        //状态
        if(input('get.status')){
            $map['status']=input('get.status');
        }     
        $this->assign('status',$map['status']);
        //排序
        $order=[
            '0'=>'id desc',
            '1'=>'id asc',
            '2'=>'addtime desc',
            '3'=>'addtime asc',
            '4'=>'view desc',
            '5'=>'view asc',

        ];        
        $data=$Model->where($map)->order($order)->paginate(15);
        $list=$Model->getMore($data);
        $this->assign('list',$list);
        $this->assign('page',$data->render());
    	//模板
    	$tpl=$catlist[$catid][$type.'_tpl']['pc'];
    	return $this->fetch($tpl);
    }

    //详情
    public function show($id){
        if($this->view->setting['jump']['show']&&Request::instance()->isMobile()){
            return $this->redirect('wap/content/show',['id'=>$id]);
        }
        if(!$id){
            return $this->error(lang('parameter_empty'));
        }
        //内容基础信息
        $Model=new Model();
        $map=[
            'is_delete'=>0,
            'id'=>$id,
            'is_show'=>1,
            'status'=>99
        ];
        $info=$Model->where($map)->find();
        if(!$info){
            return $this->error(lang('content_empty'));
        }
        $data=$Model->getMoreInfo($info);
        $this->assign('info',$data);
        //栏目信息
        $this->assign('catid',$info->catid);
        $catlist=cache('catlist');
        $this->assign('parentid',$catlist[$info->catid]['parentid']);
        //上一篇
        $map['catid']=$info->catid;
        $map['id']=['<',$id];
        $previous=$Model->where($map)->order('id desc')->find();
        $this->assign('previous',$previous);
        //下一篇
        $map['id']=['>',$id];
        $next=$Model->where($map)->order('id asc')->find();
        $this->assign('next',$next);
        //优化信息
        $seo=[
            'title'=>$info['title'].' - '.$catlist[$info->catid]['catname'].' -',
            'keywords'=>$info['keywords']
        ];
        $this->assign('seo',$seo);
        //模板信息
        $tpl=$info->tpl['pc']?$info->tpl['pc']:$catlist[$info->catid]['show_tpl']['pc'];
        //增加阅读量
        $Model->where('id',$id)->setInc('view');
        return $this->fetch($tpl);
    }
}
