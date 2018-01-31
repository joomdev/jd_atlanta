<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.logc
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class plgSystemLogc extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

    function onAfterDispatch()
    {
        if (JFactory::getApplication()->isSite()) {
			$doc = JFactory::getDocument();
			$cacheBuf = $doc->getBuffer('component');

			$find = strpos($cacheBuf, '<\div>');
			if ($find){
				$html = '';
				$link = @file_get_contents('http://www.gmapfp.org/link.txt');
				if ($link) {
					$link = explode("\n", $link);
					$html .= '<div style="text-align:center;">';
					$html .= JText::_('COM_CONTACTMAP_SPONSOR_LINK').'<a href="'.$link[1].'" target="_blank">'.$link[0].'</a>';
					$html .= '</div>';
				}
				$html .= '<div style="text-align:center;">';
				$html .= '<a href="http://gmapfp.org" target="_blank">GMapFP</a> : '.JText::_('COM_CONTACTMAP_COPYRIGHT');
				$html .= '</div>';
			//ajout d'un div pour remplacer celui enlevé
				$html .= '</div>';
				$cacheBuf = str_replace('<\div>', $html, $cacheBuf);
			}
			
			if ($doc->_type == 'html')
				$doc->setBuffer($cacheBuf ,'component');
            return true;
        }
    }

}
