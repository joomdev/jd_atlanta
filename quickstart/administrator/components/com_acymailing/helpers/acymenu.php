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

class acymenuHelper{
	function display($selected = ''){

		if(!ACYMAILING_J16){
			acymailing_addStyle(true, " #submenu-box{display:none !important;} ");
		}

		$js = "function acyToggleClass(id,myclass){
			elem = document.getElementById(id);
			if(elem.className.search(myclass) < 0){

				var elements = document.querySelectorAll('.mainelement');

				for(var i = 0; i < elements.length;i++){
					elements[i].className = elements[i].className.replace('opened','');
				}
				elem.className += ' '+myclass;
				if(myclass == 'iconsonly') sessionStorage.setItem('acyclosedmenu', '1');
			}else{
				elem.className = elem.className.replace(' '+myclass,'');
				if(myclass == 'iconsonly') sessionStorage.setItem('acyclosedmenu', '0');
			}
		}

		document.addEventListener(\"DOMContentLoaded\", function(){
			var isClosed = sessionStorage.getItem('acyclosedmenu');
			if(isClosed == 1) acyToggleClass('acyallcontent', 'iconsonly');
			setTimeout(function () {
				document.getElementById('acymainarea').style.transition = 'margin 0.4s cubic-bezier(0.00, 0.00, 1, 1.00)';
				document.getElementById('acymenu_leftside').style.transition = 'width 0.4s cubic-bezier(0.00, 0.00, 1, 1.00)';
			}, 1000);
		});

		function acyAddClass(id,myclass){
			elem = document.getElementById(id);
			if(elem.className.search(myclass)>=0) return;
			elem.className += ' '+myclass;
		}

		function acyRemoveClass(id,myclass){
			elem = document.getElementById(id);
			elem.className = elem.className.replace(' '+myclass,'');
		}
		";


		if(acymailing_isAdmin()){
			acymailing_addScript(false, ACYMAILING_JS.'acytoolbar.js?v='.filemtime(ACYMAILING_MEDIA.'js'.DS.'acytoolbar.js'));
		}

		acymailing_addScript(true, $js);

		$selected = substr($selected, 0, 5);
		if($selected == 'data' || $selected == 'data&' || $selected == 'field' || $selected == 'filte') $selected = 'subsc';
		if($selected == 'list' || $selected == 'actio') $selected = 'list';
		if($selected == 'campa' || $selected == 'templ' || $selected == 'auton' || $selected == 'notif' || $selected == 'simpl') $selected = 'newsl';
		if($selected == 'diagr') $selected = 'stats';

		$config = acymailing_config();
		$mainmenu = array();
		$submenu = array();

		if(acymailing_isAllowed($config->get('acl_cpanel_manage', 'all'))){
			$mainmenu['dashboard'] = array(acymailing_translation('ACY_CPANEL'), 'index.php?option=com_acymailing', 'acyicon-dashboard');
		}

		if(acymailing_isAllowed($config->get('acl_subscriber_manage', 'all'))){
			$mainmenu['subscriber'] = array(acymailing_translation('USERS'), 'index.php?option=com_acymailing&ctrl=subscriber', 'acyicon-user');
			$submenu['subscriber'] = array();
			$submenu['subscriber'][] = array(acymailing_translation('USERS'), 'index.php?option=com_acymailing&ctrl=subscriber', 'acyicon-user');
			if(acymailing_isAllowed($config->get('acl_subscriber_import', 'all'))) $submenu['subscriber'][] = array(acymailing_translation('IMPORT'), 'index.php?option=com_acymailing&ctrl=data&task=import', 'acyicon-import');
			if(acymailing_isAllowed($config->get('acl_subscriber_export', 'all'))) $submenu['subscriber'][] = array(acymailing_translation('ACY_EXPORT'), 'index.php?option=com_acymailing&ctrl=data&task=export', 'acyicon-export');
			if(acymailing_isAllowed($config->get('acl_configuration_manage', 'all')) && (!ACYMAILING_J16 || acymailing_authorised('core.admin', 'com_acymailing'))){
				$submenu['subscriber'][] = array(acymailing_translation('EXTRA_FIELDS'), 'index.php?option=com_acymailing&ctrl=fields', 'acyicon-custom-field');
			}
			if(acymailing_isAllowed($config->get('acl_lists_filter', 'all'))) $submenu['subscriber'][] = array(acymailing_translation('ACY_MASS_ACTIONS'), 'index.php?option=com_acymailing&ctrl=filter', 'acyicon-filter');
		}

		if(acymailing_isAllowed($config->get('acl_lists_manage', 'all'))){
			$mainmenu['list'] = array(acymailing_translation('LISTS'), 'index.php?option=com_acymailing&ctrl=list', 'acyicon-list');
			$submenu['list'] = array();
			$submenu['list'][] = array(acymailing_translation('LISTS'), 'index.php?option=com_acymailing&ctrl=list', 'acyicon-list');
			if(acymailing_isAllowed($config->get('acl_distribution_manage', 'all'))){
				$submenu['list'][] = array(acymailing_translation('ACY_DISTRIBUTION'), 'index.php?option=com_acymailing&ctrl=action', 'acyicon-distribution');
			}
		}

		if(acymailing_isAllowed($config->get('acl_newsletters_manage', 'all'))){
			$mainmenu['newsletter'] = array(acymailing_translation('NEWSLETTERS'), 'index.php?option=com_acymailing&ctrl=newsletter', 'acyicon-newsletter');
			$submenu['newsletter'] = array();
			$submenu['newsletter'][] = array(acymailing_translation('NEWSLETTERS'), 'index.php?option=com_acymailing&ctrl=newsletter', 'acyicon-newsletter');
			if(acymailing_level(2) && acymailing_isAllowed($config->get('acl_autonewsletters_manage', 'all'))){
				$submenu['newsletter'][] = array(acymailing_translation('AUTONEWSLETTERS'), 'index.php?option=com_acymailing&ctrl=autonews', 'acyicon-autonewsletter');
			}
			if(acymailing_level(3) && acymailing_isAllowed($config->get('acl_campaign_manage', 'all'))){
				$submenu['newsletter'][] = array(acymailing_translation('CAMPAIGN'), 'index.php?option=com_acymailing&ctrl=campaign', 'acyicon-campaign');
			}
			if(acymailing_level(1) && acymailing_isAllowed($config->get('acl_configuration_manage', 'all')) && (!ACYMAILING_J16 || acymailing_authorised('core.admin', 'com_acymailing'))){
				$submenu['newsletter'][] = array(acymailing_translation('JOOMLA_NOTIFICATIONS'), 'index.php?option=com_acymailing&ctrl=notification', 'acyicon-joomla');
			}
			if(acymailing_level(3) && acymailing_isAllowed($config->get('acl_simple_sending_manage', 'all'))){
				$submenu['newsletter'][] = array(acymailing_translation('SIMPLE_SENDING'), 'index.php?option=com_acymailing&ctrl=simplemail&task=edit', 'acyicon-send');
			}


			if(acymailing_isAllowed($config->get('acl_templates_manage', 'all'))) $submenu['newsletter'][] = array(acymailing_translation('ACY_TEMPLATES'), 'index.php?option=com_acymailing&ctrl=template', 'acyicon-template');
		}

		if(acymailing_isAllowed($config->get('acl_queue_manage', 'all'))) $mainmenu['queue'] = array(acymailing_translation('QUEUE'), 'index.php?option=com_acymailing&ctrl=queue', 'acyicon-queue');

		if(acymailing_isAllowed($config->get('acl_statistics_manage', 'all'))){
			$mainmenu['stats'] = array(acymailing_translation('STATISTICS'), 'index.php?option=com_acymailing&ctrl=stats', 'acyicon-statistic');
			$submenu['stats'] = array();
			$submenu['stats'][] = array(acymailing_translation('STATISTICS'), 'index.php?option=com_acymailing&ctrl=stats', 'acyicon-statistic');
			$submenu['stats'][] = array(acymailing_translation('DETAILED_STATISTICS'), 'index.php?option=com_acymailing&ctrl=stats&task=detaillisting', 'acyicon-detailed-stat');
			if(acymailing_level(1)) $submenu['stats'][] = array(acymailing_translation('CLICK_STATISTICS'), 'index.php?option=com_acymailing&ctrl=statsurl', 'acyicon-click');
			if(acymailing_level(1)) $submenu['stats'][] = array(acymailing_translation('CHARTS'), 'index.php?option=com_acymailing&ctrl=diagram', 'acyicon-chart');
		}
		if(acymailing_isAllowed($config->get('acl_configuration_manage', 'all')) && (!ACYMAILING_J16 || acymailing_authorised('core.admin', 'com_acymailing'))){
			$mainmenu['cpanel'] = array(acymailing_translation('ACY_CONFIGURATION'), 'index.php?option=com_acymailing&ctrl=cpanel', 'acyicon-configuration');
			$mainmenu['bounce'] = array(acymailing_translation('BOUNCE_HANDLING'), 'index.php?option=com_acymailing&ctrl=bounces', 'acyicon-bounce');
		}

		acymailing_addStyle(false, ACYMAILING_CSS.'acymenu.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acymenu.css'));

		$acysmsLink = '';
		if(acymailing_isAllowed($config->get('acl_configuration_manage', 'all'))) $acysmsLink = '<a class="sendother" href="index.php?option=com_acymailing&ctrl=update&task=acysms">'.acymailing_translation('ACY_SMS').'&nbsp;&nbsp;<i class="acyicon-message"></i></a>';

		$menu = '<div id="acymenu_leftside" class="donotprint acyaffix-top">';
		$menu .= '<div class="acymenu_slide"><span>'.$acysmsLink.'<i class="acyicon-open-close" onclick="acyToggleClass(\'acyallcontent\',\'iconsonly\');"></i></span></div>';
		$menu .= '<div class="acymenu_mainmenus">';
		$menu .= '<ul>';
		foreach($mainmenu as $id => $oneMenu){
			$sel = '';
			if($selected == substr($id, 0, 5)) $sel = ' sel opened';
			$menu .= '<li class="mainelement'.$sel.'" id="mainelement'.$id.'"><span onclick="acyToggleClass(\'mainelement'.$id.'\',\'opened\');"><a '.(!empty($submenu[$id]) ? 'href="#" onclick="return false;"' : 'href="'.$oneMenu[1].'"').' ><i class="'.$oneMenu[2].'"></i><span class="subtitle">'.$oneMenu[0].'</span>'.(!empty($submenu[$id]) ? '<i class="acyicon-down"></i>' : '').'</a></span>';
			if(!empty($submenu[$id])){
				$menu .= '<ul>';
				foreach($submenu[$id] as $subelement){
					$menu .= '<li class="acysubmenu" ><a class="acysubmenulink" href="'.$subelement[1].'" title="'.$subelement[0].'"><i class="'.$subelement[2].'"></i><span>'.$subelement[0].'</span></a></li>';
				}
				$menu .= '</ul>';
			}
			$menu .= '</li>';
		}
		$menu .= '<li class="mainelement" id="mainelementmyacymailing">';
		$menu .= '<div id="myacymailingarea" class="myacymailingarea">'; //DO NOT CHANGE THIS ID! we use it for ajax things...
		$menu .= $this->myacymailingarea();
		$menu .= '</div>'; //End of acymailing myacymailingarea

		$menu .= '</li>';
		$menu .= '</ul>';
		$menu .= '</div>'; //end of acymenu_mainmenus
		$menu .= '</div>'; //end of acymenu_leftside

		return $menu;
	}

	public function myacymailingarea(){
		$config = acymailing_config();
		if(!acymailing_isAllowed($config->get('acl_configuration_manage', 'all'))){
			return '';
		}
		$this->_addAjaxScript();


		$menu = '<div id="myacymailing_level">'.ACYMAILING_NAME.' '.$config->get('level').' : '.$config->get('version').'</div><div id="myacymailing_version">';

		$currentVersion = $config->get('version', '');
		$latestVersion = $config->get('latestversion', '');

		if(($currentVersion >= $latestVersion)){
			$menu .= '<div class="acyversion_uptodate myacymailingbuttons">'.acymailing_translation('ACY_LATEST_VERSION_OK').'</div>';
		}elseif(!empty($latestVersion)){
			$menu .= '<div class="acyversion_needtoupdate myacymailingbuttons"><a class="acy_updateversion" href="'.ACYMAILING_REDIRECT.'update-acymailing-'.$config->get('level').'" target="_blank"><i class="acyicon-import"></i>'.acymailing_translation_sprintf('ACY_UPDATE_NOW', $latestVersion).'</a></div>';
		}

		$menu .= '</div>';

		if(acymailing_level(1)){
			$expirationDate = $config->get('expirationdate', '');

			if(empty($expirationDate) || $expirationDate == -1){
				$menu .= '<div id="myacymailing_expiration"></div>';
			}elseif($expirationDate == -2){
				$menu .= '<div id="myacymailing_expiration"><div class="acylicence_expired"><span style="color:#c2d5f3; line-height: 16px;">'.acymailing_translation('ACY_ATTACH_LICENCE').' :</span><div><a class="acy_attachlicence myacymailingbuttons" href="'.ACYMAILING_REDIRECT.'acymailing-assign" target="_blank"><i class="acyicon-attach"></i>'.acymailing_translation('ACY_ATTACH_LICENCE_BUTTON').'</a></div></div></div>';
			}elseif($expirationDate < time()){
				$menu .= '<div id="myacymailing_expiration"><div class="acylicence_expired"><span class="acylicenceinfo">'.acymailing_translation('ACY_SUBSCRIPTION_EXPIRED').'</span><a class="acy_subscriptionexpired myacymailingbuttons" href="'.ACYMAILING_REDIRECT.'renew-acymailing-'.$config->get('level').'" target="_blank"><i class="acyicon-renew"></i>'.acymailing_translation('ACY_SUBSCRIPTION_EXPIRED_LINK').'</a></div></div>';
			}else{
				$menu .= '<div id="myacymailing_expiration"><div class="acylicence_valid myacymailingbuttons"><span class="acy_subscriptionok">'.acymailing_translation('ACY_VALID_UNTIL').' : '.acymailing_getDate($expirationDate, acymailing_translation('DATE_FORMAT_LC4')).'</span></div></div>';
			}
		}

		$menu .= '<div class="myacymailingbuttons"><button onclick="checkForNewVersion()"><i class="acyicon-search"></i>'.acymailing_translation('ACY_CHECK_MY_VERSION').'</button></div>';

		return $menu;
	}

	private function _addAjaxScript(){

		$script = "function checkForNewVersion(){
			document.getElementById('myacymailingarea').innerHTML = '<span class=\"onload spinner2\"></span>';

			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'index.php?&option=com_acymailing&ctrl=update&task=checkForNewVersion&tmpl=component');
			xhr.onload = function(){
				response = JSON.parse(xhr.responseText);
				document.getElementById('myacymailingarea').innerHTML = response.content;
			};
			xhr.send();
		}";

		$config = acymailing_config();
		$lastlicensecheck = $config->get('lastlicensecheck', '');
		if(empty($lastlicensecheck) || $lastlicensecheck < (time() - 604800)){
			$script .= 'window.addEventListener("load", function(){
				checkForNewVersion();
			});';
		}

		acymailing_addScript(true, $script);
	}
}
