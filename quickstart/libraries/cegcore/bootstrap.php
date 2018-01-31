<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;

class Bootstrap {
	const VERSION = 1;
	const UPDATE = 2;
	public static function initialize($plathform = '', $params = array()){
		switch ($plathform){
			default:
				//CONSTANTS
				\GCore\C::set('GCORE_FRONT_PATH', dirname(__FILE__).DS);
				\GCore\C::set('GCORE_ADMIN_PATH', dirname(__FILE__).DS.'admin'.DS);
				//initialize language
				\GCore\Libs\Lang::initialize();
				//SET ERROR CONFIG
				if((int)Libs\Base::getConfig('error_reporting') != 1){
					error_reporting((int)Libs\Base::getConfig('error_reporting'));
				}
				if((bool)Libs\Base::getConfig('debug') === true){
					\GCore\Libs\Error::initialize();
				}
				//timezone
				date_default_timezone_set(Libs\Base::getConfig('timezone', 'UTC'));
			break;
		}
		
		if($plathform == 'joomla'){
			$mainframe = \JFactory::getApplication();
			\GCore\Libs\Base::setConfig('db_host', $mainframe->getCfg('host'));
			$dbtype = ($mainframe->getCfg('dbtype') == 'mysqli' ? 'mysql' : $mainframe->getCfg('dbtype'));
			\GCore\Libs\Base::setConfig('db_type', $dbtype);
			\GCore\Libs\Base::setConfig('db_name', $mainframe->getCfg('db'));
			\GCore\Libs\Base::setConfig('db_user', $mainframe->getCfg('user'));
			\GCore\Libs\Base::setConfig('db_pass', $mainframe->getCfg('password'));
			\GCore\Libs\Base::setConfig('db_prefix', $mainframe->getCfg('dbprefix'));
			\GCore\C::set('GSITE_PLATFORM', 'joomla');
			
			\GCore\C::set('GCORE_FRONT_URL', \JFactory::getURI()->root().'libraries/cegcore/');
			\GCore\C::set('GCORE_ADMIN_URL', \JFactory::getURI()->root().'libraries/cegcore/admin/');
			\GCore\C::set('GCORE_ROOT_URL', \JFactory::getURI()->root());
			
			\GCore\C::set('GCORE_ROOT_PATH', dirname(dirname(dirname(__FILE__))).DS);
			
			$lang = \JFactory::getLanguage();
			\GCore\Libs\Base::setConfig('site_language', $lang->getTag());
		}else if($plathform == 'wordpress'){
			global $wpdb;
			\GCore\Libs\Base::setConfig('db_host', DB_HOST);
			$dbtype = 'mysql';
			\GCore\Libs\Base::setConfig('db_type', $dbtype);
			\GCore\Libs\Base::setConfig('db_name', DB_NAME);
			\GCore\Libs\Base::setConfig('db_user', DB_USER);
			\GCore\Libs\Base::setConfig('db_pass', DB_PASSWORD);
			\GCore\Libs\Base::setConfig('db_prefix', $wpdb->prefix);
			\GCore\C::set('GSITE_PLATFORM', 'wordpress');
			
			\GCore\C::set('GCORE_FRONT_URL', plugins_url().'/'.$params['component'].'/cegcore/');
			\GCore\C::set('GCORE_ADMIN_URL', plugins_url().'/'.$params['component'].'/cegcore/admin/');
			\GCore\C::set('GCORE_ROOT_URL', site_url().'/');
			
			\GCore\C::set('GCORE_ROOT_PATH', dirname(dirname(dirname(__FILE__))).DS);
			
			\GCore\Libs\Base::setConfig('site_language', get_bloginfo('language'));
			//change the default page parameter string because WP uses the param "page"
			\GCore\Libs\Base::setConfig('page_url_param_name', 'page_num');
			
			if(function_exists('wp_magic_quotes')){
				$stripslashes_wp = function (&$value){
					$value = stripslashes($value);
				};
				array_walk_recursive($_GET, $stripslashes_wp);
				array_walk_recursive($_POST, $stripslashes_wp);
				array_walk_recursive($_COOKIE, $stripslashes_wp);
				array_walk_recursive($_REQUEST, $stripslashes_wp);
			}
		}else{
			\GCore\C::set('GSITE_PLATFORM', '');
			
			\GCore\C::set('GCORE_FRONT_URL', \GCore\Libs\Url::root());
			\GCore\C::set('GCORE_ADMIN_URL', \GCore\Libs\Url::root().'admin/');
			\GCore\C::set('GCORE_ROOT_URL', \GCore\C::get('GCORE_FRONT_URL'));
			
			\GCore\C::set('GCORE_ROOT_PATH', dirname(__FILE__).DS);
		}
		\GCore\C::set('GSITE_PATH', \GCore\C::get('GCORE_'.strtoupper(GCORE_SITE).'_PATH'));
		\GCore\C::set('GSITE_URL', \GCore\C::get('GCORE_'.strtoupper(GCORE_SITE).'_URL'));
	}
	
	public static function getApp($site = GCORE_SITE, $thread = 'gcore'){
		if(\GCore\C::get('GSITE_PLATFORM') == 'joomla'){
			$app = \GCore\Libs\AppJ::getInstance($site, $thread);
		}else if(\GCore\C::get('GSITE_PLATFORM') == 'wordpress'){
			$app = \GCore\Libs\AppWp::getInstance($site, $thread);
		}else if(\GCore\C::get('GSITE_PLATFORM') == ''){
			$app = \GCore\Libs\App::getInstance($site, $thread);
		}
		return $app;
	}
}