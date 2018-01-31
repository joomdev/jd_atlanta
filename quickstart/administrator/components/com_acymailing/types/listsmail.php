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

class listsmailType{
	var $type = 'news';

	function load(){
		$db = JFactory::getDBO();

		$query = 'SELECT a.listid as listid,COUNT(a.mailid) as total FROM `#__acymailing_mail` as c';
		$query .= ' JOIN `#__acymailing_listmail` as a ON a.mailid = c.mailid';
		$query .= ' WHERE c.type = \''.$this->type.'\' GROUP BY a.listid';
		$db->setQuery($query);
		$alllists = $db->loadObjectList('listid');

		$allnames = array();
		if(!empty($alllists)){
			$db->setQuery('SELECT name,listid FROM `#__acymailing_list` WHERE listid IN ('.implode(',',array_keys($alllists)).') ORDER BY ordering ASC');
			$allnames = $db->loadObjectList('listid');
		}

		$this->values = array();
		$this->values[] = acymailing_selectOption('0', acymailing_translation('ALL_LISTS'));
		foreach($allnames as $listid => $oneName){
			$this->values[] = acymailing_selectOption($listid, $oneName->name.' ( '.$alllists[$listid]->total.' )' );
		}
	}

	function display($map,$value){
		$this->load();
		return acymailing_select(  $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int) $value );
	}
}
