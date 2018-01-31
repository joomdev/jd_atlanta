jQuery.gdatetimepicker = {
	format: 'd-m-Y',
	shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	shortDays: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
	shortDaysIndexes: [0, 1, 2, 3, 4, 5, 6],
	longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
};

(function($){
	function fix_number2(num){
		return ((num < 10) ? '0' : '') + num;
	};
	
	Date.prototype.format_date = function(format_string){
		var chars = {
			'Y': this.getFullYear(),
			'm': fix_number2(this.getMonth() + 1),
			'd': fix_number2(this.getDate()),
		};
		jQuery.each(chars, function(char, val){
			format_string = format_string.replace(char, val);
		});
		return format_string;
	}
		
	Date.prototype.parse_date = function(date, format_string){
		var formats = {
			'Ymd': /^(\d{4})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])$/,
			'Y-m-d': /^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/,
			'm-d-Y': /^(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])-(\d{4})$/,
			'm/d/Y': /^(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/(\d{4})$/,
			'd-m-Y': /^(0[1-9]|[12]\d|3[01])-(0[1-9]|1[0-2])-(\d{4})$/,
			'd.m.Y': /^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[0-2])\.(\d{4})$/,
			'd/m/Y': /^(0[1-9]|[12]\d|3[01])\/(0[1-9]|1[0-2])\/(\d{4})$/,
		};
		var order = {
			'Ymd': ['Y', 'm', 'd'],
			'Y-m-d': ['Y', 'm', 'd'],
			'm-d-Y': ['m', 'd', 'Y'],
			'm/d/Y': ['m', 'd', 'Y'],
			'd-m-Y': ['d', 'm', 'Y'],
			'd.m.Y': ['d', 'm', 'Y'],
			'd/m/Y': ['d', 'm', 'Y'],
		};
		if(!format_string){
			return [];
		}
		var result = {};
		if(parsed = date.match(formats[format_string])){
			jQuery.each(parsed, function(i, val){
				if(i){
					result[order[format_string][i - 1]] = val;
				}
			});
		}
		return result;
	}
	
	Date.prototype.strtotime = function(date, format_string){
		var parsed_date = {};
		parsed_date = Date.parse_date(date, format_string);
		var timestamp = (parsed_date.Y * 365 * 24 * 60 * 60) + (parsed_date.m * 24 * 60 * 60) + (parsed_date.d * 24 * 60 * 60);
		return timestamp;
	}

	$.fn.gdatetimepicker = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.gdatetimepicker, params);
				return this.each(function(){
					if(!$(this).data('gdatetimepicker') || $.type($(this).data('gdatetimepicker')) != 'object'){
						$(this).data('gdatetimepicker', new GDatetimepicker(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.gdatetimepicker, params);
				
				var picker = $(this).data('gdatetimepicker');
				
				switch (options){
					case 'show':
						return picker.show();
					case 'hide':
						return picker.hide();
					case 'destroy':
						return picker.destroy();
					case 'get':
						return picker.get();
				}
			}
		}
	}
	
	var GDatetimepicker = function(elem, params){
		this.element = elem;
		this.settings = params;
		
		this.format = $(this.element).data('format') ? $(this.element).data('format') : $.gdatetimepicker.format;
		this.shortMonths = $(this.element).data('shortmonths') ? $(this.element).data('shortmonths') : $.gdatetimepicker.shortMonths;
		this.shortDays = $(this.element).data('shortdays') ? $(this.element).data('shortdays') : $.gdatetimepicker.shortDays;
		this.shortDaysIndexes = $(this.element).data('shortdays_indexes') ? $(this.element).data('shortdays_indexes') : $.gdatetimepicker.shortDaysIndexes;
		
		this.start_date = $(this.element).data('start_date') ? $(this.element).data('start_date') : false;
		this.end_date = $(this.element).data('end_date') ? $(this.element).data('end_date') : false;
		this.open_days = $(this.element).data('open_days') ? $(this.element).data('open_days') : false;
		
		this.start_view = $(this.element).data('start_view') ? $(this.element).data('start_view') : 'd';
		
		this.shown = false;
		
		this.init();
	};
	
	GDatetimepicker.prototype = {
		init: function(){
			var picker = this;
			
			$(picker.element).on('focus', function(event){
				var the_month = new Date();
				var parsed_date = {'Y':the_month.getFullYear(), 'm':the_month.getMonth() + 1, 'd':the_month.getDate()};
				if($(picker.element).val()){
					var date = new Date();
					var parsed_date = date.parse_date($(picker.element).val(), picker.format);
				}
				
				if(picker.start_view == 'y'){
					var start_view = picker.display_decade(parsed_date.Y, parsed_date.m - 1, parsed_date.d);
				}else if(picker.start_view == 'm'){
					var start_view = picker.display_year(parsed_date.Y, parsed_date.m - 1, parsed_date.d);
				}else{
					var start_view = picker.display_month(parsed_date.Y, parsed_date.m - 1, parsed_date.d);
				}
				
				picker.start_date = $(picker.element).data('start_date') ? $(picker.element).data('start_date') : false;
				picker.end_date = $(picker.element).data('end_date') ? $(picker.element).data('end_date') : false;
				picker.open_days = $(picker.element).data('open_days') ? $(picker.element).data('open_days') : false;
				
				picker.create(start_view);
			});
			$(picker.element).on('keypress', function(){
				return false;
			});
			
			$(document).on('mousedown', function(e){
				if(picker.shown){
					if($(e.target).get(0) != $(picker.element).get(0) && $(e.target).get(0) != picker.box().get(0) && !$.contains(picker.box().get(0), $(e.target).get(0))){
						picker.hide();
					}
				}
			});
		},
		
		get: function(){
			var picker = this;
			
			return picker;
		},
		
		box: function(){
			var picker = this;
			
			return $(picker.element).gtooltip('get', {'tid':'dp'}).tip;
		},
		
		show: function(contents){
			var picker = this;
			
			$(picker.element).triggerHandler('show.gdatetimepicker');
			
			$(picker.element).data('content', contents);
			$(picker.element).gtooltip({'tipclass':'gtooltip gdatetimepicker-panel', 'tid':'dp', 'trigger':'manual', 'closable': 1, 'on_close': 'destroy'});
			$(picker.element).gtooltip('show', {'tid':'dp'});
			
			$(picker.element).on('close.gtooltip', function(){
				picker.shown = false;
			});
			
			picker.box().css('z-index', '5000');
			
			picker.shown = true;
		},

		hide: function(){
			var picker = this;
			
			$(picker.element).triggerHandler('hide.gdatetimepicker');
			
			$(picker.element).gtooltip('destroy', {'tid':'dp'});
			
			picker.shown = false;
		},
		
		create: function(contents){
			var picker = this;
			
			picker.show(contents);
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.day-item.selectable_date').on('click', function(){
				var date = new Date($(this).data('time'));
				$(picker.element).val(date.format_date(picker.format));
				$(picker.element).trigger('change');
				
				$(picker.element).triggerHandler('select_date.gdatetimepicker');
				
				picker.hide();
			});
			
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.date-nav-item.switch_month').on('click', function(){
				var month_data = picker.display_month($(this).data('year'), $(this).data('month'));
				picker.hide();
				picker.create(month_data);
			});
			
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.date-nav-item.select_month').on('click', function(){
				var year_data = picker.display_year($(this).data('year'), $(this).data('month'));
				picker.hide();
				picker.create(year_data);
			});
			
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.month-item.switch_month').on('click', function(){
				var month_data = picker.display_month($(this).data('year'), $(this).data('month'));
				picker.hide();
				picker.create(month_data);
			});
			
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.date-nav-item.switch_year').on('click', function(){
				var year_data = picker.display_year($(this).data('year'));
				picker.hide();
				picker.create(year_data);
			});
			
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.date-nav-item.switch_decade').on('click', function(){
				var decade_data = picker.display_decade($(this).data('year'));
				picker.hide();
				picker.create(decade_data);
			});
			
			$(picker.element).gtooltip('get', {'tid':'dp'}).tip.find('.year-item.switch_year').on('click', function(){
				var year_data = picker.display_year($(this).data('year'));
				picker.hide();
				picker.create(year_data);
			});
		},
		
		
		
		
		
		display_month: function (year, month, day){
			var picker = this;
			
			var the_month = new Date();
			if(typeof day == 'undefined'){
				the_month.setFullYear(year, month, 1);
			}else{
				the_month.setFullYear(year, month, day);
			}
			var days_header = picker.build_days_header(the_month);
			var days_list = picker.build_days_panel(the_month);
			return days_header + days_list;
		},
		
		display_year: function (year, month){
			var picker = this;
			
			var the_year = new Date();
			if(typeof month == 'undefined'){
				the_year.setFullYear(year, 0);
			}else{
				the_year.setFullYear(year, month);
			}
			var months_header = picker.build_months_header(the_year);
			var months_list = picker.build_months_panel(the_year);
			return months_header + months_list;
		},
		
		display_decade: function (year){
			var picker = this;
			
			var the_decade = new Date();
			if(typeof year == 'undefined'){
				//the_decade.setFullYear(year, 1);
			}else{
				the_decade.setFullYear(year);
			}
			var years_header = picker.build_years_header(the_decade);
			var years_list = picker.build_years_panel(the_decade);
			return years_header + years_list;
		},
		
		get_first_day: function (year, month){
			var picker = this;
			
			var the_day = new Date(year, month, 1, 0, 1, 0);
			var offset = the_day.getTimezoneOffset();
			return the_day.getDay();
		},
		get_month_length: function (year, month){
			var picker = this;
			
			var next_month = new Date(year, month + 1, 0);
			return next_month.getDate();
		},
		build_years_header: function (current_date){
			var picker = this;
			
			var years_header = '';
			var current_year = current_date.getFullYear();
			var decade_start = Math.floor(current_year/10) * 10;
			var decade_end = decade_start + 20;
			years_header = years_header  + '<div class="gcore-years-header">';

			years_header = years_header  + '<span class="date-nav-item date-nav-left switch_decade" data-year="'+(decade_start - 11)+'">&lsaquo;</span>';
			years_header = years_header  + '<span class="date-nav-item date-select">'+ decade_start + '-' + (decade_end - 1) +'</span>';

			years_header = years_header  + '<span class="date-nav-item date-nav-right switch_decade" data-year="'+(decade_end - 1)+'">&rsaquo;</span>';
			years_header = years_header + '</div>';
			return years_header;
		},
		build_years_panel: function (current_date){
			var picker = this;
			
			var years_list = '';
			years_list = years_list  + '<div class="gcore-years-picker">';
			var years_rows = [1,2,3,4];
			var current_year = current_date.getFullYear();
			var decade_start = Math.floor(current_year/10) * 10;
			var decade_end = decade_start + 20;
			jQuery.each(years_rows, function(i, row){
				var row_start = decade_start + (i * 5);
				years_list = years_list  + '<div class="years-row">';
				for(var year = row_start; year <= decade_end; year++){
					var active_class = '';
					if(current_date.getFullYear() == year){
						active_class = ' active_date';
					}
					if((year - decade_start) < 5 * row){
						years_list = years_list  + '<div class="year-item selectable_date switch_year'+ active_class +'" data-year="'+year+'">' + year + '</div>';
					}
				}
				years_list = years_list + '</div>';
			});
			years_list = years_list + '</div>';
			return years_list;
		},
		build_months_header: function (current_date){
			var picker = this;
			
			var months_header = '';
			months_header = months_header  + '<div class="gcore-months-header">';

			months_header = months_header  + '<span class="date-nav-item date-nav-left switch_year" data-year="'+(current_date.getFullYear() - 1)+'">&lsaquo;</span>';
			months_header = months_header  + '<span class="date-nav-item date-select switch_decade" data-year="'+(current_date.getFullYear())+'">'+ current_date.getFullYear() +'</span>';

			months_header = months_header  + '<span class="date-nav-item date-nav-right switch_year" data-year="'+(current_date.getFullYear() + 1)+'">&rsaquo;</span>';
			months_header = months_header + '</div>';
			return months_header;
		},
		build_months_panel: function (current_date){
			var picker = this;
			
			var months_list = '';
			months_list = months_list  + '<div class="gcore-months-picker">';
			var months_rows = [1,2,3,4];
			jQuery.each(months_rows, function(i, row){
				months_list = months_list  + '<div class="months-row">';
				jQuery.each(picker.shortMonths, function(k, month){
					var active_class = '';
					if(current_date.getMonth() == k){
						active_class = ' active_date';
					}
					if(k < row * (months_rows.length - 1) && k >= (row - 1) * (months_rows.length - 1)){
						months_list = months_list  + '<div class="month-item switch_month selectable_date'+ active_class +'" data-year="'+current_date.getFullYear()+'" data-month="'+k+'">' + month + '</div>';
					}
				});
				months_list = months_list + '</div>';
			});
			months_list = months_list + '</div>';
			return months_list;
		},
		build_days_header: function (current_date){
			var picker = this;
			
			var days_header = '';
			days_header = days_header  + '<div class="gcore-days-header">';
			var prev_month = new Date();
			prev_month.setFullYear(current_date.getFullYear(), current_date.getMonth() - 1, 1);
			days_header = days_header  + '<span class="date-nav-item date-nav-left switch_month" data-year="'+prev_month.getFullYear()+'" data-month="'+prev_month.getMonth()+'">&lsaquo;</span>';
			days_header = days_header  + '<span class="date-nav-item date-select select_month" data-year="'+current_date.getFullYear()+'" data-month="'+current_date.getMonth()+'">'+ picker.shortMonths[current_date.getMonth()] + ' ' + current_date.getFullYear() +'</span>';
			var next_month = new Date();
			next_month.setFullYear(current_date.getFullYear(), current_date.getMonth() + 1, 1);
			days_header = days_header  + '<span class="date-nav-item date-nav-right switch_month" data-year="'+next_month.getFullYear()+'" data-month="'+next_month.getMonth()+'">&rsaquo;</span>';
			days_header = days_header + '</div>';
			return days_header;
		},
		build_days_panel: function (current_date){
			var picker = this;
			
			var days_list = '';
			days_list = days_list  + '<div class="gcore-days-picker">';
			var days_rows = [1,2,3,4,5,6,7];
			var first_day = picker.get_first_day(current_date.getFullYear(), current_date.getMonth());

			var month_length = picker.get_month_length(current_date.getFullYear(), current_date.getMonth());
			var days_counter = 1;
			var next_days_counter = 1;
			var prev_days_counter = picker.get_month_length(current_date.getFullYear(), current_date.getMonth() - 1) - picker.shortDaysIndexes.indexOf(first_day) + 1;
			jQuery.each(days_rows, function(i, row){
				days_list = days_list  + '<div class="days-row">';
				jQuery.each(picker.shortDaysIndexes, function(ik, k){
					//k = parseInt(k.toString().replace('d', ''));
					var day = picker.shortDays[k];
					if(i == 0){
						days_list = days_list  + '<div class="day-title">' + day + '</div>';
					}else{
						var active_class = '';
						if(current_date.getDate() == days_counter){
							active_class = ' active_date';
						}
						if(i == 1){
							if(k == first_day || days_counter > 1){
								var this_date = new Date();
								this_date.setFullYear(current_date.getFullYear(), current_date.getMonth(), days_counter);
								var selecting_class = ' selectable_date' + active_class;
								var date_data = ' data-time="'+this_date.valueOf()+'"';
								
								if(picker.start_date){
									var start_date = new Date();
									var parsed_start_date = start_date.parse_date(picker.start_date, picker.format);
									start_date.setFullYear(parsed_start_date.Y, parsed_start_date.m - 1, parsed_start_date.d);
									if(start_date > this_date){
										selecting_class = ' disabled_date';
										date_data = '';
									}
								}
								
								if(picker.end_date){
									var end_date = new Date();
									var parsed_end_date = end_date.parse_date(picker.end_date, picker.format);
									end_date.setFullYear(parsed_end_date.Y, parsed_end_date.m - 1, parsed_end_date.d);
									if(end_date < this_date){
										selecting_class = ' disabled_date';
										date_data = '';
									}
								}
								
								if(picker.open_days){
									if(jQuery.inArray(this_date.getDay(), picker.open_days) == -1){
										selecting_class = ' disabled_date';
										date_data = '';
									}
								}
								
								days_list = days_list  + '<div class="day-item'+ selecting_class +'"' + date_data + '>' + days_counter + '</div>';
								days_counter = days_counter + 1;
							}else{
								//add last month days
								days_list = days_list  + '<div class="day-item disabled_date">' + prev_days_counter + '</div>';
								prev_days_counter = prev_days_counter + 1;
							}
						}else{
							if(days_counter > month_length){
								days_list = days_list  + '<div class="day-item disabled_date">' + next_days_counter + '</div>';
								next_days_counter = next_days_counter + 1;
							}else{
								var this_date = new Date();
								this_date.setFullYear(current_date.getFullYear(), current_date.getMonth(), days_counter);
								var selecting_class = ' selectable_date' + active_class;
								var date_data = ' data-time="'+this_date.valueOf()+'"';
								
								if(picker.start_date){
									var start_date = new Date();
									var parsed_start_date = start_date.parse_date(picker.start_date, picker.format);
									start_date.setFullYear(parsed_start_date.Y, parsed_start_date.m - 1, parsed_start_date.d);
									if(start_date > this_date){
										selecting_class = ' disabled_date';
										date_data = '';
									}
								}
								
								if(picker.end_date){
									var end_date = new Date();
									var parsed_end_date = end_date.parse_date(picker.end_date, picker.format);
									end_date.setFullYear(parsed_end_date.Y, parsed_end_date.m - 1, parsed_end_date.d);
									if(end_date < this_date){
										selecting_class = ' disabled_date';
										date_data = '';
									}
								}
								
								if(picker.open_days){
									if(jQuery.inArray(this_date.getDay(), picker.open_days) == -1){
										selecting_class = ' disabled_date';
										date_data = '';
									}
								}
								
								days_list = days_list  + '<div class="day-item'+ selecting_class +'"' + date_data + '>' + days_counter + '</div>';
								days_counter = days_counter + 1;
							}
						}
					}
				});
				days_list = days_list + '</div>';
			});
			days_list = days_list + '</div>';
			return days_list;
		},
		
		
	}

}(jQuery));