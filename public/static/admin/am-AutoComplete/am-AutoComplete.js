!function($,UI){
	"use strict";
	function complete(data){
		this.$element=data.element;
		this.option=data.option;
		if(!this.option.width){
			this.option.width=this.$element.width();
		}
		this._init();
	}
	complete.prototype._init=function(){
		//追加元素
		var $complete=$('<div style="position: relative;z-index:999;height:0px;"><ul style="border: 1px solid #dedede;" class="am-list am-list-static am-list-borde"></ul></div>');
		$complete.css({
			width:this.option.width,
			display:'none'
		});
		this.$element.after($complete);
		this.$complete=$complete;
		//监听事件
		var _this=this;
		this.$element.on('keyup',function(){
			_this._change(this);
		});
		this.$complete.on('click','ul li a',function(){
			_this.$complete.hide();
			_this.option.click(this,_this);
		});
	}
	complete.prototype._change=function(self){
		var _this=this;
		var url=_this.option.url;
		if(!url){
			return false;
		}
		$.get(url+"?value="+self.value,function(data){
			if(data.length<1){
				_this.$complete.hide();
				return false;
			}
			_this.data=data;
			var li="";
			var i=0;
			for (i in data) {
				li+='<li style="background-color: #f8f8f8;"><a data-index="'+i+'" href="javascript:"  class="am-text-truncate">'+data[i].value+'</a></li>';
			}
			_this.$complete.show().find('ul').html(li);
		},'json');
	}
	$.fn.extend({
		'complete':function(option){
			return new complete({
				element:this,
				option:option
			});
		}
	});	
}(jQuery,jQuery.AMUI);