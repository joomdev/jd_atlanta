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

class statusfilterlistType{
	var $extra = '';
	function __construct(){
		$this->values = array();
		$this->values[] = acymailing_selectOption('1', acymailing_translation('SUBSCRIBERS'));
		$this->values[] = acymailing_selectOption('2', acymailing_translation('PENDING_SUBSCRIPTION'));
		$this->values[] = acymailing_selectOption('-1', acymailing_translation('UNSUBSCRIBERS'));
		$this->values[] = acymailing_selectOption('-2', acymailing_translation('NO_SUBSCRIPTION'));
	}

	function display($map,$value,$submit = true){
		$onChange = $submit ? 'onchange="document.adminForm.limitstart.value=0;document.adminForm.submit( );"' : '';
		return acymailing_select(  $this->values, $map, 'class="inputbox" size="1" '.$onChange.' '.$this->extra, 'value', 'text', (int) $value );
	}
}
