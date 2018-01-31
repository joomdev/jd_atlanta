jQuery.noConflict();
window.addEvent("domready",function(){
	var parent = 'li:first';
	if(jQuery(".row-fluid").length){
		parent = 'div.control-group:first';
	}
    jQuery("#jform_params_asset-lbl").parents(parent).remove();
	jQuery('#module-sliders li > .btn-group').each(function(){
		if(jQuery(this).find('input').length != 2 ) return;
		if(this.id.indexOf('advancedparams') ==0) return;
		
		el = jQuery(this).find('input:checked').val();
		if( el != '0' && el != '1' && el != 'false' && el != 'true' && el != 'no' && el != 'yes' ){
			return;
		}
		
		jQuery(this).hide();
		var group = this;
		

		var el = jQuery(group).find('input:checked');	
		var switchClass ='';

		if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='false'){
			switchClass = 'no';
		}else{
			switchClass = 'yes';
		}
		var switcher = new Element('div',{'class' : 'switcher-'+switchClass});
		switcher.inject(group, 'after');
		switcher.addEvent("click", function(){
			var el = jQuery(group).find('input:checked');	
			if(el.val()=='' || el.val()=='0' || el.val()=='no' || el.val()=='false'){
				switcher.setProperty('class','switcher-yes');
			}else {
				switcher.setProperty('class','switcher-no');
			}
			jQuery(group).find('input:not(:checked)').attr('checked',true).trigger('click');
		});
	});
	jQuery('.bt_color').ColorPicker({
		color: '#0000ff',
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).val("#"+hex);
			//jQuery(el).css('background',jQuery(el).val())
			jQuery(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		jQuery(this).ColorPickerSetColor(this.value);
	});
	jQuery(".pane-sliders select").each(function(){
		if(this.id.indexOf('advancedparams') ==0) return;
		if(jQuery(this).is(":visible")) {
		jQuery(this).css("width",parseInt(jQuery(this).width())+20);
		jQuery(this).chosen();
		};
	})	
	jQuery(".chzn-container").click(function(){
		jQuery(".panel .pane-slider,.panel .panelform").css("overflow","visible");	
	})
	jQuery(".panel .title").click(function(){
		jQuery(".panel .pane-slider,.panel .panelform").css("overflow","hidden");		
	})	
	
	// Group element
	jQuery(".bt_control").each(function(){
		var control = this;;
		if(jQuery(control).parents(parent).css('display')=='none' ) return;
		
		// select box
		var name = control.id.replace('jform_params_','');
		jQuery(control).change(function(){
			jQuery(this).find('option').each(function(){
				jQuery('.'+name+'_'+this.value).each(function(){
					jQuery(this).parents(parent).hide();
				})
			})
			jQuery('.'+name+'_'+jQuery(this).find('option:selected').val()).each(function(){
				jQuery(this).parents(parent).fadeIn(500);
			})
		});
		jQuery(control).change();
		
		// radio box
		jQuery(control).find('input').each(function(){
			if(jQuery(this).is(':not(:checked)')){ 
				jQuery('.'+name+'_'+this.value).each(function(){
					jQuery(this).parents(parent).hide();
				});
			}
			jQuery(this).click(function(){
				jQuery(this).siblings().each(function(){
					jQuery('.'+name+'_'+this.value).each(function(){
						jQuery(this).parents(parent).fadeOut('fast');
					});
				})
				jQuery('.'+name+'_'+this.value).each(function(){
					jQuery(this).parents(parent).fadeIn(500);
				});
			})
		})
		
	});

});

