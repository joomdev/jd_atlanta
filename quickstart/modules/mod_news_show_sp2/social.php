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

class modNSSP2SocialHelper {
	
	 public static function icons($data, $params) {
		
		$icons = array();
		$url = urldecode(JRoute::_(strstr($data->link, 'index.php')));
		$url = rtrim(JURI::base(),'/') . $url;
		
		if ($params->get('btn_like')) { // Facebook Like
			$icons[] = '<span class="ns2-share-icon"><div class="fb-like" data-href="' . $url . '" data-send="false" data-layout="button_count" data-width="80" data-show-faces="false"></div></span>';
			
			if (defined('_NS2LIKE')) {
			
				define ('_NS2LIKE', 1);
				
				echo '<div id="fb-root"></div>';
				JFactory::getDocument()->addScriptDeclaration('
				(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=354400064582736";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, \'script\', \'facebook-jssdk\'));
				');			
			}
			
		}
		
		if ($params->get('btn_twitter')) { //Twitter Button
			$icons[] = '<span class="ns2-share-icon"><a href="https://twitter.com/share" class="twitter-share-button" data-text="' . $data->title . '" data-url="' . $url . '">Tweet</a></span>';
			JFactory::getDocument()->addScript('http://platform.twitter.com/widgets.js');
		}
			
		if ($params->get('btn_gplus')) { // Goolge Plus Button
			$icons[] = '<span class="ns2-share-icon"><g:plusone href="' . $url . '" size="medium"></g:plusone></span>';
			JFactory::getDocument()->addScript('https://apis.google.com/js/plusone.js');
		}
					
		return $icons;
	 }
}