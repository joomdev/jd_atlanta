<?php
/*
# News Show SP2 - News display/Slider module by JoomShaper.com
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2015 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 or later
# Websites: http://www.joomshaper.com
*/

if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.'/administrator/components/com_virtuemart/helpers/config.php');

VmConfig::loadConfig ();

// Load the language file of com_virtuemart.
VmConfig::loadJLang('com_virtuemart',true);
if (!class_exists ('calculationHelper')) {
	require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/calculationh.php');
}
if (!class_exists ('CurrencyDisplay')) {
	require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/currencydisplay.php');
}
if (!class_exists ('VirtueMartModelVendor')) {
	require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/vendor.php');
}
if (!class_exists ('VmImage')) {
	require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/image.php');
}
if (!class_exists ('shopFunctionsF')) {
	require(JPATH_SITE . '/components/com_virtuemart/helpers/shopfunctionsf.php');
}
if (!class_exists ('calculationHelper')) {
	require(JPATH_COMPONENT_SITE . '/helpers/cart.php');
}
if (!class_exists ('VirtueMartModelProduct')) {
	JLoader::import ('product', JPATH_ADMINISTRATOR . '/components/com_virtuemart/' . DS . 'models');
}

if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/vmmodel.php');

	
abstract class modNSSP2VMHelper {

	public static function getList($params,$count){

			$productModel = VmModel::getModel('Product');
			$products = $productModel->getProductListing($params->get('vmordering','latest'), $count, true, true, false, true, $params->get('vmcat',NULL));
			$productModel->addImages($products);
			$currency = CurrencyDisplay::getInstance( );
			
			
			if (count($products)) {
				foreach ($products as $item) {
					$author 			= JFactory::getUser($item->created_by);
					$item->created 		= $item->created_on;
					$item->author 		= $author->name;
					$item->hits 		= @$item->hits;
					$item->category 	= $item->category_name;
					$item->cat_link 	= JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='. $item->virtuemart_category_id);
					$item->image 		= $item->images[0]->file_url;
					$item->title 		= $item->product_name;
					$item->introtext 	= $item->product_s_desc;
					$item->price 		= round($item->prices['salesPrice'],2) . $currency->getSymbol();
					$item->addtocart 	= self::addtocart($item);
					$item->rating 		= self::getRating($item->virtuemart_product_id);
					$item->link 		= JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$item->virtuemart_product_id.'&virtuemart_category_id='.$item->virtuemart_category_id);
					$rows[] = $item;
				}
				return $rows;
			}				
			
	}	
	
	private static function addtocart($product) {
		$output = '';
		ob_start();
        if (!VmConfig::get ('use_as_catalog', 0)) {	?>
                <div class="ns2-addtocart">

				<form method="post" class="product" action="index.php">
					<input type="hidden" class="quantity-input" name="quantity[]" value="1" />
					<?php
					$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
					$button_cls = ''; 
					// Display the add to cart button
					$stockhandle = VmConfig::get('stockhandle','none');
					if(($stockhandle=='disableit' or $stockhandle=='disableadd') and ($product->product_in_stock - $product->product_ordered)<1){
						$button_lbl = JText::_('COM_VIRTUEMART_CART_NOTIFY');
						$button_cls = 'notify-button';
						$button_name = 'notifycustomer';
					}
					?>
					<?php // Display the add to cart button ?>
					<input type="submit" name="addtocart"  class="addtocart-button" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
                    <div class="clear"></div>
                    <input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="view" value="cart" />
                    <noscript><input type="hidden" name="task" value="add" /></noscript>
                    <input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
                    <input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
                </form>
				<div class="clear"></div>
            </div>
        <?php }
		$output = ob_get_clean();
		return $output;			
    }	
	 
	 /*Virtuemart Product Rating*/
	private static function getRating ($product_id) {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__virtuemart_ratings WHERE virtuemart_product_id={$product_id}";
		$db->setQuery($query);
		$item = $db->loadObject();
		if (count($item)==1) {
			$rating = number_format(intval($item->rates) / intval($item->ratingcount), 2) * 20;	
		} else {
			$rating = 0;
		}
		return $rating;
	}
}