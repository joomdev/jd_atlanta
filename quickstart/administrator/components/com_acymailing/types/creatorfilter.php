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

class creatorfilterType{
	var $type = '';
	function load($table){
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(*) as total,userid FROM '.acymailing_table($table).' WHERE `userid` > 0';
		if(!empty($this->type)) $query .= ' AND `type` = '.acymailing_escapeDB($this->type);
		$query .= ' GROUP BY userid';
		$db->setQuery($query);
		$allusers = $db->loadObjectList('userid');

		$allnames = array();
		if(!empty($allusers)){
			$db->setQuery('SELECT name,id FROM #__users WHERE id IN ('.implode(',',array_keys($allusers)).') ORDER BY name ASC');
			$allnames = $db->loadObjectList('id');
		}

		$this->values = array();
		$this->values[] = acymailing_selectOption('0', acymailing_translation('ALL_CREATORS'));
		foreach($allnames as $userid => $oneCreator){
			$this->values[] = acymailing_selectOption($userid, $oneCreator->name.' ( '.$allusers[$userid]->total.' )' );
		}
	}

	function display($map,$value,$table){
		$this->load($table);
		return acymailing_select(  $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int) $value );
	}
}
