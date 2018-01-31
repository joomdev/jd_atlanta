<?php
/*
# News Show SP2 - News display/Slider module by JoomShaper.com
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2015 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 or later
# Websites: http://www.joomshaper.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$k2route = JPATH_SITE.'/components/com_k2/helpers/route.php';
$k2utilities = JPATH_SITE.'/components/com_k2/helpers/utilities.php';
if (file_exists($k2route))
	require_once($k2route);
	
if (file_exists($k2utilities))
	require_once($k2utilities);
	
abstract class modNSSP2K2Helper {

	public static function getList($params,$count){
	
			$catids								= $params->get('k2catids', array());
			$ordering							= $params->get('ordering', 'a.ordering');
			$ordering_direction					= $params->get('ordering_direction', 'ASC');
			$user_id							= $params->get('user_id');
			$show_featured						= $params->get('show_featured');

			$user 		= JFactory::getUser();
			$aid 		= $user->get('aid');
			$db 		= JFactory::getDBO();

			$jnow 		= JFactory::getDate();
			$now 		= $jnow->toSql();
			$nullDate 	= $db->getNullDate();

			$query = "SELECT a.*, c.name as categoryname,c.id as categoryid, c.alias as categoryalias, c.params as categoryparams".
			" FROM #__k2_items as a".
			" LEFT JOIN #__k2_categories c ON c.id = a.catid";
			$query .= " WHERE a.published = 1 AND a.access IN(".implode(',', $user->getAuthorisedViewLevels()).") AND a.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")  AND c.trash = 0";
			
			// User filter
			$userId = JFactory::getUser()->get('id');
			switch ($params->get('user_id'))
			{
				case 'by_me':
					$query .= ' AND (a.created_by = ' . (int) $userId . ' OR a.modified_by = ' . (int) $userId . ')';
					break;
				case 'not_me':
					$query .= ' AND (a.created_by <> ' . (int) $userId . ' AND a.modified_by <> ' . (int) $userId . ')';
					break;

				case '0':
					break;

				default:
					$query .= ' AND (a.created_by = ' . (int) $userId . ' OR a.modified_by = ' . (int) $userId . ')';
					break;				
			}

			//Added Category
			if (!is_null($catids)) {
				if (is_array($catids)) {
					JArrayHelper::toInteger($catids);
					$query .= " AND a.catid IN(".implode(',', $catids).")";
				} else {
					$query .= " AND a.catid=".(int)$catids;
				}
			}		
			
			//  Featured items filter
			if ($show_featured == '0')
			$query .= " AND a.featured != 1";

			if ($show_featured == '1')
			$query .= " AND a.featured = 1";

			// ensure should be published
			$query .= " AND ( a.publish_up = ".$db->Quote($nullDate)." OR a.publish_up <= ".$db->Quote($now)." )";
			$query .= " AND ( a.publish_down = ".$db->Quote($nullDate)." OR a.publish_down >= ".$db->Quote($now)." )";
			
			//Ordering
			$orderby = $ordering . ' ' . $ordering_direction; //ordering

			$query .= " ORDER BY ".$orderby;
			$db->setQuery($query, 0, $count);
			$items = $db->loadObjectList();
			
			require_once (JPATH_SITE.'/components/com_k2/models/item.php');
			$model = new K2ModelItem;
			if (count($items)) {
				foreach ($items as $item) {
				
					if (! empty($item->created_by_alias)) {
						$item->author = $item->created_by_alias;
					} else {
						$author = JFactory::getUser($item->created_by);
						$item->author = $author->name;
					}
					
					$item->created 		= $item->created;
					$item->hits 		= $item->hits;
					$item->category 	= $item->categoryname;
					$item->cat_link 	= urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid.':'.urlencode($item->categoryalias))));
					$item->image 		= self::getImage($item->id, $item->introtext);
					$item->title 		= htmlspecialchars($item->title);
					$item->introtext 	= $item->introtext;
					$item->link 		= urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias))));
					$item->comment		= '<a class="ns2-comments" href="' . $item->link . '#itemCommentsAnchor">' . JText::_('COMMENTS_TEXT') . ' (' . $model->countItemComments($item->id) . ')</a>';
					$item->rating 		= $model->getVotesPercentage($item->id);
					if ($params->get('article_extra_fields')) {
						$item->extra_fields = $model->getItemExtraFields($item->extra_fields, $item);
					}

					$rows[] = $item;
				}
				return $rows;
			}
	}
	
	//retrive k2 image
	private static function getImage($id, $text) {
		if (JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . md5("Image" . $id) . '_XL.jpg')) {
			return 'media/k2/items/cache/' . md5("Image" . $id) . '_XL.jpg';
		} else {
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $text, $matches);
			if (isset($matches[1])) {
				return $matches[1];
			}		
		}	
	}
}