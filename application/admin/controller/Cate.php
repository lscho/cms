<?php
namespace app\admin\controller;

use app\admin\model\Cate as Model;
use app\admin\model\Models as Models;
use app\admin\model\ModelsData as ModelsData;
use think\Hook;

/*
 * 分类
 */
class Cate extends Base{

    /*
     * 栏目模块独立导航
     */
    public function sidebar(){
        $catlist = db('cate')->field('catid,catname,parentid,sort')->order('sort desc,catid asc')->select();
        $data    = \util\Tree::makeTree($catlist, ['primary_key' => 'catid', 'children_key' => 'products','primary_index'=>false]);
        $this->assign('catlist',json_encode($data));
        return $this->fetch();
    }

    /*
     * 栏目首页
     */
    public function index(){
        //模型列表
        $Models        = new Models();
        $map=[
            'type'=>'content',
            'status'=>1
        ];
        $list          = $Models->where($map)->order('sort desc,id desc')->select();
        $this->assign('models', $list);
        //栏目列表
        $catlist = db('cate')->field('catid,catname,parentid,sort,models_id')->order('sort desc,catid asc')->select();
        foreach ($catlist as &$val) {
            //模型基础信息
            $val['models']=db('models')->where('id',$val['models_id'])->find();            
        }
        $data    = \util\Tree::makeTreeForHtml($catlist, ['primary_key' => 'catid', 'children_key' => 'products']);
        $this->assign('catlist',$data);
        return $this->fetch();
    }

    /*
     * 添加栏目
     */
    public function add($models_id=0){
        $Model = new Model();
        $ModelsData = new ModelsData();
        //get
        if (request()->isGet()) {
			//获取栏目模型
			$map['models_type']='cate';
			$map['models_id']=$models_id;
			$data=$ModelsData->where($map)->order('sort DESC,id asc')->select();
			$this->assign('modelsData',$data);        	
            //栏目详情
            if ($catid = input('get.catid')) {
                $info = $Model->where('catid', $catid)->find();
                $this->assign('info', $info);
            }
            //父级栏目
            if(input('get.parentid')){
                    $this->assign('parent',cache('catlist')[input('get.parentid')]);
            }            
            //获取模板列表
            $this->assign('tpl', $this->getTpl());
            if(cache('setting')['wap']['close']!=1){
                $this->assign('wapTpl',$this->getTpl('wap'));
            }
            //模型列表
            $Models        = new Models();
            $map=[
            	'type'=>'content',
            	'status'=>1
            ];
            $list          = $Models->where($map)->order('sort desc,id desc')->select();
            $this->assign('models', $list);
            //审核流程
            $this->assign('workflow',cache('workflow'));
            return $this->fetch();
        }
        //表单验证
        $info   = input('post.info/a');
        $result = $this->validate($info, 'Cate');
        if (true !== $result) {
            return $this->error($result);
        }
        //模型验证
		$data=input('post.form/a');
        if($data){
            $result=$ModelsData->validate($data,$models_id,'cate');
            if(true !== $result['status']){
                return $this->error($result['data']);
            }
            $info['info']=$result['data'];
        }		        
        //添加分类
        $Model->isUpdate($info['catid'])->save($info);
        if ($Model->catid) {
            //更新cache
            Hook::listen('cache_cate');
            //记录动作
            Hook::listen('cate_update', $info,$Model->catid);
            $this->success(lang('save_success'));
        } else {
            $this->error(lang('save_error'));
        }
    }

    /*
     * 删除栏目
     */
    public function delete(){
        if (input('?get.catid')) {
            $Model = new Model();
            $cate  = $Model->where('catid', input('get.catid'))->find();
            $info  = $cate->toArray();
            if ($cate->delete()) {
                //更新cache
                Hook::listen('cache_cate');
                //记录动作
                Hook::listen('cate_delete', $info);
                $this->success(lang('action_success'));
            } else {
                $this->error(lang('action_error'));
            }
        } else {
            return $this->error(lang('parameter_empty'));
        }
    }

    /*
     * 栏目排序
     */
    public function sort()
    {
        if (!input('?post.sort')) {
            return $this->error(lang('parameter_empty'));
        }
        $sort = input('post.sort/a');
        foreach ($sort as $key => $value) {
            $list[] = [
                'catid' => $key,
                'sort'  => $value,
            ];
        }
        $Model = new model();
        $rs    = $Model->saveAll($list);
        if ($rs) {
            //更新cache
            Hook::listen('cache_cate');
            $this->success(lang('action_success'));
        } else {
            $this->error(lang('action_error'));
        }
    }
}
