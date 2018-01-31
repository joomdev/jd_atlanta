<?php
/**
 * @package 	mod_bt_login - BT Login Module
 * @version		2.6.0
 * @created		April 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2011 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'cms.captcha.captcha' );
jimport ( 'joomla.application.component.view' );
jimport ( 'joomla.application.component.helper' );
jimport ( 'joomla.plugin.plugin' );


// Include the syndicate functions only once
require_once (dirname ( __FILE__ ) . '/helper.php');
modbt_loginHelper::fetchHead ( $params );

// load language 
$language = JFactory::getLanguage();
$language_tag = $language->getTag(); // loads the current language-tag
JFactory::getLanguage()->load('plg_captcha_recaptcha',JPATH_ADMINISTRATOR,$language_tag,true);
JFactory::getLanguage()->load('mod_bt_login',JPATH_SITE,$language_tag,true);
JFactory::getLanguage()->load('lib_joomla',JPATH_SITE,$language_tag,true);
JFactory::getLanguage()->load('com_users',JPATH_SITE,$language_tag,true);

$mainframe = JFactory::getApplication ();
$bttask = JRequest::getVar('bttask');
if($bttask){
	modbt_loginHelper::ajax($bttask, $params);
}

$mainframe = JFactory::getApplication ();


//get position display
$align = $params->get ( 'align_option' );

//get color setting
$bgColor=$params->get('bg_button_color','#6d850a');
$textColor=$params->get('text_button_color','#fff');

$showLogout= $params->get('logout_button',1);
//setting component to integrated
$integrated_com = $params->get ( 'integrated_component' );
$moduleRender = '';
$linkOption = '';
if($integrated_com != ''){
	if ($integrated_com == 'k2') {
		$moduleRender = modbt_loginHelper::loadModule ( 'mod_k2_login', 'K2 Login' );
		if (! JComponentHelper::isEnabled ( 'com_k2', true )) {
			$integrated_com = '';
		} else {
			$linkOption = 'index.php?option=com_users&view=registration';
		}
	} elseif ($integrated_com == 'jomsocial') {
		$moduleRender = modbt_loginHelper::loadModule ( 'mod_sclogin', 'SCLogin' );
		if (! JComponentHelper::isEnabled ( 'com_community', true )) {
			$integrated_com = '';
		} else {
			$linkOption = 'index.php?option=com_community&view=register&task=register';
		}
	} elseif ($integrated_com == 'cb') {
		$moduleRender = modbt_loginHelper::loadModule ( 'mod_cblogin', 'CB Login' );
		if (! JComponentHelper::isEnabled ( 'com_comprofiler', true )) {
			$integrated_com = '';
		} else {
			$linkOption = 'index.php?option=com_comprofiler&task=registers';
		}
	} elseif($integrated_com =='com_user') {
		$moduleRender = modbt_loginHelper::loadModule ( 'mod_login', 'Login' );
		$linkOption = 'index.php?option=com_users&view=registration';
	}elseif ($integrated_com == 'option') {
		$moduleRender = modbt_loginHelper::loadModuleById ( $params->get ( 'module_option' ) );
		$linkOption = $params->get ( 'link_option' );
	
	}elseif ($integrated_com == 'joomshopping') {
		$moduleRender = modbt_loginHelper::loadModule ( 'mod_jshopping_login', 'JS Login' );
		if (! JComponentHelper::isEnabled ( 'com_jshopping', true )) {
			$integrated_com = '';
		}else {
			$linkOption = 'index.php?option=com_jshopping&task=registers';
		}     
	
	}
}
$linkOption = JRoute::_($linkOption);
//get option tag active modal
$loginTag = $params->get ( 'tag_login_modal' );
if($params->get('enabled_registration', 1)){
	$registerTag = $params->get ( 'tag_register_modal' );
}else{
	$registerTag='';
}

$type = modbt_loginHelper::getType ();

$return = modbt_loginHelper::getReturnURL ( $params, $type );

$return_decode = base64_decode($return);

$return_decode = str_replace('&amp;','&',JRoute::_($return_decode));

$loggedInHtml = modbt_loginHelper::getModules ( $params );

$user =  JFactory::getUser ();

//setting display type
if ($params->get ( "display_type" ) == 1) {
	$effect = 'btl-dropdown';
} else {
	$effect = 'btl-modal';
}

//setting for registration 
$usersConfig = JComponentHelper::getParams ( 'com_users' );
$enabledRegistration = false;
$viewName = JRequest::getVar ( 'view', 'registry' );
$enabledRecaptcha = 'none';
if ($usersConfig->get ( 'allowUserRegistration' ) && $params->get ( "enabled_registration", 1 ) && ($viewName != "registration" || $integrated_com !='') ) {
	$enabledRegistration = true;
	$enabledRecaptcha = $params->get('use_captcha', 1);
	if($enabledRecaptcha == 1){
		//create instance captcha, get recaptcha
		
		$captcha = JFactory::getConfig ()->get ( 'captcha' );
		if($captcha){
			$reCaptcha = JCaptcha::getInstance ($captcha);
			$reCaptcha = $reCaptcha->display ('bt-login-recaptcha', 'bt-login-recaptcha', 'bt-login-recaptcha' );
		}else{
			$reCaptcha = '';
			$enabledRecaptcha = 0;
		}
	}else if($enabledRecaptcha == 2){
		$reCaptcha = modbt_loginHelper::getBuiltinCaptcha();	
	}
}

$language = JFactory::getLanguage ();
require (JModuleHelper::getLayoutPath ( 'mod_bt_login' ));
?>

