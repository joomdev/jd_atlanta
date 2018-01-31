<?php
/*
# News Show SP2 - News display/Slider module by JoomShaper.com
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2015 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 or later
# Websites: http://www.joomshaper.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.form.formfield');

class JFormFieldAssets extends JFormField
{
	protected	$type = 'Assets';
	
	protected function getInput() {
		$doc = JFactory::getDocument();
		JHtml::_('jquery.framework');
		$doc->addScript(JURI::root(true).'/modules/mod_news_show_sp2/elements/js/script.js');
		$doc->addStylesheet(JURI::root(true).'/modules/mod_news_show_sp2/elements/css/style.css');			
		
		return null;
	}
}