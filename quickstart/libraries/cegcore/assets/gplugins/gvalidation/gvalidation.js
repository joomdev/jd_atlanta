jQuery.gvalidation = {
	rules : {
		required : /[^.*]/,
		alpha : /^[a-z ._-]+$/i,
		alphanum : /^[a-z0-9 ._-]+$/i,
		digit : /^[-+]?[0-9]+$/,
		nodigit : /^[^0-9]+$/,
		nospace : /^[^ ]+$/,
		number : /^[-+]?\d*\.?\d+$/,
		email : /^([a-zA-Z0-9_\.\-\+%])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
		image : /.(jpg|jpeg|png|gif|bmp)$/i,
		phone : /^\+{0,1}[0-9 \(\)\.\-]+$/, // alternate regex : /^[\d\s ().-]+$/,/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/
		phone_inter : /^\+{0,1}[0-9 \(\)\.\-]+$/,
		url : /^(http|https|ftp)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,3}(:[a-z0-9]*)?\/?([a-z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i
	},
	errors : {
		required : 'This field is required.',
		alpha : 'This field accepts alphabetic characters only.',
		alphanum : 'This field accepts alphanumeric characters only.',
		digit : 'Please enter a valid integer.',
		nodigit : 'No digits are accepted.',
		nospace : 'No spaces are accepted.',
		number : 'Please enter a valid number.',
		email : 'Please enter a valid email.',
		image : 'This field should only contain image types',
		phone : 'Please enter a valid phone.',
		phone_inter : 'Please enter a valid international phone number.',
		url : 'Please enter a valid url.',
		group: 'Please make at least %1 selection(s).',
		confirm: 'Please make sure that the value matches the %1 field value.',
		custom: 'The value entered is not valid.',
	},
	display : 'tooltip',
	'css':{
		'background-color':'#ff4242',
		'border-color':'#ff0000',
		'padding':'4px',
	},
};
(function($){
	$.fn.gvalidate = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend({}, $.gvalidation, params);
				return this.each(function(){
					if(!$(this).data('gvalidation')){
						$(this).data('gvalidation', new GValidation(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend({}, $.gvalidation, params);
				var gval = $(this).data('gvalidation');
				
				if(typeof gval == 'undefined' || !gval){
					return null;
				}
				
				switch (options){
					case 'get':
						return gval.get();
				}
			}
		}
	}
	
	var GValidation = function(elem, params){
		this.element = elem;
		this.settings = params;
		this.rules = [];
		this.errors = {};
		this.invalid_rule = '';
		this.error_shown = false;
		this.disabled = false;
		
		this.init();
	};
	
	GValidation.prototype = {
		init: function(){
			var gval = this;
			if($(gval.element).is(':input')){
				gval.validate(false);
				//validate the field when some events occur
				$(gval.element).on('change', function(){
					gval.validate(true);
				});
				$(gval.element).on('blur', function(){
					gval.validate(true);
				});
			}else{
				//check on form submit
				if($(gval.element).prop('tagName') == 'FORM'){
					/*$(gval.element).find(':input').on('blur', function(){
						$(this).gvalidate();
					});
					$(gval.element).find(':input').on('change', function(){
						$(this).gvalidate();
					});*/
					$(gval.element).find(':input').each(function(i, inp){
						$(inp).gvalidate();
					});
					$(gval.element).on('submit', function(e){
						//gval.init();
						$(gval.element).find(':input').each(function(i, inp){
							$(inp).gvalidate();
						});
						
						if(gval.validate_area() == true){
							$(gval.element).trigger('success.gvalidation');
							return true;
						}else{
							e.stopImmediatePropagation();
							$(gval.element).trigger('fail.gvalidation');
							return false;
						}
					});
					$(gval.element).on('reset', function(e){
						setTimeout(function(){
							$(gval.element).find(':input').trigger('change');
						}, 500);
					});
				}else{
					return gval.validate_area();
				}
			}
		},
		
		validate_area: function(){
			var gval = this;
			var focus_gval = null;
			$(gval.element).find(':input').each(function(i, inp){
				$(inp).gvalidate();
				if(focus_gval == null){
					var gval_inp = $(inp).gvalidate('get');
					if(!gval_inp.validate()){
						focus_gval = gval_inp;
						return false;//break
					}
				}
			});
			if(focus_gval != null){
				$(focus_gval.element).focus();
				$('html, body').animate({
					scrollTop: $(focus_gval.element).offset().top - 100
				}, 300);
				focus_gval.show_error();
				return false;
			}
			return true;
		},
		
		get: function(){
			var gval = this;
			
			return gval;
		},
		
		reset: function(){
			var gval = this;
			
			$(gval.element).data('gvalidation', null);
		},
		
		validate: function(showError){
			var gval = this;
			gval.inspect();
			
			var result = gval.check();
			if(result){
				if(gval.rule_name(gval.invalid_rule) == 'group'){
					$('input[class*="'+gval.invalid_rule+'"]').each(function(i, chk){
						var gval_other = $(chk).gvalidate('get');
						if(gval_other != null){
							gval_other.remove_error();
						}
					});
				}else if(gval.rules.length && gval.rule_name(gval.rules[0]) == 'group'){
					$('input[class*="'+gval.rules[0]+'"]').each(function(i, chk){
						var gval_other = $(chk).gvalidate('get');
						if(gval_other != null){
							gval_other.remove_error();
						}
					});
				}else{
					gval.remove_error();
				}
			}else{
				if(gval.rule_name(gval.invalid_rule) == 'group'){
					$('input[class*="'+gval.invalid_rule+'"]').each(function(i, chk){
						var gval_other = $(chk).gvalidate('get');
						if(gval_other != null){
							gval_other.check();
						}
					});
				}
				if(showError == true){
					gval.show_error();
				}
			}
			return result;
		},
		
		inspect: function(){
			var gval = this;
			var validate_matches = $(gval.element).prop('class').match(/validate\[(.*?)\]/g);
			if(validate_matches){
				$.each(validate_matches, function(vm, validate_match){
					var matches = validate_match.match(/validate\[(.*?)\]/);
					if(matches && typeof matches[1] != 'undefined'){
						var rules = matches[1].split(',');
						var clean_rules = [];
						$.each(rules, function(i, rule){
							rule = rule.replace(/("|')/g, '');
							clean_rules.push(rule);
						});
						gval.set_rules(clean_rules);
					}
				});
			}
		},
		
		check: function(){
			var gval = this;
			var toReturn = true;
			$(gval.element).trigger('check.gvalidation');
			
			$.each(gval.rules, function(i, rule){
				if(!gval.check_rule(rule)){
					gval.invalid_rule = rule;
					toReturn = false;
					return false;//break
				}
			});
			
			if(toReturn == false){
				$(gval.element).trigger('invalid.gvalidation');
			}
			
			if(gval.disabled == true){
				return true;
			}
			if($(gval.element).is(':hidden')){
				return true;
			}
			
			return toReturn;
		},
		
		set_rules: function(rules){
			var gval = this;
			gval.rules = rules;
		},
		
		check_rule: function(rule){
			var gval = this;
			if($(gval.element).prop('disabled')){
				return true;
			}
			var $type = $(gval.element).prop('type');
			var $rule_parts = rule.split(':');
			if($.gvalidation.rules.hasOwnProperty($rule_parts[0])){
				if($.inArray($type, ['checkbox','radio']) > -1){
					return $(gval.element).prop('checked');
				}else{
					if($rule_parts[0] == 'required'){
						if($(gval.element).val() == null){
							return false;//for multi select with nothing selected
						}else if($.isArray($(gval.element).val())){
							if($(gval.element).val().length == 0){
								return false;//for multi select
							}else{
								return true;
							}
						}else{
							return $(gval.element).val().trim().match($.gvalidation.rules[$rule_parts[0]]);
						}
					}else{
						return (!$(gval.element).val().trim() || $(gval.element).val().trim().match($.gvalidation.rules[$rule_parts[0]]));
					}
				}
			}else{
				if($rule_parts[0] == 'group'){
					var count = 0;
					$('input[class*="'+rule+'"]').each(function(i, chk){
						if($(chk).prop('checked')){
							count = count + 1;
						}
					});
					var limit = ($rule_parts[2] ? $rule_parts[2] : 1);
					return (count >= limit);
				}else if($rule_parts[0] == 'confirm'){
					return ($('#'+$rule_parts[1]).val() == $(gval.element).val());
				}else if($rule_parts[0] == 'custom'){
					var fn = $rule_parts[1];
					if(fn in window){
						return window[fn]($(gval.element));
					}
					return true;
				}
			}
		},
		
		show_error: function(){
			var gval = this;
			
			if(!gval.invalid_rule){
				return;
			}
			
			var $rule_parts = gval.invalid_rule.split(':');
			rule = $rule_parts[0];
			if(typeof gval.errors[rule] == 'undefined'){
				if($(gval.element).prop('title')){
					gval.errors[rule] = $(gval.element).prop('title');
				}else{
					var error_string = $.gvalidation.errors[rule];
					if(rule == "group"){
						var skip = false;
						$('input[class*="'+gval.invalid_rule+'"]').each(function(i, chk){
							var gval_other = $(chk).gvalidate('get');
							var another_error_shown = (gval_other == null ? false : gval_other.error_shown);
							if(another_error_shown !== false){
								skip = true;
								return false;
							}
						});
						if(skip){
							return;
						}
					}
					if(rule == "group"){
						if(typeof $rule_parts[2] == 'undefined'){
							//dirty fix for the group validtaions
							$rule_parts[1] = 1;
						}
					}
					$.each($rule_parts, function(i, val){
						error_string = error_string.replace('%'+i, val);
					});
					gval.errors[rule] = error_string;
				}
			}
			
			gval.display_error();
			gval.error_shown = true;
		},
		
		display_error: function(){
			var gval = this;
			
			$(gval.element).trigger('display.gvalidation');
			
			var error_target = $(gval.element);
			if($(gval.element).data('gvalidation-target')){
				var error_target = $(gval.element).data('gvalidation-target');
			}
			
			if($.gvalidation.display == 'tooltip'){
				error_target.data('content', '<span class="gvalidation-error-text">'+gval.errors[gval.rule_name(gval.invalid_rule)]+'</span>');
				error_target.gtooltip({'tipclass':'gtooltip gvalidation-error-tip', 'closable': 1, 'tid':'gval', 'trigger':'manual', 'css':gval.settings.css});
				error_target.gtooltip('reset', {'tid':'gval'});
				error_target.gtooltip('show', {'tid':'gval'});
			}else{
				//$(gval.element).parent().after($($(gval.element).data('content')).css('display', 'block'));
			}
		},
		
		remove_error: function(){
			var gval = this;
			
			if(!gval.invalid_rule){
				return;
			}
			
			gval.errors = {};
			gval.error_shown = false;
			gval.invalid_rule = '';
			
			if($.gvalidation.display == 'tooltip'){
				$(gval.element).gtooltip('destroy', {'tid':'gval'});
			}else{
				
			}
		},
		
		disable: function(){
			var gval = this;
			
			gval.disabled = true;
		},
		
		enable: function(){
			var gval = this;
			
			gval.disabled = false;
		},
		
		rule_name: function(rule){
			var $rule_parts = rule.split(':');
			return $rule_parts[0];
		},
		
		rule_params: function(rule){
			var $rule_parts = rule.split(':');
			$rule_parts.splice(0, 1);
			return $rule_parts;
		},
		
	};
}(jQuery));