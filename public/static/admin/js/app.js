$(function() {
    //全局存储
    var store = $.AMUI.store;
    //frame
    $("[data-frame]").on('click',function(){
        $("[name='_sidebar']").attr('src',$(this).data('sidebar'));
        $("[name='_content']").attr('src',$(this).data('content'));
        //记录地址
        store.set('sidebar',$(this).data('sidebar'));
        store.set('content',$(this).data('content'));
        return false;
    });
    $("[data-sidebar-frame]").on('click',function(){
        $("[name='_content']", window.parent.document).attr('src',$(this).data('src'));
        $('.sidebar-nav-sub .sidebar-nav-link a').removeClass('active');
        $(this).addClass('active');
        store.set('content',$(this).data('src'));
        return false;
    });
    //刷新后跳转
    if(window.top.location==window.self.location){
        if(store.get('sidebar'))$("[name='_sidebar']").attr('src',store.get('sidebar'));
        if(store.get('content'))$("[name='_content']").attr('src',store.get('content'));
    }
    //data-api
    $("[data-model]").on('click',function(){
        var model=$(this).data('model');
        var action=$(this).data('action');
        var option = jQuery.AMUI.utils.parseOptions($(this).attr('data-option'));
        var msg=$(this).data('msg');
        var prompt=$(this).data('prompt');        
        var $this=$(this);
        switch(model){
            case 'check':
            //全选
                var todo=function(){
                    $(action).prop('checked',$this.is(":checked"));
                }
                break;
            //layer封装
            case 'layer':
                var todo=function(){
                    if(layer){
                        layer[action](option);
                    }
                }
                break;
            //表单提交
            case 'ajaxform':
                var todo=function(){
                    option.url=option.url?option.url:$(option.form).attr('action');
                    if(!option.url){
                        console.warn('url未定义');
                        return false;
                    }       
                    option.type=$(option.form).attr('method');
                    option.data=$(option.form).serialize();
                    option.success=window[$this.data('success')];
                    option.error=window[$this.data('error')];
                    $.ajax(option);
                }
                break;
            //ajax
            case 'ajax':
                var url=$(this).data('url');
                var success=window[$this.data('success')];
                var error=window[$this.data('error')];
                if(!url){
                    console.warn('url未定义');
                    return false;
                }
                var todo=function(){
                    $.ajax({
                        type:action,
                        data:option,
                        url:url,
                        success:function(data){
                            if(typeof success=='function'){
                                success(data,$this);
                            }
                        },
                        error:error
                    });
                    return false;
                }
            break;
        }
                if(msg&&layer){
                    if(prompt){
                        layer.prompt({title: msg, formType: 3}, function(text, index){
                            if(prompt.indexOf(':')==-1&&text==prompt){
                                layer.close(index);
                                return todo();                                
                            }else if(prompt.indexOf(':')>-1){
                                var key=prompt.match(/:(\S*)/)[1]
                                option[key]=text;
                                layer.close(index);
                                return todo();
                            }else{
                                layer.msg('口令不正确')
                            }
                        });
                    }else{
                        var index=layer.confirm(msg, {
                          btn: ['确定','取消'] //按钮
                        }, function(){
                            layer.close(index);
                            return todo();
                        });
                    }
                }else{
                    return todo();
                }
        return false;
    });
});