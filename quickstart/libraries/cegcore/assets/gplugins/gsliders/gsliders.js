jQuery.gsliders = {
	'pane_selector':'.gpane',
	'tab_selector':'.ghandler',
	'active_pane_class':'expand',
	'active_tab_class':'active',
};

(function($){
	$.fn.gsliders = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gsliders, params);
				return this.each(function(){
					if(!$(this).data('gsliders')){
						$(this).data('gsliders', new GSliders(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gsliders, params);
				
				var sliders = $(this).data('gsliders');
				
				switch (options){
					case 'open':
						return sliders.open();
					case 'close':
						return sliders.close();
					case 'get':
						return sliders.get();
				}
			}
		}
	}
	
	var GSliders = function(elem, params){
		this.element = elem;
		this.settings = params;
		
		this.init();
	};
	
	GSliders.prototype = {
		init: function(){
			var sliders = this;
			//hide pans without the active class
			$(sliders.element).find(sliders.settings.pane_selector).not('.'+sliders.settings.active_pane_class).slideUp();
			$(sliders.element).find(sliders.settings.pane_selector).filter('.'+sliders.settings.active_pane_class).slideDown();
			
			$(sliders.element).on('click', sliders.settings.tab_selector, function(e){
				var $a = $(this);
				e.preventDefault();
				//check target
				if($a.data('target')){
					var target = $a.data('target');
				}else{
					var target = $a.attr('href');
				}
				//activate pane
				$(sliders.element).find(sliders.settings.pane_selector).slideUp();
				$(sliders.element).find(sliders.settings.pane_selector).removeClass(sliders.settings.active_pane_class);
				$(sliders.settings.pane_selector + target).slideDown();
				$(sliders.settings.pane_selector + target).addClass(sliders.settings.active_pane_class);
			});
		},
		
		get: function(){
			var sliders = this;
			return sliders;
		},
		
		show: function(slider_link){
			var sliders = this;
			slider_link.trigger('click');
		},
	};
}(jQuery));