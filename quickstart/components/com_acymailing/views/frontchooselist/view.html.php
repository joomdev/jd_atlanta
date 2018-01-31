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


class frontchooselistViewfrontchooselist extends acymailingView
{
	function display($tpl = null)
	{
		$function = $this->getLayout();
		if(method_exists($this,$function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){

		$listClass = acymailing_get('class.list');
		$rows = $listClass->getFrontendLists();

		$selectedLists = acymailing_getVar('string', 'values', '', '');

		if(strtolower($selectedLists) == 'all'){
			foreach($rows as $id => $oneRow){
				$rows[$id]->selected = true;
			}
		}elseif(!empty($selectedLists)){
			$selectedLists = explode(',',$selectedLists);
			foreach($rows as $id => $oneRow){
				if(in_array($oneRow->listid,$selectedLists)){
					$rows[$id]->selected = true;
				}
			}
		}

		$fieldName = acymailing_getVar('string', 'task');
		$controlName = acymailing_getVar('string', 'control', 'params');
		$popup = acymailing_getVar('string', 'popup', '1');

		$this->rows = $rows;
		$this->selectedLists = $selectedLists;
		$this->fieldName = $fieldName;
		$this->controlName = $controlName;
		$this->popup = $popup;
	}
}
