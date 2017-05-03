<?php
namespace app\common\taglib;
use think\template\TagLib;
use think\view;
/*
* Sp标签库
*/
class Sp extends TagLib{
   /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'content'      => ['attr' => 'action,table,join,catid,where,field,order,num,return,more,page', 'close' => 1], 
        'select'       => ['attr' => 'table,join,catid,where,field,order,num,return', 'close' => 1], 
        'sql'           => ['attr' => 'sql,return', 'close' => 1], 

    ];

    /**
     * content标签入口
     */
    public function tagContent($tag, $content){
        //默认参数处理
        empty($tag['return'])&&$tag['return']='data';
        empty($tag['num'])&&$tag['num']=10;
        empty($tag['page'])&&$tag['page']=1;
        //数据分发
        $action="actionContent".ucfirst($tag['action']);
        return $this->$action($tag,$content);
    }

    /**
     * content action="get"
     */    
    public function actionContentGet($tag, $content){
        //模板输出
        $parse ='<?php ';
        $parse .='$Model=new app\admin\model\Content;';
        $parse .='$'.$tag['return'].'=$Model->'.$tag['action'].'('.$tag['where'].');';
        $parse .='?>';
        $parse .=$content;
        return $parse;        
    }

    /**
     * content action="list"
     */ 
    public function actionContentList($tag, $content){
        $parse ='<?php ';
        $parse .='$Model=new app\admin\model\Content;';
        //查询条件
        $where="1=1";
        if(!empty($tag['catid'])){
            $parse .='$_catid=$catlist['.$tag['catid'].']?rtrim("'.$tag['catid'].'".",".$catlist['.$tag['catid'].']["sonid"],","):0;';
            $where.=' AND catid in ($_catid)';
        }
        if(!empty($tag['where'])){
            $where.=' AND ('.$tag['where'].')';
        }
        $parse .='$data=$Model->where("'.$where.'")';
        //排序
        empty($tag['order'])||$parse .='->order('.$tag['order'].')';
        //字段
        empty($tag['field'])||$parse .='->field('.$tag['field'].')';
        //分页
        $parse .='->paginate('.$tag['num'].',false,["page"=>'.$tag['page'].']);'; 
        //返回
        if($tag['more']){
            $parse .='$'.$tag['return'].'=$Model->getMore($data);';
        }else{
            $parse .='$'.$tag['return'].'=$data;';
        }

        $parse .='?>';
        $parse .=$content;
        return $parse;          
    }

    /**
     * select
     * GOTO
     */     
    public function tagSelect($tag, $content){
        //默认参数处理
        empty($tag['return'])&&$tag['return']='data';
        empty($tag['num'])&&$tag['num']=10;  
        empty($tag['page'])&&$tag['page']=1;      
        $parse ='<?php ';
        //表名
        $parse.='$'.$tag['return']."=Db::table('".$tag['table']."')";
        //关联
        if($tag['join']){
            $parse.="->join('".$tag['join']."')";
        }
        //查询条件
        if(!empty($tag['catid'])){
            $parse .='$_catid=$catlist['.$tag['catid'].']?rtrim("'.$tag['catid'].'".",".$catlist['.$tag['catid'].']["sonid"],","):0;';
            $where.=' AND catid in ($_catid)';
        }
        if(!empty($tag['where'])){
            $where.=' AND ('.$tag['where'].')';
        }
        $parse.="->where('".$where."')";
        //排序
        empty($tag['order'])||$parse .='->order('.$tag['order'].')';
        //字段
        empty($tag['field'])||$parse .='->field('.$tag['field'].')';
        //分页
        $parse .='->paginate('.$tag['num'].',false,["page"=>'.$tag['page'].']);'; 
        $parse .='?>';
        $parse .=$content;
        return $parse;                 
    }
    /**
     * sql
     */ 
    public function tagSql($tag,$content){
        //默认参数处理
        empty($tag['return'])&&$tag['return']='data';       
        $parse ='<?php ';
        $parse .='$'.$tag['return']."=Db()->query(".'"'.$tag['sql'].'"'.");";
        $parse .='?>';
        $parse .=$content;
        return $parse;                   
    }


}