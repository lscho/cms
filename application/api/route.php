<?php
return [
    // 定义资源路由
    '__rest__'=>[
        'api/content'=>'api/content',
        'api/cate'=>'api/cate',
        'api/form'=>'api/form',
        'api/form/:form_id/data'=>'api/formdata',
        'api/form/:form_id/data/:data_id'=>'api/formdata'
    ],
];
