/*
# News Show SP2 - News display/Slider module by JoomShaper.com
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2015 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 or later
# Websites: http://www.joomshaper.com
*/

jQuery(function($) {
	
	$('#jform_params_asset-lbl').parent().parent().remove();

	nssp2_showhide();

	$("#jform_params_article_count_title_text,#jform_params_article_count_intro_text,#jform_params_article_more_text,#jform_params_article_image_float,#jform_params_links_title_count,#jform_params_links_intro_count,#jform_params_links_more_text,#jform_params_links_image_float").parent().parent().css("display", "none");
	
	$('#jform_params_article_count_title_text').insertAfter($('#jform_params_article_title_text_limit').wrap('<div class="nssp2" />'));
	$('#jform_params_article_count_intro_text').insertAfter($('#jform_params_article_intro_text_limit').wrap('<div class="nssp2" />'));
	$('#jform_params_article_image_float').insertAfter($('#jform_params_article_image_pos').wrap('<div class="nssp2" />'));
	$('#jform_params_article_more_text').insertAfter($('#jform_params_article_show_more').wrap('<div class="nssp2" />'));
	$('#jform_params_links_title_count').insertAfter($('#jform_params_links_title_text_limit').wrap('<div class="nssp2" />'));
	$('#jform_params_links_intro_count').insertAfter($('#jform_params_links_intro_text_limit').wrap('<div class="nssp2" />'));
	$('#jform_params_links_more_text').insertAfter($('#jform_params_links_more').wrap('<div class="nssp2" />'));
	$('#jform_params_links_image_float').insertAfter($('#jform_params_links_image_pos').wrap('<div class="nssp2" />'));
	
	$('#jform_params_content_source, #jform_params_article_animation, #jform_params_links_animation').change(function(){
		nssp2_showhide();
	});

	function nssp2_showhide(){

		if ($("#jform_params_content_source").val()=="k2") {
			$("#jform_params_catids").parent().parent().css("display", "none");
			$("#jformparamsk2catids,#jform_params_article_extra_fields").parent().parent().css("display", "block");		
		} else {
			$("#jform_params_catids").parent().parent().css("display", "block");	
			$("#jformparamsk2catids,#jform_params_article_extra_fields").parent().parent().css("display", "none");		
		}
		
		//Virtuemart
		if ($("#jform_params_content_source").val()=="vm") {
			$(".vm,#jform_params_vmcat-lbl").parent().parent().css("display", "block");
			$("#jform_params_ordering,#jform_params_ordering_direction-lbl,#jformparamsk2catids,#jform_params_catids,#jform_params_user_id-lbl,#jform_params_show_featured-lbl").parent().parent().css("display", "none");
		} else {
			$(".vm,#jform_params_vmcat-lbl").parent().parent().css("display", "none");
			$("#jform_params_ordering,#jform_params_ordering_direction-lbl,#jform_params_user_id-lbl,#jform_params_show_featured-lbl").parent().parent().css("display", "block");
		}
		
		//block1 animation
		if ($("#jform_params_article_animation").val()=="disabled") {
			$(".ani1").parent().parent().css("display", "none");
		} else {
			$(".ani1").parent().parent().css("display", "block");
		}

		if ($("#jform_params_links_animation").val()=="disabled") {
			$(".ani2").parent().parent().css("display", "none");
		} else {
			$(".ani2").parent().parent().css("display", "block");
		}

	}
});