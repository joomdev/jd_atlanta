jQuery.gloader = {
	'element': null,
	'url': '',
	'data': {},
	'loading_element': null,
	'load_button': null,
	'scroll_trigger': null,
	'add_method': 'append',
};

(function($){
	$.fn.gloader = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gloader, params);
				return this.each(function(){
					if(!$(this).data('gloader')){
						$(this).data('gloader', new gloader(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gloader, params);
				
				var loader = $(this).data('gloader');
				
				switch (options){
					case 'load':
						return loader.load();
				}
			}
		}
	}
	
	var gloader = function(elem, params){
		this.element = elem;
		this.settings = params;
		this.end_reached = 0;
		this.loader_running = 0;
		
		this.init();
	};
	
	gloader.prototype = {
		init: function(){
			var loader = this;
			
			if(loader.settings.load_button){
				loader.settings.load_button.on("click", function(e){
					e.preventDefault();
					loader.load();
				});
			}
			
			if(loader.settings.scroll_trigger){
				jQuery(window).scroll(function(){
					var loader_trigger = loader.settings.scroll_trigger.offset().top;
					var view_end = jQuery(window).scrollTop() + jQuery(window).height();
					var distance = loader_trigger - view_end;
					if(distance < 200){
						loader.load();
					}
				});
			}
		},
		
		load: function(){
			var loader = this;
			
			var data = loader.settings.data;
			if($.isFunction(data)){
				data = data.call(this);
			}
			
			if(!loader.end_reached && !loader.loader_running){
				$.ajax({
					"type" : "POST",
					"url" : loader.settings.url,
					"data" : data,
					beforeSend: function(){
						loader.loader_running = 1;
						if(loader.settings.loading_element){
							loader.settings.loading_element.css("display", "block");
						}
					},
					"success" : function(res){
						if($.trim(res) == ""){
							loader.end_reached = 1;
							if(loader.settings.load_button){
								loader.settings.load_button.prop("disabled", true).css("display", "none");
							}
						}else{
							if(loader.settings.add_method == 'append'){
								jQuery(loader.element).append(res);
							}else if(loader.settings.add_method == 'after'){
								jQuery(loader.element).after(res);
							}
						}
						
						loader.loader_running = 0;
						if(loader.settings.loading_element){
							loader.settings.loading_element.css("display", "none");
						}
					},
				});
				
				if(loader.end_reached){
					if(loader.settings.load_button){
						loader.settings.load_button.css("display", "none");
					}
				}
			}
		},
	};
}(jQuery));