<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class plgSystemJceacymailing extends JPlugin{
	function onBeforeWfEditorRender(&$settings) {
		if(JRequest::getString('option', '') != 'com_acymailing') return;

		$acycssfile = JRequest::getString('acycssfile');
		if(!empty($acycssfile)) $settings['content_css'] = $acycssfile;
	}
}
