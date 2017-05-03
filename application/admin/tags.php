<?php

// 应用行为扩展定义文件
return [
    /*模块初始化*/
	'module_init'=>['app\\admin\\behavior\\Role'],    	                                //模块初始化
    /*动作记录*/
    'cate_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],    		//栏目更新标签位
    'cate_delete'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],    		//栏目删除标签位
    'content_update'=>['app\\admin\\behavior\\Log'],    								//内容更新标签位
    'content_delete'=>['app\\admin\\behavior\\Log'],    								//内容更新标签位
    'setting_update'=>['app\\admin\\behavior\\Log'],    	                            //配置更新标签位
    'navigation_update'=>['app\\admin\\behavior\\Log'],                                 //导航更新标签位
    'navigation_delete'=>['app\\admin\\behavior\\Log'],                                 //导航更新标签位
    'navigation_set'=>['app\\admin\\behavior\\Log'],                                    //导航设置标签位
    'navigation_unset'=>['app\\admin\\behavior\\Log'],                                  //导航取消标签位
    'menu_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],   		//菜单更新标签位
    'menu_delete'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],          //菜单删除标签位
    'link_update'=>['app\\admin\\behavior\\Log'],                                       //链接更新标签位
    'link_delete'=>['app\\admin\\behavior\\Log'],                                       //链接删除标签位
    'crontab_update'=>['app\\admin\\behavior\\Log'],                                    //定时任务更新标签位
    'crontab_delete'=>['app\\admin\\behavior\\Log'],                                    //定时任务删除标签位    
    'adsense_update'=>['app\\admin\\behavior\\Log'],                                    //广告更新标签位
    'adsense_delete'=>['app\\admin\\behavior\\Log'],                                    //广告删除标签位    
    'model_update'=>['app\\admin\\behavior\\Log'],                                      //模型更新标签位  
    'model_delete'=>['app\\admin\\behavior\\Log'],                                      //模型更新标签位  
    'login_init'=>['app\\admin\\behavior\\Role'],                                       //登录初始化
    'login_success'=>['app\\admin\\behavior\\Session','app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],		//登录成功标签位
    'login_error'=>['app\\admin\\behavior\\Log'],                                        //登录失败
    'log_delete'=>['app\\admin\\behavior\\Log'],                                        //清除一个月前日志
    'role_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],          //编辑角色标签位
    'role_delete'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],          //删除角色标签位
    'node_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],          //节点删除标签位
    'node_delete'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],          //节点删除标签位
    'access_cate_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],   //分配栏目标签位
    'access_menu_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],   //分配栏目标签位
    'access_node_update'=>['app\\admin\\behavior\\Log','app\\admin\\behavior\\Role'],   //分配接点标签位
    'admin_update'=>['app\\admin\\behavior\\Log'],                                      //管理员更新标签位
    'admin_delete'=>['app\\admin\\behavior\\Log'],                                      //管理员删除标签位
    'user_update'=>['app\\admin\\behavior\\Log'],                                      //管理员更新标签位
    'user_delete'=>['app\\admin\\behavior\\Log'],                                      //管理员删除标签位    
    'login_out'=>['app\\admin\\behavior\\Session'],			                            //退出登录标签位
    /*cache更新*/
    'cache_navigation'=>['app\\admin\\behavior\\Cache'],                                //导航cache更新
    'cache_cate'=>['app\\admin\\behavior\\Cache'],                                      //栏目cache更新
    'cache_setting'=>['app\\admin\\behavior\\Cache'],                                   //配置cache更新
    'cache_menu'=>['app\\admin\\behavior\\Cache'],                                      //菜单cache更新
    'cache_link'=>['app\\admin\\behavior\\Cache'],                                      //连接cache更新
    'cache_adsense'=>['app\\admin\\behavior\\Cache'],                                   //广告cache更新
    'cache_work'=>['app\\admin\\behavior\\Cache'],                                      //工作流cache更新
    'cache_crontab'=>['app\\admin\\behavior\\Cache'],                                   //定时任务cache更新
    'cache_node'=>['app\\admin\\behavior\\Cache'],                                      //权限接点cache更新
];
