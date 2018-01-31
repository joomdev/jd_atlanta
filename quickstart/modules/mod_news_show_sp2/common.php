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

jimport('joomla.filter.output');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.image.image.php');
require_once dirname(__FILE__) . '/image.php';

class modNSSP2CommonHelper {

	public static function cText($text, $limit, $type=0) {//function to cut text
		$text 					= preg_replace('/<img[^>]+\>/i', "", $text);
		if ($limit==0) {//no limit
			$allowed_tags 		= '<b><i><a><small><h1><h2><h3><h4><h5><h6><sup><sub><em><strong><u><br>';
			$text 				= strip_tags( $text, $allowed_tags );
			$text 				= $text;	
		} else {
			if ($type==1) {//character lmit
				$text 			= self::characterLimit($text, $limit, '...');
			} else {//word limit
				$text 			= self::wordLimit($text, $limit, '...');
			}		
		}
		return $text;
	}

	// Word limit
	public static function wordLimit($str, $limit = 100, $end_char = '&#8230;')
	{
		if (JString::trim($str) == '')
			return $str;

		// always strip tags for text
		$str = strip_tags($str);

		$find = array("/\r|\n/u", "/\t/u", "/\s\s+/u");
		$replace = array(" ", " ", " ");
		$str = preg_replace($find, $replace, $str);

		preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/u', $str, $matches);
		if (JString::strlen($matches[0]) == JString::strlen($str))
			$end_char = '';
		return JString::rtrim($matches[0]).$end_char;
	}

	// Character limit
	public static function characterLimit($str, $limit = 150, $end_char = '...')
	{
		if (JString::trim($str) == '')
			return $str;

		// always strip tags for text
		$str = strip_tags(JString::trim($str));

		$find = array("/\r|\n/u", "/\t/u", "/\s\s+/u");
		$replace = array(" ", " ", " ");
		$str = preg_replace($find, $replace, $str);

		if (JString::strlen($str) > $limit)
		{
			$str = JString::substr($str, 0, $limit);
			return JString::rtrim($str).$end_char;
		}
		else
		{
			return $str;
		}

	}
	
	public static function thumb($image, $width, $height, $ratio=false, $uniqid) {

		if( substr($image,0,4)=='http' ) {//to detect externel image source
			if(strpos($image, JURI::base())===FALSE) {//externel source
				return $image;
			} else {//return internel image relative path
				$image = str_replace(JURI::base(),'',$image);
			}
		}
		
		// remove any / that begins the path
		$image = ltrim($image,'/');

		$image = JPATH_ROOT . '/' . $image;
		
		//cache path
		$thumb_dir = JPATH_CACHE.'/mod_news_show_sp2/nssp2_thumbs/'. $uniqid;
		
		if (!JFolder::exists($thumb_dir)) {
			JFolder::create($thumb_dir, 0755);
		}

		$file_name 			= JFile::stripExt(basename($image));
		$file_ext 			= JFile::getExt($image);
		$thumb_file_name 	= $thumb_dir . '/' . $file_name . '.' . $file_ext;
		$thumb_url 			= basename(JPATH_CACHE) .'/mod_news_show_sp2/nssp2_thumbs/'. $uniqid. '/' . $file_name . "_{$width}x{$height}." . $file_ext;

		//Creating thumbnails		
		if ( file_exists($image) ) {
			self::crop($image, $width, $height, $ratio, $thumb_dir, $thumb_file_name);
		}


			
		return $thumb_url;	
	}

	private static function crop($image_to_resize, $width, $height, $ratio, $thumbs_path, $thumb_file)
	{
		
		$sizes = array("{$width}x{$height}");

		$image = new modNSSP2ImageHelper( $image_to_resize );
		//$output = $image->createThumbs($sizes, 1, $thumbs_path);
		
		if( file_exists( $thumb_file ) )
		{
			$imageProperties = modNSSP2ImageHelper::getImageFileProperties( $thumb_file );

			if( $imageProperties->width != $width || $imageProperties->height != $height )
			{
				//$image = new JImage( $image_to_resize );
				$output = $image->createThumbs($sizes, 1, $thumbs_path);
			}

		} else {
			//$image = new JImage( $image_to_resize );
			$output = $image->createThumbs($sizes, 1, $thumbs_path);
		}

		return true;

	}
		
}