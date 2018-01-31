jQuery.gdropdown = {
	
};

(function($){
	$.fn.gdropdown = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gdropdown, params);
				return this.each(function(){
					if(!$(this).data('gdropdown')){
						$(this).data('gdropdown', new Gdropdown(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gdropdown, params);
				
				var dropdown = $(this).data('gdropdown');
				
				switch (options){
					case 'get':
						return dropdown.get();
					case 'show':
						return dropdown.show();
					case 'hide':
						return dropdown.hide();
					case 'toggle':
						return dropdown.toggle();
				}
			}
		}
	}
	
	var Gdropdown = function(elem, params){
		this.element = elem;
		this.settings = params;
		
		this.shown = false;
		
		this.init();
	};
	
	Gdropdown.prototype = {
		init: function(){
			var dropdown = this;
			$(dropdown.element).addClass('gdropdown');
			$(dropdown.element).css({
				'background-clip' : 'padding-box',
				'display' : 'none',
				'float' : 'left',
				'left' : '0',
				'list-style' : 'outside none none',
				'position' : 'absolute',
				'top' : '100%',
				'z-index' : '1000',
			});
			
			$(dropdown.element).find('a').on('click', function(){
				dropdown.hide();
			});
			
			$(document).on('mousedown', function(e){
				if(dropdown.shown){
					if($(e.target).get(0) != $(dropdown.element).get(0) && !$.contains($(dropdown.element).get(0), $(e.target).get(0))){
						dropdown.hide();
					}
				}
			});
		},
		
		get: function(){
			var dropdown = this;
			return dropdown;
		},
		
		show: function(){
			var dropdown = this;
			$(dropdown.element).trigger('show.gdropdown');
			$(dropdown.element).show();
			$(dropdown.element).trigger('shown.gdropdown');
			dropdown.shown = true;
		},
		
		hide: function(){
			var dropdown = this;
			$(dropdown.element).trigger('hide.gdropdown');
			$(dropdown.element).hide();
			$(dropdown.element).trigger('hidden.gdropdown');
			dropdown.shown = false;
		},
		
		toggle: function(){
			var dropdown = this;
			
			if(dropdown.shown){
				dropdown.hide();
			}else{
				dropdown.show();
			}
			
			//$(dropdown.element).toggle();
		},
		
	};
}(jQuery));