(function($){
	$.fn.geditor = function(options, params){
		if(options === Object(options)){
			params = options;
		}
		
		this.each(function(){
			$(this).data('geditor', new GEditor(this, params));
			//inject the div in dom
			//$(this).after(editorDiv);
		});
	}
	
	var GEditor = function(elem, params){
		this.textarea = elem;
		this.div = null;
		this.body = null;
		this.header = null;
		
		this.selection = null;
		this.range = null;
		this.selection_start = false;
		
		this.settings = $.extend({
			div_class : 'panel panel-default geditor-div',
			header_class : 'geditor-header',
			body_class : 'geditor-body',
			buttons : {
				'bold' : {'html' : '', 'btnClass' : 'geditor-btn fa fa-bold', 'data' : {'func' : 'wrap', 'start' : '<strong>', 'end' : '</strong>'}},
				'italic' : {'html' : '', 'btnClass' : 'geditor-btn fa fa-italic', 'data' : {'func' : 'wrap', 'start' : '<em>', 'end' : '</em>'}},
				'strike' : {'html' : '', 'btnClass' : 'geditor-btn fa fa-strikethrough', 'data' : {'func' : 'wrap', 'start' : '<del>', 'end' : '</del>'}},
				'paragraph' : {'html' : '&nbsp;<i class="fa fa-angle-up"></i>', 'btnClass' : 'geditor-btn fa fa-text-height', 'css':{'display':'block'}, 'data' : {'func' : 'dropdown', 'buttons' : {
					'small' : {'html' : '<font style="font-size:0.8em;">ABCDEF</font>', 'btnClass' : 'geditor-btn', 'data' : {'func' : 'wrap', 'start' : '<font style="font-size:0.8em;">', 'end' : '</font>'}},
					'normal' : {'html' : '<font style="font-size:1em;">ABCDEF</font>', 'btnClass' : 'geditor-btn', 'data' : {'func' : 'wrap', 'start' : '<font style="font-size:1em;">', 'end' : '</font>'}},
					'big' : {'html' : '<font style="font-size:1.5em;">ABCDEF</font>', 'btnClass' : 'geditor-btn', 'data' : {'func' : 'wrap', 'start' : '<font style="font-size:1.5em;">', 'end' : '</font>'}},
					'huge' : {'html' : '<font style="font-size:2em;">ABCDEF</font>', 'btnClass' : 'geditor-btn', 'data' : {'func' : 'wrap', 'start' : '<font style="font-size:2em;">', 'end' : '</font>'}},
				}}},
			}
		}, params);
		
		this.init();
	};
	
	GEditor.prototype = {
		init: function(){
			var editor = this;
			editor.createDiv();
			editor.createButtonsbar();
			editor.setDimensions();
			
			$(editor.body).on('mousedown', function(){
				editor.selection_start = true;
				if($(editor.body).html() == ''){
					//editor.insert('<p></p>');
				}
			});
			jQuery(document).on('mouseup', function(){
				if(editor.selection_start){
					editor.selection = editor.getSelectionHtml();//needed for IE
					editor.range = editor.getRange();//needed for IE
					editor.selection_start = false;
				}
			});
			$(editor.body).on('keydown', function(){
				editor.selection_start = true;
				if($(editor.body).html() == ''){
					//editor.insert('<p></p>');
				}
			});
			jQuery(document).on('keyup', function(){
				if(editor.selection_start){
					editor.selection = editor.getSelectionHtml();//needed for IE
					editor.range = editor.getRange();//needed for IE
					editor.selection_start = false;
				}
			});
			//initilaize buttons functions
			$(editor.header).find('.btn-geditor').each(function(){
				if($(this).data('func') == 'dropdown'){
					if(!$.isEmptyObject($(this).data('buttons'))){
						var buttons = editor.buttonsList($(this).data('buttons'));
						/*buttons.each(function(btn){
							btn.on('click', function(){
								console.log(1);
							});
						});*/
						//$(this).data('content', buttons);
						$(this).gtooltip({
							'tipclass':'gtooltip geditor-dropdown',
							'awaytime':0, 
							'createOnShow':true, 
							'trigger':'click', 
							'content':buttons, 
							'css':{
								'padding':'2px',
								'background-color':'#ddd',
								'border-color':'#888',
							},
							'onShow':function(tipObj){
								//tipObj.tip.find('.gtooltip-content').html('');
								//tipObj.tip.find('.gtooltip-content').append(buttons);
								tipObj.tip.find('.btn-geditor').on('click', function(){
									editor.setButtonsEvent($(this));
								});
							}
						});
					}
				}
			});
			$(editor.header).find('.btn-geditor').on('click', function(){
				editor.setButtonsEvent($(this));
			});
		},
		
		setButtonsEvent: function(button){
			var editor = this;
			console.log(editor.range);
			console.log(editor.range.startContainer);
			if(editor.selection){
				if(button.data('func') == 'wrap'){
					editor.replaceSelection(editor.range, button.data('start') + editor.selection + button.data('end'));
				}
			}else{
				if(button.data('func') == 'wrap'){
					var lastNode = editor.replaceSelection(editor.range, button.data('start') + '' + button.data('end'));
					if(lastNode){
						editor.range.setStart(lastNode, 0);
						editor.range.setEnd(lastNode, 0);
					}
				}
			}
			editor.body.focus();
			editor.setRange(editor.body, editor.range);
			$('#test_editor').val($(editor.body).html());
		},
		
		createDiv: function(){
			var editor = this;
			var editorDiv = $('<div class="'+editor.settings.div_class+'"></div>');
			editor.div = editorDiv[0];
			
			editorDiv.height($(editor.textarea).height());
			editorDiv.outerWidth($(editor.textarea).outerWidth());
			$(editor.textarea).after(editorDiv);
			
			var editorBody = $('<div class="'+editor.settings.body_class+'"></div>');
			editorDiv.append(editorBody);
			//editorBody.height($(editor.textarea).height());
			editorBody.css('overflow-y', 'scroll');
			editorBody.css('padding', '5px');
			editor.body = editorBody[0];
			//initilaize the body properties
			editorBody.prop('contenteditable', true);
			editorBody.html($(editor.textarea).val());
		},
		
		createButtonsbar: function(){
			var editor = this;
			//add the bar
			var editorHeader = $('<div class="'+editor.settings.header_class+'"></div>');
			$(editor.div).prepend(editorHeader);
			editor.header = editorHeader[0];
			//add the buttons
			if(!$.isEmptyObject(editor.settings.buttons)){
				editorHeader.append(editor.buttonsList(editor.settings.buttons));
			}
		},
		
		setDimensions: function(){
			var editor = this;
			$(editor.body).outerHeight($(editor.div).height() - $(editor.header).outerHeight());
		},
		
		buttonsList: function(buttons){
			var list = [];
			$.each(buttons, function(k, btn){
				var button = $('<div class="'+btn.btnClass+' btn-geditor">'+btn.html+'</div>');
				if(!$.isEmptyObject(btn.data)){
					$.each(btn.data, function(d, dat){
						button.data(d, dat);
						button.attr('data-'+d, dat);
					});
				}
				list.push(button);
			});
			return list;
		},
		
		insert: function(string){
			var editor = this;
			$(editor.body).html(string);
		},
		
		getRange: function (){
			var range;
			if (window.getSelection && window.getSelection().getRangeAt){
				range = window.getSelection().getRangeAt(0);
			}else if(document.selection && document.selection.createRange){
				range = document.selection.createRange();
			}
			return range;
		},
		
		setRange: function (el, range){
			/*if(typeof window.getSelection != "undefined" && typeof document.createRange != "undefined"){
				//var range = document.createRange();
				//range.selectNodeContents(el);
				//range.collapse(false);
				var sel = window.getSelection();
				sel.removeAllRanges();
				sel.addRange(range);
			} else if (typeof document.body.createTextRange != "undefined") {
				var textRange = document.body.createTextRange();
				textRange.moveToElementText(el);
				textRange.collapse(false);
				textRange.select();
			}*/
			if(range){
				if(typeof window.getSelection != "undefined" && typeof document.createRange != "undefined"){
					var sel = window.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
				} else if (typeof document.body.createTextRange != "undefined") {
					range.select();
				}
			}
		},
		
		replaceSelection: function (range, html){
			if(range){
				if (window.getSelection && window.getSelection().getRangeAt){
					range.deleteContents();
					var div = document.createElement("div");
					div.innerHTML = html;
					var frag = document.createDocumentFragment(), child, lastNode;
					while ((child = div.firstChild)){
						lastNode = frag.appendChild(child);
					}
					range.insertNode(frag);
					return lastNode;
				}else if(document.selection && document.selection.createRange){
					range.pasteHTML(html);
				}
			}
			return false;
		},
		
		getSelectionHtml: function () {
			var html = "";
			if (typeof window.getSelection != "undefined") {
				var sel = window.getSelection();
				if (sel.rangeCount) {
					var container = document.createElement("div");
					for (var i = 0, len = sel.rangeCount; i < len; ++i) {
						container.appendChild(sel.getRangeAt(i).cloneContents());
					}
					html = container.innerHTML;
				}
			} else if (typeof document.selection != "undefined") {
				if (document.selection.type == "Text") {
					html = document.selection.createRange().htmlText;
				}
			}
			return html;
		}
	};
	
}(jQuery));