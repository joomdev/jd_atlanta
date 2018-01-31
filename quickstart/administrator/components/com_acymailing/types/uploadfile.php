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

class uploadfileType{
	function display($picture, $map, $value, $mapdelete = ''){
		if(!$picture){
			$result = '<input type="hidden" name="'.$map.'[]" id="'.$map.$value.'" />';
			$result .= acymailing_popup('index.php?option=com_acymailing&ctrl='.(acymailing_isAdmin() ? '' : 'front').'file&task=select&id='.$map.$value.'&tmpl=component', acymailing_translation('SELECT'), 'acyupload acymailing_button_grey', 850, 600);
			$result .= '<span id="'.$map.$value.'selection"></span>';
			return $result;
		}

		$result = '<input type="hidden" name="'.$mapdelete.'" id="'.$map.'" />';
		$result .= acymailing_popup('index.php?option=com_acymailing&ctrl='.(acymailing_isAdmin() ? '' : 'front').'file&task=select&id='.$map.'&tmpl=component', acymailing_translation('SELECT'), 'acyupload acymailing_button_grey', 850, 600);

		if(empty($value)) $value = 'media/com_acymailing/images/emptyimg.png';
		$result .= '<img id="'.$map.'preview" src="'.ACYMAILING_LIVE.$value.'" style="float:left;max-height:50px;margin-right:10px;" />
		<br /><input type="checkbox" name="'.$mapdelete.'" value="delete" id="delete'.$map.'" /> <label for="delete'.$map.'">'.acymailing_translation('DELETE_PICT').'</label>';

		return $result;
	}
}
