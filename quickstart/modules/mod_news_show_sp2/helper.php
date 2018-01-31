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

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
jimport( 'joomla.plugin.helper');
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');

abstract class modNSSP2JHelper
{
	public static function getList($params,$count){
		
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();

		//Parameters
		$catids								= $params->get('catids', array());
		
		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $count);
		$model->setState('filter.published', 1);
		
		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);		
		
		//sp comments
		if (JPluginHelper::isEnabled('content', 'spcomments')) {
			$plgname 	= JPluginHelper::getPlugin('content', 'spcomments');
			$plgParams 	= json_decode($plgname->params);
		}
		//sp comments
		
		// Category filter
		$model->setState('filter.category_id', $catids);
		
		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}


		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());		

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$model->setState('filter.featured', 'only');
				break;
			case '0':
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}

		$ordering 			= $params->get('ordering', 'a.ordering');
		$ordering_direction	= $params->get('ordering_direction', 'ASC');

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $ordering_direction);

		$items 				= $model->getItems();
		
		foreach ($items as &$item) {
			$item->slug 		= $item->id.':'.$item->alias;
			$item->catslug 		= $item->catid.':'.$item->category_alias;
			$author 			= JFactory::getUser($item->created_by);
			$item->author 		= ($item->created_by_alias) ? $item->created_by_alias : $author->name;
			$item->created 		= $item->created;
			$item->hits 		= $item->hits;
			$item->category 	= $item->category_title;
			$item->cat_link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
			$item->image 		= self::getImage($item->introtext,$item->images);
			$item->title 		= htmlspecialchars($item->title);
			$item->introtext 	= JHtml::_('content.prepare', $item->introtext);
			$item->link 		= JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));

			if (JPluginHelper::isEnabled('content', 'spcomments'))
			{
				$item->comment 	= self::getComment($item->link, $item->catid, $plgParams);
			} 
			else
			{
				$item->comment 	= '<a class="ns2-comments" href="#">0 Comment</a>';
			}
			
			$item->rating 		= ($item->rating) ? number_format(intval($item->rating)/intval($item->rating_count), 2)*20 : 0;
		}	
		return $items;
		
	}
	
	private static function getImage($text, $image_src="") {
		$image_src = json_decode($image_src);		
		if (JVERSION>=2.5 && @$image_src->image_intro) {
			return $image_src->image_intro;
		} elseif (JVERSION>=2.5 && @$image_src->image_fulltext) {
			return $image_src->image_fulltext;
		} else {
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $text, $matches);	
			if (isset($matches[1])) {
				return $matches[1];
			}			
		}
	}
	
	//function to retrive comment from sp comments plugin
	private static function getComment($url, $catid, $params) {
		if (JPluginHelper::isEnabled('content', 'spcomments')) {
			if (in_array($catid, $params->catids)) {	
				//identifier
				$post_id			=substr(JURI::base(), 0, -1)."/".strstr($url, 'index.php');
				$identifier			= md5($post_id);
				//params
				$commenting_engine 	= $params->commenting_engine;
				$disqus_subdomain	= $params->disqus_subdomain;
				$disqus_devmode		= $params->disqus_devmode;
				$disqus_lang		= $params->disqus_lang;
				$intensedebate_acc	= $params->intensedebate_acc;
				$fb_appID			= $params->fb_appID;
				$fb_lang			= $params->fb_lang;
				
				if ($commenting_engine=="disqus") {//if disquss
					$link = '<a class="ns2-comments" href="' . $url . '#disqus_thread" data-disqus-identifier="' . $identifier . '"></a>';
				} else if ($commenting_engine=="intensedebate") {//intenseDebate
					$link = '<span class="containerCountComment">
							<script type="text/javascript">
							//<![CDATA[
									var idcomments_acct = "' . $intensedebate_acc . '";
									var idcomments_post_id = "' . $identifier . '";
									var idcomments_post_url = encodeURIComponent("' . $post_id . '");
							//]]>
							</script>
							<script type="text/javascript" src="http://www.intensedebate.com/js/genericLinkWrapperV2.js"></script>
					</span>';
				} else {//facebook
					$link = "<a class=\"ns2-comments\" href='$url'>Comments (<fb:comments-count href='$url'></fb:comments-count>)</a>";
				}
				return $link;
			}	
		}
	}
}