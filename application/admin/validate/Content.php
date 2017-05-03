<?php
namespace app\admin\validate;

use think\Validate;

/*
 * 内容模型验证器
 */
class Content extends Validate{
    //验证规则
    protected $rule = [
        'title'     => 'require',
        'catid'     => 'require|gt:0',
        'models_id' => 'require',
    ];
    //错误提示
    protected $message = [
        'title.require' => '文章标题不能为空',
        'catid.require'         => '栏目不能为空',
        'catid.gt'         => '栏目不能为空',
        'models_id.require'     => '文章模型不能为空',
    ];
}
