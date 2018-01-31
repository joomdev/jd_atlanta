jQuery.gtooltip = {
	'tipclass' : 'gtooltip',
	'awaytime': 800,
	'ontime': 0,
	'ajax': 0,
	'ajax_cache': {},
	'ajaxloading': 'Loading....',
	'append': 'after',
	'position':'top',
	'closable':0,
	'on_close':'hide',
	'tid':'',
	'content':'',
	'trigger':'hover',
	'resetOnShow':false,
	'createOnShow':false,
	'spacing':3,
	'arrow_size':7,
	'css':{
		'background-color':'#000',
		'border-color':'#000',
		'border-radius':'4px',
		'border-width':'1px',
		'padding':'8px',
		'color':'#fff',
		'font-size':'12px',
		'max-width':'200px',
		'text-align':'center',
	},
};

(function($){
	$.fn.gtooltip = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gtooltip, params);
				
				var prefix = '';
				if(params.tid){
					prefix = '-'+params.tid;
				}
				
				return this.each(function(){
					if(!$(this).data('gtooltip'+prefix)){
						$(this).data('gtooltip'+prefix, new GTooltip(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gtooltip, params);
				
				var prefix = '';
				if(params.tid){
					prefix = '-'+params.tid;
				}
				
				var tip = $(this).data('gtooltip'+prefix);
				if(typeof tip == 'undefined'){
					return null;
				}
				
				switch (options){
					case 'show':
						return tip.show();
					case 'hide':
						return tip.hide();
					case 'destroy':
						return tip.destroy();
					case 'reset':
						return tip.reset();
					case 'get':
						return tip.get();
				}
			}
		}
	}
	
	var GTooltip = function(elem, params){
		this.element = elem;
		this.settings = params;
		
		this.shown = false;
		this.hidden = false;
		
		this.content = null;
		this.location = null;
		
		this.init();
	};
	
	GTooltip.prototype = {
		init: function(){
			var ttObj = this;
			ttObj.create();
			
			if(ttObj.settings.trigger == 'hover'){
				ttObj.initHover();
			}else if(ttObj.settings.trigger == 'click'){
				ttObj.initClick();
			}
			
			//show events
			$(ttObj.element).on('show.gtooltip', function(){
				if(ttObj.settings.resetOnShow || ttObj.settings.createOnShow){
					ttObj.reset();
				}
				//check ajax data
				if(ttObj.settings.ajax){
					if($.type($(ttObj.element).data('ajax')) !== 'undefined'){
						if($.type(ttObj.ajax_result) === 'undefined'){
							ttObj.setContent(ttObj.settings.ajaxloading);
							ttObj.reset();
							$.ajax({
								"type" : "GET",
								"url" : $(ttObj.element).data('ajax'),
								"cache" : true,
								"beforeSend" : function(res){
									//ttObj.setContent(ttObj.settings.ajaxloading);
								},
								"success" : function(res){
									ttObj.setContent(res);
									ttObj.ajax_result = res;
								},
							});
						}
					}
				}
			});
		},
		
		get: function(){
			var ttObj = this;
			return ttObj;
		},
		
		destroy: function(){
			var ttObj = this;
			ttObj.tip.remove();
			
			var prefix = '';
			if(ttObj.settings.tid){
				prefix = '-'+ttObj.settings.tid;
			}
			$(ttObj.element).removeData('gtooltip'+prefix);
			return true;
		},
		
		initClick: function(){
			var ttObj = this;
			$(ttObj.element).on('click', function(){
				if(ttObj.shown){
					ttObj.hide();
				}else{
					ttObj.show();
				}
			});
		},
		
		initHover: function(){
			var ttObj = this;
			$(ttObj.element).on('mouseover', function(){
				clearTimeout(ttObj.awaytime);
				//$this.gtooltip('show');
				var ontime_timeout = setTimeout(function(){
					ttObj.show();
					ttObj.tip.on('mouseover', function(){
						clearTimeout(ttObj.awaytime);
					});
					ttObj.tip.on('mouseleave', function(){
						var awaytime_timeout = setTimeout(function(){
							ttObj.hide();
						}, ttObj.settings.awaytime);
						ttObj.awaytime = awaytime_timeout;
					});
				}, ttObj.settings.ontime);
				ttObj.ontime = ontime_timeout;
			});
			$(ttObj.element).on('mouseleave', function(){
				clearTimeout(ttObj.ontime);
				var awaytime_timeout = setTimeout(function(){
					ttObj.hide();
				}, ttObj.settings.awaytime);
				ttObj.awaytime = awaytime_timeout;
			});
		},
		
		initClose: function(){
			var ttObj = this;
			
			ttObj.tip.find('.gtooltip-close').on('click', function(){
				/*if($.isFunction(ttObj.settings.onClose)){
					ttObj.settings.onClose.call(this);
				}*/
				$(ttObj.element).trigger('close.gtooltip');
				
				if(ttObj.settings.on_close == 'hide'){
					ttObj.hide();
				}else if(ttObj.settings.on_close == 'destroy'){
					ttObj.destroy();
				}
			});
		},
		
		initContent: function(){
			var ttObj = this;
			
			if($(ttObj.element).prop('title')){
				ttObj.content = $(ttObj.element).prop('title');
				$(ttObj.element).prop('title', '');
			}
			
			if(ttObj.settings.content){
				ttObj.content = ttObj.settings.content;
			}else{
				if($(ttObj.element).data('content')){
					ttObj.content = $(ttObj.element).data('content');
				}else{
					//$(ttObj.element).data('content', $(ttObj.element).prop('title'));
				}
			}
		},
		
		
		setContent: function(content){
			var ttObj = this;
			
			ttObj.content = content;
			ttObj.tip.find('.gtooltip-content').html(content);
		},
		
		show: function(){
			var ttObj = this;
			
			$(ttObj.element).triggerHandler('show.gtooltip');
			
			ttObj.tip.show();
			ttObj.shown = true;
			ttObj.hidden = false;
			return true;
		},
		
		hide: function(){
			var ttObj = this;
			
			$(ttObj.element).triggerHandler('hide.gtooltip');
			
			ttObj.tip.hide();
			ttObj.hidden = true;
			ttObj.shown = false;
			return true;
		},
		
		reset: function(){
			var ttObj = this;
			//ttObj.destroy();
			if($.type(ttObj.tip) !== 'undefined' && $.contains(document, ttObj.tip[0])){
				ttObj.tip.remove();
			}
			ttObj.create();
			return true;
		},
		
		create: function(){
			var ttObj = this;
			
			if($.type(ttObj.tip) !== 'undefined' && $.contains(document, ttObj.tip[0])){
				//tip already exists and we don't need to create a new one
				return;
			}
			
			ttObj.createTip();
			ttObj.positionTip();
			ttObj.styleTip();
			ttObj.initClose();
		},
		
		createTip: function(){
			var ttObj = this;
			
			if(ttObj.settings.closable){
				var $close_button = '<div class="gtooltip-close">&times;</div>';
			}else{
				var $close_button = '';	
			}
			
			ttObj.tip = $('<div class="'+ttObj.settings.tipclass+'" tid="'+ttObj.settings.tid+'">'+$close_button+'<div class="gtooltip-content">'+'</div><div class="gtooltip-arrow-border gtooltip-arrow-border-'+ttObj.settings.position+'"></div><div class="gtooltip-arrow gtooltip-arrow-'+ttObj.settings.position+'"></div></div>');
			ttObj.initContent();
			ttObj.setContent(ttObj.content);
		},
		
		positionTip: function(){
			var ttObj = this;
			
			//calculate position
			var $offset = $(ttObj.element).offset();
			var $position = $(ttObj.element).position();
			if($(ttObj.element).data('target')){
				var $offset = $(ttObj.element).data('target').offset();
				var $position = $(ttObj.element).data('target').position();
			}
			
			if(ttObj.settings.append == 'after'){
				$(ttObj.element).after(ttObj.tip);
				ttObj.location = $position;
			}else if(ttObj.settings.append == 'body'){
				$('body').append(ttObj.tip);
				ttObj.location = $offset;
			}
		},
		
		styleTip: function(){
			var ttObj = this;
			
			//apply css
			ttObj.tip.css(ttObj.settings.css);
			var arrow_css = {};
			arrow_css['border-'+ttObj.settings.position+'-color'] = ttObj.settings.css['background-color'];
			ttObj.tip.find('.gtooltip-arrow').css(arrow_css);
			var arrow_border_css = {};
			arrow_border_css['border-'+ttObj.settings.position+'-color'] = ttObj.settings.css['border-color'];
			ttObj.tip.find('.gtooltip-arrow-border').css(arrow_border_css);
			
			ttObj.tip.find('.gtooltip-arrow, .gtooltip-arrow-border').css('border-width', ttObj.settings.arrow_size + 'px');
			
			var border_width = parseInt(ttObj.settings.css['border-width']);
			
			if(ttObj.settings.position == 'top'){
				var $top = ttObj.location.top - ttObj.tip.outerHeight() - ttObj.tip.find('.gtooltip-arrow').outerHeight() - ttObj.settings.spacing;
				var $left = ttObj.location.left + $(ttObj.element).outerWidth()/2 - ttObj.tip.outerWidth()/2;
				//var $bottom = $(window).height() - ttObj.location.top + ttObj.tip.find('.gtooltip-arrow').outerHeight() + tip.settings.spacing;
				var $bottom = $(window).height() - $top - ttObj.tip.outerHeight(true);
				
				ttObj.tip.find('.gtooltip-arrow-border').css('left', ttObj.tip.outerWidth()/2 - ttObj.tip.find('.gtooltip-arrow-border').outerWidth()/2);
				ttObj.tip.find('.gtooltip-arrow').css('left', ttObj.tip.outerWidth()/2 - ttObj.tip.find('.gtooltip-arrow').outerWidth()/2);
				
				ttObj.tip.find('.gtooltip-arrow, .gtooltip-arrow-border').css('border-bottom-width', '0px');
				ttObj.tip.find('.gtooltip-arrow').css('bottom', -1 * (ttObj.settings.arrow_size) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('bottom', -1 * (ttObj.settings.arrow_size + border_width + 1) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('border-top-width', (ttObj.settings.arrow_size + 1) + 'px');
			}else if(ttObj.settings.position == 'bottom'){
				var $top = ttObj.location.top + $(ttObj.element).outerHeight() + ttObj.tip.find('.gtooltip-arrow').outerHeight() + ttObj.settings.spacing;
				var $left = ttObj.location.left + $(ttObj.element).outerWidth()/2 - ttObj.tip.outerWidth()/2;
				
				ttObj.tip.find('.gtooltip-arrow-border').css('left', ttObj.tip.outerWidth()/2 - ttObj.tip.find('.gtooltip-arrow-border').outerWidth()/2);
				ttObj.tip.find('.gtooltip-arrow').css('left', ttObj.tip.outerWidth()/2 - ttObj.tip.find('.gtooltip-arrow').outerWidth()/2);
				
				ttObj.tip.find('.gtooltip-arrow, .gtooltip-arrow-border').css('border-top-width', '0px');
				ttObj.tip.find('.gtooltip-arrow').css('top', -1 * (ttObj.settings.arrow_size) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('top', -1 * (ttObj.settings.arrow_size + border_width + 1) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('border-bottom-width', (ttObj.settings.arrow_size + 1) + 'px');
			}else if(ttObj.settings.position == 'right'){
				var $top = ttObj.location.top + $(ttObj.element).outerHeight()/2 - ttObj.tip.outerHeight()/2;
				var $left = ttObj.location.left + $(ttObj.element).outerWidth() + ttObj.tip.find('.gtooltip-arrow').outerWidth() + ttObj.settings.spacing;
				
				ttObj.tip.find('.gtooltip-arrow-border').css('top', ttObj.tip.outerHeight()/2 - ttObj.tip.find('.gtooltip-arrow-border').outerHeight()/2);
				ttObj.tip.find('.gtooltip-arrow').css('top', ttObj.tip.outerHeight()/2 - ttObj.tip.find('.gtooltip-arrow').outerHeight()/2);
				
				ttObj.tip.find('.gtooltip-arrow, .gtooltip-arrow-border').css('border-left-width', '0px');
				ttObj.tip.find('.gtooltip-arrow').css('left', -1 * (ttObj.settings.arrow_size) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('left', -1 * (ttObj.settings.arrow_size + border_width + 1) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('border-right-width', (ttObj.settings.arrow_size + 1) + 'px');
			}else if(ttObj.settings.position == 'left'){
				var $top = ttObj.location.top + $(ttObj.element).outerHeight()/2 - ttObj.tip.outerHeight()/2;
				var $left = ttObj.location.left - ttObj.tip.outerWidth() - ttObj.tip.find('.gtooltip-arrow').outerWidth() - ttObj.settings.spacing;
				
				ttObj.tip.find('.gtooltip-arrow-border').css('top', ttObj.tip.outerHeight()/2 - ttObj.tip.find('.gtooltip-arrow-border').outerHeight()/2);
				ttObj.tip.find('.gtooltip-arrow').css('top', ttObj.tip.outerHeight()/2 - ttObj.tip.find('.gtooltip-arrow').outerHeight()/2);
				
				ttObj.tip.find('.gtooltip-arrow, .gtooltip-arrow-border').css('border-right-width', '0px');
				ttObj.tip.find('.gtooltip-arrow').css('right', -1 * (ttObj.settings.arrow_size) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('right', -1 * (ttObj.settings.arrow_size + border_width + 1) + 'px');
				ttObj.tip.find('.gtooltip-arrow-border').css('border-left-width', (ttObj.settings.arrow_size + 1) + 'px');
			}
			ttObj.tip.css('top', $top);
			//set bottom css and unset top to account for any dynamic content changes
			if(ttObj.settings.position == 'top' && ttObj.settings.append == 'body'){
				ttObj.tip.css('bottom', $bottom);
				ttObj.tip.css('top', '');
			}
			//ttObj.tip.css('left', $(ttObj.element).outerWidth()/2 - 30 - 10); //element width - ttObj arrow left shift - arrow's border width
			ttObj.tip.css('left', $left);
			ttObj.tip.hide();
			
			return true;
		},
	};
}(jQuery));