<?php
/**
* COMPONENT FILE HEADER
**/
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
class pkg_chronoforms5InstallerScript {
	function postflight($type, $parent){
		$mainframe = JFactory::getApplication();
		//$mainframe->redirect('index.php?option=com_chronoforms5&cont=installer');
		$parent->getParent()->setRedirectURL('index.php?option=com_chronoforms5&cont=installer');
	}
}
?>