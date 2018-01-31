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

class FilterController extends acymailingController{
	var $pkey = 'filid';
	var $table = 'filter';

	function listing(){
		return $this->add();
	}

	function countresults(){
		$filterClass = acymailing_get('class.filter');
		$num = acymailing_getVar('int', 'num');
		$filters = acymailing_getVar('none', 'filter');
		$query = new acyQuery();
		if(empty($filters['type'][$num])) die('No filter type found for the num '.intval($num));
		$currentType = $filters['type'][$num];
		if(empty($filters[$num][$currentType])) die('No filter parameters founds for the num '.intval($num));
		$currentFilterData = $filters[$num][$currentType];
		acymailing_importPlugin('acymailing');
		$messages = acymailing_trigger('onAcyProcessFilterCount_'.$currentType, array(&$query,$currentFilterData,$num));
		echo implode(' | ',$messages);
		exit;
	}

	function displayCondFilter(){
		acymailing_importPlugin('acymailing');
		$fct = acymailing_getVar('none', 'fct');

		$message = acymailing_trigger('onAcyTriggerFct_'.$fct);
		echo implode(' | ',$message);
		exit;
	}

	function process(){
		if(!$this->isAllowed('lists','filter')) return;
		acymailing_checkToken();

		$filid = acymailing_getVar('int', 'filid');
		if(!empty($filid)){
			$this->store();
		}

		$filterClass = acymailing_get('class.filter');
		$filterClass->subid = acymailing_getVar('string', 'subid');
		$filterClass->execute(acymailing_getVar('none', 'filter'),acymailing_getVar('none', 'action'));

		if(!empty($filterClass->report)){
			if(acymailing_getVar('cmd', 'tmpl') == 'component'){
				echo acymailing_display($filterClass->report,'info');
				if(acymailing_getVar('string', 'tmpl', '') != 'component') {
					acymailing_addScript(true, "setTimeout('redirect()',2000); function redirect(){window.top.location.href = 'index.php?option=com_acymailing&ctrl=subscriber'; }");
				}
				return;
			}else{
				foreach($filterClass->report as $oneReport){
					acymailing_enqueueMessage($oneReport);
				}
			}
		}
		return $this->edit();
	}

	function filterDisplayUsers(){
		if(!$this->isAllowed('lists','filter')) return;
		acymailing_checkToken();
		return $this->edit();
	}

	function store(){
		if(!$this->isAllowed('lists','filter')) return;
		acymailing_checkToken();

		$class = acymailing_get('class.filter');
		$status = $class->saveForm();
		if($status){
			acymailing_enqueueMessage(acymailing_translation( 'JOOMEXT_SUCC_SAVED' ), 'message');
		}else{
			acymailing_enqueueMessage(acymailing_translation( 'ERROR_SAVING' ), 'error');
			if(!empty($class->errors)){
				foreach($class->errors as $oneError){
					acymailing_enqueueMessage($oneError, 'error');
				}
			}
		}
	}
}
