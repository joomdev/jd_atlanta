jQuery.guploader = {
	'url': '',
	'data': {},
};

(function($){
	$.fn.guploader = function(options, params){
		if(this.length > 0){
			if($.type(params) === 'undefined' && $.type(options) === 'object'){
				params = options;
			}
			
			if($.type(options) === 'undefined' || $.type(options) === 'object'){
				params = $.extend(true, {}, $.guploader, params);
				return this.each(function(){
					if(!$(this).data('guploader')){
						$(this).data('guploader', new guploader(this, params));
					}
				});
			}
			
			if($.type(options) === 'string'){
				params = $.extend(true, {}, $.guploader, params);
				
				var loader = $(this).data('guploader');
				
				switch (options){
					case 'load':
						return loader.load();
				}
			}
		}
	}
	
	var guploader = function(elem, params){
		this.element = elem;
		this.settings = params;
		this.end_reached = 0;
		this.loader_running = 0;
		
		this.init();
	};
	
	guploader.prototype = {
		init: function(){
			var loader = this;
			
			$(loader.element).on('change', function(){
				var formData = new FormData();
				for(var i = 0; i < $(loader.element).get(0).files.length; i++){
					formData.append($(loader.element).attr('name'), $(loader.element).get(0).files[i]);
				}
				$.each(loader.settings.data, function(i, v){
					formData.append(i, v);
				});
				$.ajax({
					url: loader.settings.url,
					type: 'POST',
					xhr: function() {// Custom XMLHttpRequest
						var myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload){// Check if upload property exists
							myXhr.upload.addEventListener('progress', function (e){
								if(e.lengthComputable){
									if($(loader.element).next('progress.progress-block').length == 0){
										$(loader.element).after($('<progress></progress>').addClass('progress-block'));
									}
									$(loader.element).next('progress.progress-block').attr({value:e.loaded,max:e.total});
								}
							}, false);// For handling the progress of the upload
						}
						return myXhr;
					},
					//Ajax events
					beforeSend: function(xh, settings){
						$(loader.element).trigger('beforeSend.guploader');
					},
					success: function(data, status, xhr){
						//var results = data.split("\n");
						//var results = $.parseJSON(data);
						/*if(results['status'] == 'ok'){
							console.log(results);
						}*/
						$(loader.element).trigger('success.guploader', [data]);
					},
					error: function(xhr, status, error){
						$(loader.element).trigger('error.guploader');
					},
					complete: function(xhr, status){
						$(loader.element).trigger('complete.guploader');
					},
					// Form data
					data: formData,
					//Options to tell jQuery not to process data or worry about content-type.
					cache: false,
					contentType: false,
					processData: false
				});
			});
		},
		
	};
}(jQuery));