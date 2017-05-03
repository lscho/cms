<?php
namespace app\admin\validate;

use think\Validate;

/*
 * 分类模型验证器
 */
class Cate extends Validate{
    //验证规则
    protected $rule = [
        'catname'   => 'require|max:25',
        'models_id' => 'require',
    ];
    //错误提示
    protected $message = [
        'catname.require'      => '栏目名称必填',
        'catname.max'          => '栏目名称最多不能超过25个字符',
        'models_id.require' => '模型不能为空',
    ];
}
