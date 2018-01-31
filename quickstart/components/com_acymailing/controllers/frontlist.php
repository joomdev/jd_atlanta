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
$currentUserid = acymailing_currentUserId();
if(empty($currentUserid)){
	$usercomp = !ACYMAILING_J16 ? 'com_user' : 'com_users';
	$uri = JFactory::getURI();
	$url = 'index.php?option='.$usercomp.'&view=login&return='.base64_encode($uri->toString());
	acymailing_redirect($url, acymailing_translation('ACY_NOTALLOWED'), 'error');
	return false;
}

$config = acymailing_config();
if(!acymailing_isAllowed($config->get('acl_lists_manage', 'all'))) die(acymailing_translation('ACY_NOTALLOWED'));

include(ACYMAILING_BACK.'controllers'.DS.'list.php');
class FrontlistController extends ListController{
	function __construct($config = array()){
		parent::__construct($config);

		$listClass = acymailing_get('class.list');
		$lists = $listClass->getFrontendLists('listid');

		$listid = acymailing_getVar('int', 'listid', 0);

		if(empty($lists) || (!empty($listid) && !in_array($listid, array_keys($lists)))) {
			acymailing_redirect('index.php', acymailing_translation('ACY_NOTALLOWED'), 'error');
			return false;
		}
	}

	function remove(){
		$cids = acymailing_getVar('array', 'cid', array(), '');
		acymailing_arrayToInteger($cids);
		$db = JFactory::getDBO();

		if(empty($cids)) acymailing_redirect('index.php?option=com_acymailing&ctrl=frontlist');

		$db->setQuery('SELECT * FROM `#__acymailing_list` WHERE listid IN ('.implode(',', $cids).')');
		$lists = $db->loadObjectList();
		foreach($lists as $list){
			if(acymailing_currentUserId() != $list->userid){
				acymailing_enqueueMessage(acymailing_translation_sprintf('ACY_NO_ACCESS_LIST', $list->listid), 'error');
				array_splice($cids, array_search($list->listid, $cids), 1);
			}
		}

		acymailing_setVar('cid', $cids);
		return parent::remove();
	}

	function form(){
		return $this->edit();
	}

	function edit(){
		acymailing_setVar('layout', 'form');
		return parent::display();
	}
}
