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
if(version_compare(PHP_VERSION, '5.0.0', '<')){
	echo '<p style="color:red">This version of AcyMailing does not support PHP4, it is time to upgrade your server to PHP5!</p>';
	exit;
}

if(!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo "Could not load Acy helper file";
	return;
}

if(defined('JDEBUG') AND JDEBUG) acymailing_displayErrors();

$taskGroup = acymailing_getVar('cmd', 'ctrl', acymailing_getVar('cmd', 'gtask', 'dashboard'));
if($taskGroup == 'config') $taskGroup = 'cpanel';

$config = acymailing_config();

acymailing_addStyle(false, ACYMAILING_CSS.'backend_default.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'backend_default.css'));
$cssBackend = $config->get('css_backend');
if($cssBackend == 'backend_custom' && file_exists(ACYMAILING_MEDIA.'css'.DS.'backend_custom.css')) acymailing_addStyle(false, ACYMAILING_CSS.'backend_custom.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'backend_custom.css'));


acymailing_addScript(false, ACYMAILING_JS.'acymailing.js?v='.filemtime(ACYMAILING_MEDIA.'js'.DS.'acymailing.js'));

if($taskGroup != 'update' && !$config->get('installcomplete')){
	$url = acymailing_completeLink('update&task=install', false, true);
	echo "<script>document.location.href='".$url."';</script>\n";
	echo 'Install not finished... You will be redirected to the second part of the install screen<br />';
	echo '<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>';
	return;
}


$action = acymailing_getVar('cmd', 'task', 'listing');
if(empty($action)){
	$action = acymailing_getVar('cmd', 'defaulttask', 'listing');
	acymailing_setVar('task', $action);
}

$menuDisplayed = false;
if(!($taskGroup == 'send' && $action == 'send') && $taskGroup !== 'toggle' && acymailing_getVar('string', 'tmpl') !== 'component' && !in_array($action, array('doexport', 'continuesend', 'load')) && !in_array($taskGroup, array('editor'))){
	$menuHelper = acymailing_get('helper.acymenu');
	echo '<div id="acyallcontent" class="acyallcontent">';
	echo $menuHelper->display($taskGroup);

	echo '<div id="acymainarea" class="acymaincontent_'.$taskGroup.'">';
	$menuDisplayed = true;
}

if($taskGroup != 'update' && ACYMAILING_J16 && !acymailing_authorised('core.manage', 'com_acymailing')){
	acymailing_display(acymailing_translation('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}
if(($taskGroup == 'cpanel' || ($taskGroup == 'update' && $action == 'listing')) && ACYMAILING_J16 && !acymailing_authorised('core.admin', 'com_acymailing')){
	acymailing_display(acymailing_translation('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

if(!include_once(ACYMAILING_CONTROLLER.$taskGroup.'.php')){
	acymailing_redirect('index.php?option=com_acymailing');
	return;
}
$className = ucfirst($taskGroup).'Controller';
$classGroup = new $className();

acymailing_setVar('view', $classGroup->getName());
$classGroup->execute($action);

$classGroup->redirect();

if($menuDisplayed){
	echo '</div></div>';
}
