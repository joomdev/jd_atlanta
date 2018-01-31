jQuery.gmodal = {
	'closeOnBGClick':0,
	'overlay_color':'#000',
	'close_selector':'*[data-close="gmodal"]',
};

(function($){
	$.fn.gmodal = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gmodal, params);
				return this.each(function(){
					$(this).data('gmodal', new GModal(this, params));
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gmodal, params);
				
				var modal = $(this).data('gmodal');
				
				switch (options){
					case 'open':
						return modal.open();
					case 'close':
						return modal.close();
					case 'get':
						return modal.get();
				}
			}
		}
	}
	
	var GModal = function(elem, params){
		this.element = elem;
		this.settings = params;
		this.opened = false;
		//this.bg = null;
		this.element_css = {
			'position': '',
			'top': '',
			'left': '',
			'right': '',
			'bottom': '',
			'width': '',
			'display': '',
			'z-index': '',
			'overflow-x': '',
			'overflow-y': '',
		};
		
		this.init();
	};
	
	GModal.prototype = {
		init: function(){
			var modal = this;
			$(modal.element).find(modal.settings.close_selector).on('click', function(e){
				e.preventDefault();
				modal.close();
			});
		},
		
		get: function(){
			var modal = this;
			return modal;
		},
		
		pose: function(){
			var modal = this;
			/*var poser = $('<div class="gmodal-poser"></div>').css({
				'position': 'absolute',
				'top': '0',
				'left': '0',
				'width': '100%',
				'display': 'block',
			});*/
			//store original CSS
			$.each(modal.element_css, function(p, v){
				modal.element_css[p] = $(modal.element).css(p);
			});
			
			if(modal.settings.closeOnBGClick){
				modal.bg.on('click', function(){
					modal.close();
				});
			}
			
			$(modal.element).css({
				'position': 'fixed',
				'top': '0',
				'left': '0',
				'right': '0',
				'bottom': '0',
				'opacity': '1',
				'width': '100%',
				'display': 'block',
				'z-index': '2222',
				'overflow-x': 'auto',
				'overflow-y': 'scroll',
			});
		},
		
		open: function(){
			var modal = this;
			if(modal.opened){
				return false;
			}
			$(modal.element).trigger('open.gmodal');
			modal.overlay();
			modal.pose();
			modal.opened = true;
			$(modal.element).addClass('gmodal-open');
			$(modal.element).trigger('opened.gmodal');
		},
		
		close: function(){
			var modal = this;
			$(modal.element).trigger('close.gmodal');
			modal.bg.remove();
			$(modal.element).css(modal.element_css);
			modal.opened = false;
			$(modal.element).removeClass('gmodal-open');
			$(modal.element).trigger('closed.gmodal');
		},
		
		overlay: function(){
			var modal = this;
			if($.type(modal.bg) !== 'undefined' && $.contains(document, modal.bg[0])){
				//overlay already exists and we don't need to create a new one
				return;
			}
			var overlay = $('<div class="gmodal-overlay dark"></div>');
			overlay.css({
				'position': 'fixed',
				'top': '0',
				'right': '0',
				'bottom': '0',
				'left': '0',
				'z-index': '1111',
				'background-color': modal.settings.overlay_color,
				'opacity': '0.8',
				'filter': 'alpha(opacity=80)',
			});
			$('body').append(overlay);
			
			modal.bg = overlay;
		},
	};
}(jQuery));