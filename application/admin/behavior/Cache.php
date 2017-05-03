<?php
namespace app\admin\behavior;

use util\Tree;
use app\admin\model\Setting;
use think\Hook;

class Cache {

    //自动更新全部缓存
    public function ModuleInit(){
        //映射关系
        $data=[
            'catlist'=>'CacheCate',
            'setting'=>'CacheSetting',
            'link'=>'CacheLink',
            'navigation'=>'CacheNavigation',
            'menu'=>'CacheMenu',
            'adsense'=>'CacheAdsense',
            'work'=>'CacheWork',
            'crontab'=>'CacheCrontab',
            'node'=>'CacheNode'
        ];
        //检测更新
        foreach($data as $k=>$v){
            if(!cache('?'.$k)){
                $this->$v();
            }
        }
    }

	//更新栏目缓存
    public function CacheCate(){
        //栏目信息
    	$list=db('cate')->order('sort desc,catid asc')->select();
        //缓存树形数据
        cache('cate_data',Tree::makeTree($list,['primary_key'=>'catid']));
        //缓存线性数据
		$data = Tree::makeTreeForHtml($list,['primary_key'=>'catid']);
        //栏目模型信息
        foreach ($data as &$val) {
            //模板数据
            $val['cate_tpl']=json_decode($val['cate_tpl'],true);
            $val['list_tpl']=json_decode($val['list_tpl'],true);
            $val['show_tpl']=json_decode($val['show_tpl'],true);
            //模型基础信息
            $val['models']=db('models')->where('id',$val['models_id'])->find();
            //模型字段信息
            $val['models_data']=db('models_data')->where('models_id',$val['models_id'])->select();
        }
    	cache('catlist',$data);
    }

    //更新配置缓存
    public function CacheSetting(){
    	$info=Setting::get(1);
    	cache('setting',$info->toArray());
    }

    //更新友情链接缓存
    public function CacheLink(){
        $list=db('link')->order('sort desc,id asc')->select();       
        cache('link',$list);
    }

    //更新导航缓存
    public function CacheNavigation(){
        $list=db('navigation')->order('sort desc,id asc')->select();
        //缓存树形数据
        cache('navigation_data',Tree::makeTree($list,['primary_key'=>'id']));
        //缓存线性数据    
        $data = Tree::makeTreeForHtml($list,['primary_key'=>'id']);
        cache('navigation',$data);      
    }    

    //更新后台菜单缓存
    public function CacheMenu(){
        $list=db('menu')->order('sort desc,id asc')->select();
        //缓存树形数据
        cache('menu_data',Tree::makeTree($list,['primary_key'=>'id']));
        //缓存线性数据          
        $data = Tree::makeTreeForHtml($list,['primary_key'=>'id']);
        cache('menu',$data);
    }

    //更新广告位缓存
    public function CacheAdsense(){
        $list=db('adsense')->order('sort desc,id asc')->select();
        //缓存树形数据
        cache('adsense_data',Tree::makeTree($list,['primary_key'=>'id']));
        //缓存线性数据           
        $data = Tree::makeTreeForHtml($list,['primary_key'=>'id']);
        cache('adsense',$data);
    } 

    //更新工作流缓存
    public function CacheWork(){
        $list=db('workflow')->select();
        foreach ($list as $v) {
            $v['admin']=json_decode($v['admin'],1);
            $data[$v['id']]=$v;
        }
        cache('workflow',$data);
    }

    //更新定时任务缓存
    public function CacheCrontab(){
        $list=db('crontab')->select();
        cache('crontab',$list);
    }

    //更新权限节点
    public function CacheNode(){
        $list=db('node')->order('id asc')->select();
        //缓存树形数据
        cache('node_data',Tree::makeTree($list,['primary_key'=>'id']));
        //缓存线性数据           
        $data = Tree::makeTreeForHtml($list,['primary_key'=>'id']);
        cache('node',$data);        
    }

}