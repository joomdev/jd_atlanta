jQuery.gcompleter = {
	'height' : '250px',
	'placeholder' : '',
	'label' : '',
	'minimumInputLength' : 2,
};

(function($){
	$.fn.gcompleter = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gcompleter, params);
				return this.each(function(){
					if(!$(this).data('gcompleter')){
						$(this).data('gcompleter', new Gcompleter(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gcompleter, params);
				
				var completer = $(this).data('gcompleter');
				
				switch (options){
					case 'get':
						return completer.get();
					case 'show':
						return completer.show();
					case 'hide':
						return completer.hide();
					case 'toggle':
						return completer.toggle();
				}
			}
		}
	}
	
	var Gcompleter = function(elem, params){
		this.element = elem;
		this.settings = params;
		
		this.vals = [];
		this.options = [];
		this.term = '';
		this.multi = false;
		
		this.div = null;
		this.display = null;
		this.arrow = null;
		this.dropdown = null;
		this.input = null;
		this.label = null;
		this.fields = null;
		
		this.init();
	};
	
	Gcompleter.prototype = {
		init: function(){
			var completer = this;
			//set initial options
			if($(completer.element).prop('tagName') == 'SELECT'){
				$(completer.element).children('option').each(function(opt){
					completer.options.push({'value' : $(this).val(), 'text' : $(this).text()});
				});
				
				var value = $(completer.element).val();
				var text = $(completer.element).find('option:selected').text();
			}else if($(completer.element).data('options')){
				completer.options = $(completer.element).data('options');
			}else{
				//empty, wait for results
				
			}
			//hide main element
			$(completer.element).hide();
			//initalize the completer div
			completer.initDiv();
			//initalize the completer dropdown
			completer.initDropdown();
			
			$(completer.element).after(completer.div);
			
			$(completer.element).on('term.gcompleter', function(){
				completer.updateLabel();
				completer.checkDynamic();
				completer.updateList();
			});
			//set value
			completer.setVal(value, text);
			//set placeholder
			completer.setPlaceholder();
		},
		
		checkDynamic: function(){
			var completer = this;
			
			if(completer.settings.ajax){
				if(completer.settings.minimumInputLength <= completer.term.length){
					var ajax_object = {
						"type" : "GET",
						"data" : {},
						"cache" : false,
						"async": false,
						"beforeSend" : function(res){
							completer.label.addClass('gcompleter-ajax-loading');
						},
						"success" : function(res){
							completer.options = [];
							$.each($.parseJSON(res), function(id, val){
								completer.options.push(val);
							});
							completer.label.removeClass('gcompleter-ajax-loading');
						},
					};
					ajax_object = $.extend(true, {}, ajax_object, completer.settings.ajax);
					ajax_object.data[ajax_object.term] = completer.term;
					$.ajax($.extend(true, {}, ajax_object, completer.settings.ajax));
				}
			}
		},
		
		initDiv: function(){
			var completer = this;
			
			//create the completer element
			var div = completer.createDiv();
			completer.div = div;
			completer.display = div.children('.gcompleter-text').first();
			completer.arrow = div.children('.gcompleter-arrow');
			completer.fields = div.children('.gcompleter-fields').first();
			
			/*completer.div.on('focusout', function(){
				completer.dropdown.gdropdown('hide');
			});*/
		},
		
		initDropdown: function(){
			var completer = this;
			
			var dropdown = completer.createDropdown();
			completer.dropdown = dropdown;
			completer.input = completer.dropdown.children('.gcompleter-input').first();
			completer.label = completer.dropdown.children('.gcompleter-label').first();
			
			completer.div.find('.gdropdown').remove();
			
			completer.div.append(dropdown);
			
			completer.dropdown.gdropdown();
			/*if(opened == true){
				completer.dropdown.gdropdown('show');
			}*/
			completer.dropdown.on('show.gdropdown', function(){
				completer.updateList();
			});
			completer.dropdown.on('shown.gdropdown', function(){
				completer.input.focus();
			});
			
			if(completer.arrow){
				completer.dropdown.on('show.gdropdown', function(){
					completer.arrow.html('&#x25B4');
				});
				completer.dropdown.on('hide.gdropdown', function(){
					completer.arrow.html('&#x25BE');
				});
			}
			
			completer.div.find('.gcompleter-text, .gcompleter-arrow').on('click', function(){
				completer.dropdown.gdropdown('toggle');
			});
		},
		
		updateList: function(){
			var completer = this;
			
			var newList = completer.createList();
			completer.dropdown.find('.gcompleter-list').remove();
			completer.dropdown.append(newList);
		},
		
		setVal: function(value, text){
			var completer = this;
			
			if(value != null){
				if(completer.multi == true){
					completer.vals.push({'value' : value, 'text' : text});
				}else{
					completer.vals[0] = {'value' : value, 'text' : text};
				}
				//set display
				completer.display.html(text);
				//add hidden fields
				completer.fields.empty();
				completer.vals.each(function(v, k){
					var field = $('<input type="hidden" name="'+ $(completer.element).prop('name') +'" value="'+ v.value +'" />');
					completer.fields.append(field);
				});
			}
		},
		
		setPlaceholder: function(){
			var completer = this;
			
			if(completer.settings.placeholder && completer.vals.length == 0){
				completer.display.html(completer.settings.placeholder);
			}
		},
		
		get: function(){
			var completer = this;
			return completer;
		},
		
		createDiv: function(){
			var completer = this;
			var $div = $('<div class="gcompleter"></div>');
			//var props = ['float', 'position', 'width', 'height', ];
			$div.html('<div class="gcompleter-text">'+ completer.settings.placeholder +'</div><div class="gcompleter-arrow">&#x25BE</div><div class="gcompleter-fields"></div>');
			
			var arrow_width = 18;
			var arrow_height = $(completer.element).outerHeight() - parseInt($(completer.element).css('border-top-width')) - parseInt($(completer.element).css('border-bottom-width'));
			$div.children('.gcompleter-arrow').css('width', arrow_width);
			$div.children('.gcompleter-arrow').css('min-width', arrow_width);
			$div.children('.gcompleter-arrow').css('height', arrow_height);
			
			var completer_width = $(completer.element).outerWidth();
			var completer_height = $(completer.element).outerHeight();
			$div.css('width', completer_width);
			$div.css('max-width', completer_width);
			$div.css('height', completer_height);
			
			$div.children('.gcompleter-text').css('padding-top', $(completer.element).css('padding-top'));
			$div.children('.gcompleter-text').css('padding-bottom', $(completer.element).css('padding-bottom'));
			$div.children('.gcompleter-text').css('padding-left', $(completer.element).css('padding-left'));
			$div.children('.gcompleter-text').css('padding-right', $(completer.element).css('padding-right'));
			
			var display_width = completer_width - 18 - parseInt($div.css('border-left-width')) - parseInt($div.css('border-right-width'));
			var display_height = $(completer.element).outerHeight() - parseInt($(completer.element).css('padding-top')) - parseInt($(completer.element).css('padding-bottom')) - parseInt($div.css('border-top-width')) - parseInt($div.css('border-bottom-width'));
			$div.children('.gcompleter-text').css('width', display_width);
			$div.children('.gcompleter-text').css('max-width', display_width);
			$div.children('.gcompleter-text').css('height', display_height);
			
			
			$div.children().css('line-height', $div.children('.gcompleter-text').height() + 'px');
			
			return $div;
		},
		
		createList: function(){
			var completer = this;
			var $ul = $('<ul class="gcompleter-list"></ul>');
			completer.options.each(function(opt, n){
				if(completer.term != ''){
					if(opt.text.toLowerCase().indexOf(completer.term) < 0){
						return;
					}
				}
				var $li = $('<li><a href="#" data-value="'+opt.value+'" data-text="'+opt.text+'">'+ opt.text +'</a></li>');
				completer.vals.each(function(v, k){
					if(opt.value == v.value){
						$li.addClass('active');
						return false;
					}
				});
				/*if($.inArray(opt.value, completer.vals) > -1){
					$li.addClass('active');
				}*/
				$ul.append($li);
			});
			$ul.find('li').children('a').on('click', function(e){
				e.preventDefault();
				var $a = $(this);
				//completer.display.html($a.data('text'));
				completer.dropdown.gdropdown('hide');
				completer.setVal($a.data('value'), $a.data('text'));
			});
			if(completer.settings.height){
				$ul.css('max-height', completer.settings.height);
			}
			
			return $ul;
		},
		
		createDropdown: function(){
			var completer = this;
			var $dd = $('<div class="gdropdown"></div>');
			$dd.css('border-radius', '0px');
			$dd.css('min-width', completer.div.width() - parseInt(completer.div.css('border-left-width')) - parseInt(completer.div.css('border-right-width')));
			$dd.css('width', 'auto');
			
			$dd.append(completer.createInput());
			$dd.append(completer.createLabel());
			$dd.append(completer.createList());
			return $dd;
		},
		
		createInput: function(){
			var completer = this;
			var $input = $('<input type="text" class="gcompleter-input" />');
			
			//$input.css('padding', '0px');
			$input.css('margin', '2% 4%');
			$input.css('width', '92%');
			$input.css('height', 'auto');
			
			$input.on('keyup', function(){
				completer.term = $input.val();
				$(completer.element).trigger('term.gcompleter');
			});
			
			return $input;
		},
		
		createLabel: function(){
			var completer = this;
			var $label = $('<label class="gcompleter-label">'+ completer.settings.label +'</label>');
			
			if(completer.settings.label){
				if(completer.settings.minimumInputLength > completer.term.length){
					$label.html(completer.settings.label.replace('%s', completer.settings.minimumInputLength - completer.term.length));
				}
			}else{
				$label.hide();
			}
			
			$label.css('margin', '2% 4%');
			$label.css('width', '92%');
			$label.css('height', 'auto');
			
			return $label;
		},
		
		updateLabel: function(content){
			var completer = this;
			
			if(completer.settings.label){
				if(completer.settings.minimumInputLength > completer.term.length){
					completer.label.show();
					completer.label.html(completer.settings.label.replace('%s', completer.settings.minimumInputLength - completer.term.length));
					return;
				}
			}
			
			if(content){
				completer.label.show();
				completer.label.html(content);
				return;
			}
			completer.label.html('');
			completer.label.hide();
		},
		
		show: function(){
			var completer = this;
			$(completer.element).show();
		},
		
		hide: function(){
			var completer = this;
			$(completer.element).hide();
		},
		
		toggle: function(){
			var completer = this;
			$(completer.element).toggle();
		},
		
	};
}(jQuery));