<?php
/**
* @package Helix Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

function modChrome_sp_xhtml($module, $params, $attribs) {
	$wrap_tag = 'span';
	$html_title = preg_replace("~\W\w+\s*$~", '<'.$wrap_tag.'>'.'\\0'.'</'.$wrap_tag.'>', $module->title);
	$moduleTag     = $params->get('module_tag', 'div');
	$bootstrapSize = (int) $params->get('bootstrap_size', 0);
	$moduleClass   = $bootstrapSize != 0 ? ' col-sm-' . $bootstrapSize : '';
	$headerTag     = htmlspecialchars($params->get('header_tag', 'h3'));
	$headerClass   = htmlspecialchars($params->get('header_class', 'sp-module-title'));
	
	if ($module->content) {
		echo '<' . $moduleTag . ' class="sp-module ' . htmlspecialchars($params->get('moduleclass_sfx')) . $moduleClass . '">';

			if ($module->showtitle)
			{
				echo '<' . $headerTag . ' class="' . $headerClass . '">' . $html_title . '</' . $headerTag . '>';
			}

			echo '<div class="sp-module-content">';
			echo $module->content;
			echo '</div>';

		echo '</' . $moduleTag . '>';
	}
}