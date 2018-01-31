<?php
/*
# News Show SP2 - News display/Slider module by JoomShaper.com
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2015 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 or later
# Websites: http://www.joomshaper.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$doc 								= JFactory::getDocument();

//Basic
$moduleclass_sfx 					= $params->get('moduleclass_sfx');
$layout 							= $params->get('layout', 'default');
$moduleName         				= basename(dirname(__FILE__));
$uniqid								= ($params->get('uniqid')=="") ? $module->id : $params->get('uniqid');
$content_source						= $params->get('content_source');

//Article Layout
$article_column						= $params->get('article_column');
$article_row						= $params->get('article_row');
$article_col_padding				= $params->get('article_col_padding');
$article_showtitle					= $params->get('article_showtitle');
$article_linkedtitle				= $params->get('article_linkedtitle');
$article_title_text_limit			= $params->get('article_title_text_limit');
$article_count_title_text			= $params->get('article_count_title_text');
$article_introtext					= $params->get('article_introtext');
$article_intro_text_limit			= $params->get('article_intro_text_limit');
$article_count_intro_text			= $params->get('article_count_intro_text');
$article_date_format				= $params->get('article_date_format');
$article_show_author				= $params->get('article_show_author');
$article_show_category				= $params->get('article_show_category');
$article_linked_category			= $params->get('article_linked_category');
$article_show_image					= $params->get('article_show_image');
$article_linked_image				= $params->get('article_linked_image');
$article_image_pos					= $params->get('article_image_pos');
$article_image_float				= $params->get('article_image_float');			
$article_image_margin				= $params->get('article_image_margin');
$article_thumb_width				= $params->get('article_thumb_width');
$article_thumb_height				= $params->get('article_thumb_height');
$article_thumb_ratio				= $params->get('article_thumb_ratio');
$article_extra_fields				= $params->get('article_extra_fields');
$article_show_more					= $params->get('article_show_more');
$article_more_text					= $params->get('article_more_text');
$article_comments					= $params->get('article_comments');
$article_hits						= $params->get('article_hits');
$article_show_ratings				= $params->get('article_show_ratings');
$article_animation					= $params->get('article_animation');

if( ( $article_animation == 'cover-horizontal-push' ) || ( $article_animation == 'cover-vertical-push' ) )
{
	$article_animation 				= 'nssp2-slide';

}
else if ( $article_animation == 'cover-inplace-fade' )
{
	$article_animation 				= 'nssp2-slide nssp2-fade';
} 
else if ( $article_animation == 'cover-inplace' )
{
	$article_animation 				= 'nssp2-noeffect';
}


$article_slide_count				= $params->get('article_slide_count');
$article_controllers_style			= $params->get('article_controllers_style', 'nssp2-default');
$article_pagination					= $params->get('article_pagination');
$article_arrows						= $params->get('article_arrows');
$article_autoplay					= $params->get('article_autoplay');
$article_animation_interval			= ( $article_autoplay ) ? $params->get('article_animation_interval') : 'false';

//Links Layout
$links_block						= $params->get('links_block');
$links_count						= $params->get('links_count');
$links_col_padding					= $params->get('links_col_padding');
$links_position						= $params->get('links_position');
$links_more							= $params->get('links_more');
$links_more_text					= $params->get('links_more_text');
$links_title_text_limit				= $params->get('links_title_text_limit');
$links_title_count					= $params->get('links_title_count');
$links_show_intro					= $params->get('links_show_intro');
$links_intro_text_limit				= $params->get('links_intro_text_limit');
$links_intro_count					= $params->get('links_intro_count');
$links_show_image					= $params->get('links_show_image');
$links_linked_image					= $params->get('links_linked_image');
$links_image_pos					= $params->get('links_image_pos');
$links_image_float					= $params->get('links_image_float');
$links_image_margin					= $params->get('links_image_margin');
$links_thumb_width					= $params->get('links_thumb_width');
$links_thumb_height					= $params->get('links_thumb_height');
$links_thumb_ratio					= $params->get('links_thumb_ratio');
$links_animation					= $params->get('links_animation');

if( ( $links_animation == 'cover-horizontal-push' ) || ( $links_animation == 'cover-vertical-push' ) )
{
	$links_animation 				= 'nssp2-slide';

}
else if ( $links_animation == 'cover-inplace-fade' )
{
	$links_animation 				= 'nssp2-slide nssp2-fade';
} 
else if ( $links_animation == 'cover-inplace' )
{
	$links_animation 				= 'nssp2-noeffect';
}

$links_slide_count					= $params->get('links_slide_count');
$links_controllers_style			= $params->get('links_controllers_style', 'nssp2-default');
$links_pagination					= $params->get('links_pagination');
$links_arrows						= $params->get('links_arrows');
$links_autoplay						= $params->get('links_autoplay');
$links_animation_interval			= ( $links_autoplay ) ? $params->get('links_animation_interval') : 'false';

//Virtuemart
$art_show_price 					= $params->get('art_show_price');
$links_show_price 					= $params->get('links_show_price');
$art_show_cart_button 				= $params->get('art_show_cart_button');
$links_show_cart_button 			= $params->get('links_show_cart_button');

JHtml::_('jquery.framework'); //jQuery
	
//Calculated count	
if ($article_animation!="disabled") {
	$c_article_count				= $article_column*$article_row*$article_slide_count;
} else {
	$c_article_count				= $article_column*$article_row;
}

if ($links_block) {
	if ($links_animation!="disabled") {
		$c_links_count					= $links_count*$links_slide_count;
	} else {
		$c_links_count					= $links_count;
	}
} else {
	$c_links_count						= 0;
}

$c_count 							= $c_article_count + $c_links_count;

require_once (dirname(__FILE__).'/common.php');//include common.php file

if ($content_source=="joomla") {
	require_once (dirname(__FILE__).'/helper.php');
	$list 		= modNSSP2JHelper::getList($params, $c_count);
} elseif ($content_source=="vm") {
	if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/vmmodel.php');
	require_once (dirname(__FILE__).'/vmhelper.php');
	$list 		= modNSSP2VMHelper::getList($params, $c_count);	
} else {
	require_once (dirname(__FILE__).'/k2helper.php');
	$list 							= modNSSP2K2Helper::getList($params, $c_count);
}

//Social
require_once (dirname(__FILE__).'/social.php');

$a_count 							= count($list);//actual count

if ($c_count>$a_count) {
	$c_count						= $a_count;
	if ($c_article_count>=$c_count) {
		$c_article_count			= $c_count;
		$c_links_count				= 0;
	} else {
		if ($c_links_count>$c_count-$c_article_count) {
			$c_links_count			= $c_count-$c_article_count;
		}	
	}
}

if (($content_source=="vm") && ($art_show_cart_button || $links_show_cart_button)) {
	vmJsApi::jQuery();
	vmJsApi::jPrice();
	vmJsApi::cssSite();
}

$cssFile 							= JPATH_THEMES. '/'.$doc->template.'/css/'.$moduleName.'.css';

if(file_exists($cssFile)) {
	$doc->addStylesheet(JURI::base(true) . '/templates/'.$doc->template.'/css/'. $moduleName . '.css');
} else {
	$doc->addStylesheet(JURI::base(true) . '/modules/'.$moduleName.'/assets/css/' . $moduleName . '.css');
}

if ($article_animation!="disabled" || ($links_block && $c_links_count!=0 && $links_animation!="disabled")) {
	$doc->addScript(JURI::base(true) . '/modules/mod_news_show_sp2/assets/js/nssp2.js');
}
require(JModuleHelper::getLayoutPath('mod_news_show_sp2', $layout));