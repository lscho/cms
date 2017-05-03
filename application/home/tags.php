<?php

// 应用行为扩展定义文件
return [
    'module_init'=>['app\\home\\behavior\\Theme'],    										//模块初始化
    'login_success'=>['app\\home\\behavior\\Session'],    									//登录成功
    'login_out'=>['app\\home\\behavior\\Session'],    										//退出登录
];
