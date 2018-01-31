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

class FilterViewFilter extends acymailingView{

	var $chosen = false;

	function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function form(){

		$config = acymailing_config();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();

		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase.".filter_order", 'filter_order', 'name', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase.".filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));


		$pageInfo->limit->value = acymailing_getUserVar($paramBase.'.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$db = JFactory::getDBO();

		if(acymailing_getVar('none', 'task') == 'filterDisplayUsers'){
			$action = array();
			$action['type'] = array('displayUsers');
			$action[] = array('displayUsers' => array());

			$filterClass = acymailing_get('class.filter');
			$filterClass->subid = acymailing_getVar('string', 'subid');
			$filterClass->execute(acymailing_getVar('none', 'filter'), $action);

			if(!empty($filterClass->report)){
				$this->filteredUsers = $filterClass->report[0];
			}
		}


		$filid = acymailing_getCID('filid');

		$filterClass = acymailing_get('class.filter');
		$testFilter = acymailing_getVar('none', 'filter');
		if(!empty($filid) && empty($testFilter)){
			$filter = $filterClass->get($filid);
		}else{
			$filter = new stdClass();
			$filter->action = acymailing_getVar('none', 'action');
			$filter->filter = acymailing_getVar('none', 'filter');
			$filter->published = 1;
		}

		acymailing_importPlugin('acymailing');

		$typesFilters = array();
		$typesActions = array();

		$outputFilters = implode('', acymailing_trigger('onAcyDisplayFilters', array(&$typesFilters, 'massactions')));
		$outputActions = implode('', acymailing_trigger('onAcyDisplayActions', array(&$typesActions)));

		$typevaluesFilters = array();
		$typevaluesActions = array();
		$typevaluesFilters[] = acymailing_selectOption('', acymailing_translation('FILTER_SELECT'));
		$typevaluesActions[] = acymailing_selectOption('', acymailing_translation('ACTION_SELECT'));
		foreach($typesFilters as $oneType => $oneName){
			$typevaluesFilters[] = acymailing_selectOption($oneType, $oneName);
		}
		foreach($typesActions as $oneType => $oneName){
			$typevaluesActions[] = acymailing_selectOption($oneType, $oneName);
		}

		$js = "function updateAction(actionNum){
				var actiontype = window.document.getElementById('actiontype'+actionNum);
				if(actiontype == 'undefined' || actiontype == null) return;
				currentActionType = actiontype.value;
				if(!currentActionType){
					window.document.getElementById('actionarea_'+actionNum).innerHTML = '';
					return;
				}
				actionArea = 'action__num__'+currentActionType;
				window.document.getElementById('actionarea_'+actionNum).innerHTML = window.document.getElementById(actionArea).innerHTML.replace(/__num__/g,actionNum);
				if(typeof(window['onAcyDisplayAction_'+currentActionType]) == 'function') {
					try{ window['onAcyDisplayAction_'+currentActionType](actionNum); }catch(e){alert('Error in the onAcyDisplayAction_'+currentActionType+' function : '+e); }
				}

			}";

		$js .= "var numActions = 0;
				function addAction(){
					var newdiv = document.createElement('div');
					newdiv.id = 'action'+numActions;
					newdiv.className = 'plugarea';
					newdiv.innerHTML = document.getElementById('actions_original').innerHTML.replace(/__num__/g, numActions);
					var allactions = document.getElementById('allactions');
					if(allactions != 'undefined' && allactions != null) allactions.appendChild(newdiv); updateAction(numActions); numActions++;
				}";

		$js .= "document.addEventListener(\"DOMContentLoaded\", function(){ addAcyFilter(); addAction(); });";

		if(!ACYMAILING_J16){
			$js .= 'function submitbutton(pressbutton){
						if (pressbutton != \'save\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$js .= 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton != \'save\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}
		if(ACYMAILING_J30){
			$js .= "if(window.document.getElementById('filterinfo').style.display == 'none'){
						window.document.getElementById('filterinfo').style.display = 'block';
						return false;}
					if(window.document.getElementById('title').value.length < 2){alert('".acymailing_translation('ENTER_TITLE', true)."'); return false;}";
		}else{
			$js .= "if(window.document.getElementById('filterinfo').style.display == 'none'){
						window.document.getElementById('filterinfo').style.display = 'block';
						return false;}
					if(window.document.getElementById('title').value.length < 2){alert('".acymailing_translation('ENTER_TITLE', true)."'); return false;}";
		}
		if(!ACYMAILING_J16){
			$js .= "submitform( pressbutton );} ";
		}else{
			$js .= "Joomla.submitform(pressbutton,document.adminForm);}; ";
		}

		acymailing_addScript(true, $js);

		$filterClass->addJSFilterFunctions();

		$js = '';
		$data = array('addAction' => 'action', 'addAcyFilter' => 'filter');
		foreach($data as $jsFunction => $datatype){
			if(empty($filter->$datatype)) continue;
			foreach($filter->{$datatype}['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$js .= "while(!document.getElementById('".$datatype."type$num')){".$jsFunction."();}
						document.getElementById('".$datatype."type$num').value= '$oneType';
						update".ucfirst($datatype)."($num);";
				if(empty($filter->{$datatype}[$num][$oneType])) continue;
				foreach($filter->{$datatype}[$num][$oneType] as $key => $value){
					if(is_array($value)){
						$js .= "try{";
						foreach($value as $subkey => $subval){
							$js .= "document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].value = '".addslashes(str_replace(array("\n", "\r"), ' ', $subval))."';";
							$js .= "if(document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].type && document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].type == 'checkbox'){ document.adminForm.elements['".$datatype."[$num][$oneType][$key][$subkey]'].checked = 'checked'; }";
						}
						$js .= "}catch(e){}";
					}
					$myVal = is_array($value) ? implode(',', $value) : $value;
					$js .= "try{";
					$js .= "document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].value = '".addslashes(str_replace(array("\n", "\r"), ' ', $myVal))."';";
					$js .= "if(document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].type && document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].type == 'checkbox'){ document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].checked = 'checked'; }";
					$js .= "}catch(e){}";
				}

				$js .= "\n"." if(typeof(onAcyDisplay".ucfirst($datatype)."_".$oneType.") == 'function'){
					try{ onAcyDisplay".ucfirst($datatype)."_".$oneType."($num); }catch(e){alert('Error in the onAcyDisplay".ucfirst($datatype)."_".$oneType." function : '+e); }
				}";

				if($datatype == 'filter') $js .= " countresults($num);";
			}
		}

		$listid = acymailing_getVar('int', 'listid');
		if(!empty($listid)){
			$js .= "document.getElementById('actiontype0').value = 'list'; updateAction(0); document.adminForm.elements['action[0][list][selectedlist]'].value = '".$listid."';";
		}

		acymailing_addScript(true, "document.addEventListener(\"DOMContentLoaded\", function(){ $js });");

		$triggers = array();
		$triggers['daycron'] = acymailing_translation('AUTO_CRON_FILTER');

		if(empty($filter->daycron)){
			$nextDate = $config->get('cron_plugins_next');
		}else{
			$nextDate = $filter->daycron;
		}

		$listHours = array();
		$listMinutess = array();
		for($i = 0; $i < 24; $i++){
			$listHours[] = acymailing_selectOption($i, ($i < 10 ? '0'.$i : $i));
		}
		$hours = acymailing_select($listHours, 'triggerhours', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', acymailing_getDate($nextDate, 'H'));
		for($i = 0; $i < 60; $i += 5){
			$listMinutess[] = acymailing_selectOption($i, ($i < 10 ? '0'.$i : $i));
		}
		$defaultMin = floor(acymailing_getDate($nextDate, 'i') / 5) * 5;
		$minutes = acymailing_select($listMinutess, 'triggerminutes', 'class="inputbox" size="1" style="width:60px;"', 'value', 'text', $defaultMin);
		$this->hours = $hours;
		$this->minutes = $minutes;

		$this->nextDate = !empty($nextDate) ? ' ('.acymailing_translation('NEXT_RUN').' : '.acymailing_getDate($nextDate, '%d %B %Y  %H:%M').')' : '';

		$triggers['allcron'] = acymailing_translation('ACY_EACH_TIME');
		$triggers['subcreate'] = acymailing_translation('ON_USER_CREATE');
		$triggers['subchange'] = acymailing_translation('ON_USER_CHANGE');
		acymailing_trigger('onAcyDisplayTriggers', array(&$triggers));

		$name = empty($filter->name) ? '' : ' : '.$filter->name;

		if(acymailing_getVar('cmd', 'tmpl', '') != 'component'){
			$acyToolbar = acymailing_get('helper.toolbar');
			$acyToolbar->custom('filterDisplayUsers', acymailing_translation('FILTER_VIEW_USERS'), 'user', false, '');
			$acyToolbar->custom('process', acymailing_translation('PROCESS'), 'process', false, '');
			$acyToolbar->divider();
			if(acymailing_level(3)){
				$acyToolbar->save();
				if(!empty($filter->filid)) $acyToolbar->link(acymailing_completeLink('filter&task=edit&filid=0'), acymailing_translation('ACY_NEW'), 'new');
			}
			$acyToolbar->link(acymailing_completeLink('dashboard'), acymailing_translation('ACY_CLOSE'), 'cancel');
			$acyToolbar->divider();
			$acyToolbar->help('filter');
			$acyToolbar->setTitle(acymailing_translation('ACY_MASS_ACTIONS').$name, 'filter&task=edit&filid='.$filid);
			$acyToolbar->display();
		}else{
			acymailing_setPageTitle(acymailing_translation('ACY_MASS_ACTIONS').$name);
		}

		$subid = acymailing_getVar('string', 'subid');
		if(!empty($subid)){
			$subArray = explode(',', trim($subid, ','));
			acymailing_arrayToInteger($subArray);

			$db->setQuery('SELECT `name`,`email` FROM `#__acymailing_subscriber` WHERE `subid` IN ('.implode(',', $subArray).')');
			$users = $db->loadObjectList();
			if(!empty($users)){
				$this->users = $users;
				$this->subid = $subid;
			}
		}

		$this->typevaluesFilters = $typevaluesFilters;
		$this->typevaluesActions = $typevaluesActions;
		$this->outputFilters = $outputFilters;
		$this->outputActions = $outputActions;
		$this->filter = $filter;
		$this->pageInfo = $pageInfo;

		$this->triggers = $triggers;
		if(acymailing_getVar('cmd', 'tmpl') == 'component'){
			acymailing_addStyle(false, ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));
		}

		if(acymailing_level(3) AND acymailing_getVar('cmd', 'tmpl') != 'component'){
			$query = 'SELECT * FROM '.acymailing_table('filter');

			if(!empty($pageInfo->search)){
				$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
				$query .= ' WHERE LOWER(name) LIKE'.$searchVal;
			}

			if(!empty($pageInfo->filter->order->value) && (($pageInfo->filter->order->value === "name") || ($pageInfo->filter->order->value === "filid"))){
				$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
			}

			$db->setQuery($query);
			$filters = $db->loadObjectList();

			$toggleClass = acymailing_get('helper.toggle');
			$this->toggleClass = $toggleClass;
			$this->filters = $filters;
		}
	}
}
