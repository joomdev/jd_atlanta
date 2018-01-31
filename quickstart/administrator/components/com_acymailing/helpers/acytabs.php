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

class acytabsHelper{
	var $ctrl = 'tabs';
	var $tabs = null;
	var $openPanel = false;
	var $mode = null;
	var $data = array();
	var $options = null;
	var $name = '';

	function __construct(){
		if(!ACYMAILING_J16){
			$this->mode = 'pane';
		}elseif(!ACYMAILING_J30){
			$this->mode = 'pane';
		}else{
			$this->mode = 'bootstrap';
		}
	}

	function startPane($name){
		return $this->start($name);
	}

	function startPanel($text, $id){
		return $this->panel($text, $id);
	}

	function endPanel(){
		return '';
	}

	function endPane(){
		return $this->end();
	}

	function setOptions($options = array()){
		if($this->options == null){
			$this->options = $options;
		}else{
			$this->options = array_merge($this->options, $options);
		}
	}

	function start($name, $options = array()){
		$this->name = $name;
		if($this->options == null){
			$this->options = $options;
		}else{
			$this->options = array_merge($this->options, $options);
		}
		return '';
	}

	function panel($text, $id){
		if($this->openPanel){
			$this->_closePanel();
		}

		$obj = new stdClass();
		$obj->text = $text;
		$obj->id = $id;
		$obj->data = '';
		$this->data[] = $obj;
		ob_start();
		$this->openPanel = true;
		return '';
	}

	function _closePanel(){
		if(!$this->openPanel){
			return;
		}
		$panel = end($this->data);
		$panel->data .= ob_get_clean();
		$this->openPanel = false;
	}

	function end(){
		$ret = '';
		static $jsInit = false;

		if($this->openPanel){
			$this->_closePanel();
		}

		$ret .= '<div style="margin-left:10px;"><ul class="nav nav-tabs" id="'.$this->name.'" style="width:100%;">'."\r\n";
		foreach($this->data as $k => $data){
			$active = '';
			if($k == 0) $active = ' class="active"';
			$ret .= '	<li'.$active.'><a href="#'.$data->id.'" id="'.$data->id.'_tablink" onclick="toggleTab(\''.$this->name.'\', \''.$data->id.'\');return false;">'.acymailing_translation($data->text).'</a></li>'."\r\n";
		}
		$ret .= '</ul>'."\r\n".'<div class="tab-content" id="'.$this->name.'_content">'."\r\n";
		foreach($this->data as $k => $data){
			$active = '';
			if($k == 0) $active = ' active';
			$ret .= '	<div class="tab-pane'.$active.'" id="'.$data->id.'">'."\r\n".$data->data."\r\n".'	</div>'."\r\n";
			unset($data->data);
		}
		$ret .= '</div></div>';
		unset($this->data);

		if(!$jsInit){
			$jsInit = true;
			$js = '
			function toggleTab(group, id){
				var contentTabs = document.querySelectorAll("#"+group+"_content > div");
				for (i = 0; i < contentTabs.length; i++) {
					contentTabs[i].className = contentTabs[i].className.replace("active", "");
				}
				document.getElementById(id).className += " active";
				var groupTabs = document.querySelectorAll("#"+group+" > li");
				for (i = 0; i < groupTabs.length; i++) {
					groupTabs[i].className = groupTabs[i].className.replace("active", "");
				}
				document.getElementById(id+"_tablink").parentElement.className += " active";

			}';
			acymailing_addScript(true, $js);
		}
		return $ret;
	}
}
