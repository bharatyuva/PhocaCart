<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

$layoutC 	= new JLayoutFile('button_compare', null, array('component' => 'com_phocacart'));
$layoutW 	= new JLayoutFile('button_wishlist', null, array('component' => 'com_phocacart'));
$layoutQVB 	= new JLayoutFile('button_quickview', null, array('component' => 'com_phocacart'));
$layoutS	= new JLayoutFile('product_stock', null, array('component' => 'com_phocacart'));
$layoutPOQ	= new JLayoutFile('product_order_quantity', null, array('component' => 'com_phocacart'));
$layoutR	= new JLayoutFile('product_rating', null, array('component' => 'com_phocacart'));
$layoutAI	= new JLayoutFile('button_add_to_cart_icon', null, array('component' => 'com_phocacart'));
$layoutIL	= new JLayoutFile('items_list', null, array('component' => 'com_phocacart'));
$layoutIGL	= new JLayoutFile('items_gridlist', null, array('component' => 'com_phocacart'));
$layoutIG	= new JLayoutFile('items_grid', null, array('component' => 'com_phocacart'));
$layoutAAQ	= new JLayoutFile('popup_container_iframe', null, array('component' => 'com_phocacart'));

// HEADER - NOT AJAX
if (!$this->t['ajax']) {
	echo '<div id="ph-pc-category-box" class="pc-category-view'.$this->p->get( 'pageclass_sfx' ).'">';
	$c = isset($this->t['categories']) ? count($this->t['categories']) : 0;
	echo $this->loadTemplate('header');
	echo $this->loadTemplate('subcategories');
	echo $this->loadTemplate('pagination_top');
	echo '<div id="phItemsBox">';
}


// ITEMS

if (!empty($this->items)) {

	$price			= new PhocacartPrice;
	$col 			= PhocacartRenderFront::getColumnClass((int)$this->t['columns_cat']);
	$lt				= $this->t['layouttype'];
	$i				= 1; // Not equal Heights

	echo '<div id="phItems" class="ph-items '.$lt.'">';
	echo '<div class="'.PhocacartRenderFront::completeClass(array($this->s['c']['row'], $this->t['class_row_flex'], $this->t['class_lazyload'], $lt)).'">';

	foreach ($this->items as $v) {

		$label 				= PhocacartRenderFront::getLabel($v->date, $v->sales, $v->featured);
		$link 				= JRoute::_(PhocacartRoute::getItemRoute($v->id, $v->catid, $v->alias, $v->catalias));

		// Image data
		$attributesOptions 	= $this->t['hide_attributes_category'] == 0 ? PhocacartAttribute::getAttributesAndOptions((int)$v->id) : array();

		if (!isset($v->additional_image)) { $v->additional_image = '';}
		$image = PhocacartImage::getImageDisplay($v->image, $v->additional_image, $this->t['pathitem'], $this->t['switch_image_category_items'], $this->t['image_width_cat'], $this->t['image_height_cat'], '', $lt, $attributesOptions);

		// :L: IMAGE
		$dI	= array();
		if (isset($image['image']->rel) && $image['image']->rel != '') {
			$dI['t']				= $this->t;
			$dI['s']				= $this->s;
			$dI['product_id']		= (int)$v->id;
			$dI['layouttype']		= $lt;
			$dI['title']			= $v->title;
			$dI['image']			= $image;
			$dI['typeview']			= 'Category';
		}

		// :L: COMPARE
		$icon 				= array();
		$icon['compare'] 	= '';
		if ($this->t['display_compare'] == 1) {
			$d					= array();
			$d['s']				= $this->s;
			$d['linkc']			= $this->t['linkcomparison'];
			$d['id']			= (int)$v->id;
			$d['catid']			= $this->t['categoryid'];
			$d['return']		= $this->t['actionbase64'];
			$d['method']		= $this->t['add_compare_method'];
			$icon['compare'] 	= $layoutC->render($d);
		}

		// :L: WISHLIST
		$icon['wishlist'] = '';
		if ($this->t['display_wishlist'] == 1) {
			$d					= array();
			$d['s']				= $this->s;
			$d['linkw']			= $this->t['linkwishlist'];
			$d['id']			= (int)$v->id;
			$d['catid']			= $this->t['categoryid'];
			$d['return']		= $this->t['actionbase64'];
			$d['method']		= $this->t['add_wishlist_method'];
			$icon['wishlist'] 	= $layoutW->render($d);
		}

		// :L: QUICKVIEW
		$icon['quickview'] = '';
		if ($this->t['display_quickview'] == 1) {
			$d					= array();
			$d['s']				= $this->s;
			$d['linkqvb']		= JRoute::_(PhocacartRoute::getItemRoute($v->id, $v->catid, $v->alias, $v->catalias));
			$d['id']			= (int)$v->id;
			$d['catid']			= $this->t['categoryid'];
			$d['return']		= $this->t['actionbase64'];
			$icon['quickview'] 	= $layoutQVB->render($d);
		}

		// :L: PRICE
		$dP = array();
		$dP['type'] = $v->type;// PRODUCTTYPE

		if ($this->t['can_display_price']) {
			$dP['priceitems']	= $price->getPriceItems($v->price, $v->taxid, $v->taxrate, $v->taxcalculationtype, $v->taxtitle, $v->unit_amount, $v->unit_unit, 1, 1, $v->group_price);

			$price->getPriceItemsChangedByAttributes($dP['priceitems'], $attributesOptions, $price, $v);
			$dP['priceitemsorig']= array();
			if ($v->price_original != '' && $v->price_original > 0) {
				$dP['priceitemsorig'] = $price->getPriceItems($v->price_original, $v->taxid, $v->taxrate, $v->taxcalculationtype);
			}
			//$dP['class']		= 'ph-category-price-box '.$lt;
			$dP['class']		= 'ph-category-price-box';// Cannot be dynamic as can change per ajax - this can cause jumping of boxes
			$dP['product_id']	= (int)$v->id;
			$dP['typeview']		= 'Category';


			// Display discount price
			// Move standard prices to new variable (product price -> product discount)
			$dP['priceitemsdiscount']		= $dP['priceitems'];
			$dP['discount'] 				= PhocacartDiscountProduct::getProductDiscountPrice($v->id, $dP['priceitemsdiscount']);

			// Display cart discount (global discount) in product views - under specific conditions only
			// Move product discount prices to new variable (product price -> product discount -> product discount cart)
			$dP['priceitemsdiscountcart']	= $dP['priceitemsdiscount'];
			$dP['discountcart']				= PhocacartDiscountCart::getCartDiscountPriceForProduct($v->id, $v->catid, $dP['priceitemsdiscountcart']);

			$dP['zero_price']		= 1;// Apply zero price if possible
		}

		// :L: LINK TO PRODUCT VIEW
		$dV = array();
		$dV['s'] = $this->s;
		if ((int)$this->t['display_view_product_button'] > 0) {
			$dV['link']							= $link;
			$dV['display_view_product_button'] 	= $this->t['display_view_product_button'];
		}

		// :L: ADD TO CART
		$dA = $dA2 = $dA3 = $dAb = $dF = array();
		$icon['addtocart'] = '';

		// STOCK ===================================================
		// Set stock: product, variations, or advanced stock status
		$dSO 				= '';
		$dA['class_btn']	= '';
		$dA['class_icon']	= '';
		if ($this->t['display_stock_status'] == 2 || $this->t['display_stock_status'] == 3) {

			$stockStatus 				= array();
			$stock 						= PhocacartStock::getStockItemsChangedByAttributes($stockStatus, $attributesOptions, $v);

			if ($this->t['hide_add_to_cart_stock'] == 1 && (int)$stock < 1) {
				$dA['class_btn'] 		= 'ph-visibility-hidden';// hide button
				$dA['class_icon']		= 'ph-display-none';// hide icon
			}

			if($stockStatus['stock_status'] || $stockStatus['stock_count'] !== false) {
				$dS							= array();
				$dS['s']	                = $this->s;
				$dS['class']				= 'ph-item-stock-box';
				$dS['product_id']			= (int)$v->id;
				$dS['typeview']				= 'Category';
				$dS['stock_status_output'] 	= PhocacartStock::getStockStatusOutput($stockStatus);
				$dSO = $layoutS->render($dS);
			}

			if($stockStatus['min_quantity']) {
				$dPOQ						= array();
				$dPOQ['s']	                = $this->s;
				$dPOQ['text']				= JText::_('COM_PHOCACART_MINIMUM_ORDER_QUANTITY');
				$dPOQ['status']				= $stockStatus['min_quantity'];
				$dSO .= $layoutPOQ->render($dPOQ);
			}

			if($stockStatus['min_multiple_quantity']) {
				$dPOQ						= array();
				$dPOQ['s']	                = $this->s;
				$dPOQ['text']				= JText::_('COM_PHOCACART_MINIMUM_MULTIPLE_ORDER_QUANTITY');
				$dPOQ['status']				= $stockStatus['min_multiple_quantity'];
				$dSO .= $layoutPOQ->render($dPOQ);
			}
		}
		// END STOCK ================================================


		// ------------------------------------
		// BUTTONS + ICONS
		// ------------------------------------
		// Prepare data for Add to cart button
		// - Add To Cart Standard Button
		// - Add to Cart Icon Button
		// - Add to Cart Icon Only

		if ((int)$this->t['category_addtocart'] == 1 || (int)$this->t['category_addtocart'] == 4 || $this->t['display_addtocart_icon'] == 1) {

			// FORM DATA
			$dF['s']					= $this->s;
			$dF['linkch']				= $this->t['linkcheckout'];// link to checkout (add to cart)
			$dF['id']					= (int)$v->id;
			$dF['catid']				= $this->t['categoryid'];
			$dF['return']				= $this->t['actionbase64'];
			$dF['typeview']				= 'Category';
			$dA['addtocart']			= $this->t['category_addtocart'];
			$dA['addtocart_icon']		= $this->t['display_addtocart_icon'];


			// Both buttons + icon
			$dA['s']					= $this->s;
			$dA['id']					= (int)$v->id;
			$dA['link']					= $link;// link to item (product) view e.g. when there are required attributes - we cannot add it to cart
			$dA['addtocart']			= $this->t['category_addtocart'];
			$dA['method']				= $this->t['add_cart_method'];
			$dA['typeview']				= 'Category';

			// ATTRIBUTES, OPTIONS
			$dAb['s']						= $this->s;
			$dAb['attr_options']			= $attributesOptions;
			$dAb['hide_attributes']			= $this->t['hide_attributes_category'];
			$dAb['dynamic_change_image'] 	= $this->t['dynamic_change_image'];
			$dAb['zero_attribute_price']	= $this->t['zero_attribute_price'];
			$dAb['pathitem']				= $this->t['pathitem'];
			$dAb['product_id']				= (int)$v->id;
			$dAb['image_size']				= $image['size'];
			$dAb['typeview']				= 'Category';
			$dAb['price']					= $price;

			// Attribute is required and we don't display it in category/items view, se we need to redirect to detail view
			$dA['selectoptions']	= 0;
			if (isset($v->attribute_required) && $v->attribute_required == 1 && $this->t['hide_attributes_category'] == 1) {
				$dA['selectoptions']	= 1;
			}

			// Add To Cart as Icon
			if ($this->t['display_addtocart_icon'] == 1) {
				$icon['addtocart'] 	= $layoutAI->render($dA);

			}

		}

		// Different button or icons
		$addToCartHidden = 0;// Design parameter - if there is no button (add to cart, paddle link, external link), used e.g. for displaying ask a question button
		if ($v->type == 3) {
			// PRODUCTTYPE - price on demand price cannot be added to cart
			$dA = array(); // Skip Standard Add to cart button
			$icon['addtocart'] = '';// Skip Add to cart icon
			$dF = array();// Skip form
			$addToCartHidden = 1;
		} else if ($this->t['hide_add_to_cart_zero_price'] == 1 && $v->price == 0) {
			// Don't display Add to Cart in case the price is zero
			$dA = array(); // Skip Standard Add to cart button
			$icon['addtocart'] = '';// Skip Add to cart icon
			$dF = array();// Skip form
			$addToCartHidden = 1;
		} else if ((int)$this->t['category_addtocart'] == 1 || (int)$this->t['category_addtocart'] == 4) {
			// ADD TO CART BUTTONS - we have data yet
		} else if ((int)$this->t['category_addtocart'] == 102 && (int)$v->external_id != '') {
			// EXTERNAL LINK PADDLE
			$dA2['t']				= $this->t;
			$dA2['s']				= $this->s;
			$dA2['external_id']		= (int)$v->external_id;
			$dA2['return']			= $this->t['actionbase64'];

			$dA = array(); // Skip Standard Add to cart button
			$icon['addtocart'] = '';// Skip Add to cart icon
			$dF = array();// Skip form

		} else if ((int)$this->t['category_addtocart'] == 103 && $v->external_link != '') {
			// EXTERNAL LINK
			$dA3['t']				= $this->t;
			$dA3['s']				= $this->s;
			$dA3['external_link']	= $v->external_link;
			$dA3['external_text']	= $v->external_text;
			$dA3['return']			= $this->t['actionbase64'];

			$dA = array(); // Skip Standard Add to cart button
			$icon['addtocart'] = '';// Skip Add to cart icon
			$dF = array();// Skip form


		} else {
			// ADD TO CART ICON ONLY (NO BUTTONS)
			$dA = array(); // Skip Standard Add to cart button
			// We remove the $dA completely, even for the icon, but the icon has the data already stored in $icon['addtocart']
			// so no problem with removing the data completely
			// $dA for button will be rendered
			// $dA for icon was rendered already
			// Do not skip the form here
			$addToCartHidden = 1;
		}
		// ---------------------------- END BUTTONS

		$dQ	= array();
		if (((int)$this->t['category_askquestion'] == 1) || ($this->t['category_askquestion'] == 2 && ((int)$this->t['category_addtocart'] == 0 || $addToCartHidden != 0))) {

			$dQ['s']			= $this->s;
			$dQ['id']			= (int)$v->id;
			$dQ['catid']		= $this->t['categoryid'];;
			$dQ['popup']		= 0;
			$tmpl				= '';
			if ((int)$this->t['popup_askquestion'] > 0) {
				$dQ['popup']		= (int)$this->t['popup_askquestion'];
				$popupAskAQuestion	= (int)$this->t['popup_askquestion'];
				$tmpl				= 'tmpl=component';
			}
			$dQ['link']			=  JRoute::_(PhocacartRoute::getQuestionRoute($v->id, $v->catid, $v->alias, $v->catalias, $tmpl));
			$dQ['return']		= $this->t['actionbase64'];

		}






		// ======
		// RENDER
		// ======
		$dL 					= array();
		$dL['t']				= $this->t;
		$dL['s']				= $this->s;
		$dL['col']				= $col;
		$dL['link'] 			= $link;
		$dL['lt']				= $lt;// Layout Type
		$dL['layout']['dI']		= $dI;// Image
		$dL['layout']['dP']		= $dP;// Price
		$dL['layout']['dSO']	= $dSO;// Stock Output
		$dL['layout']['dF']		= $dF;// Form
		$dL['layout']['dAb']	= $dAb;// Attributes
		$dL['layout']['dV']		= $dV;// Link to Product View
		$dL['layout']['dA']		= $dA;// Button Add to Cart
		$dL['layout']['dA2']	= $dA2;// Button Buy now
		$dL['layout']['dA3']	= $dA3;// Button external link
		$dL['layout']['dQ']		= $dQ;// Ask A Question

		$dL['icon']				= $icon;// Icons
		$dL['product_header']	= PhocacartRenderFront::renderProductHeader($this->t['product_name_link'], $v, 'item', '', $lt);

		// Events
		$results = \JFactory::getApplication()->triggerEvent('onCategoryItemAfterAddToCart', array('com_phocacart.category', &$v, &$this->p));
		$dL['event']['onCategoryItemsItemAfterAddToCart'] = trim(implode("\n", $results));

		// LABELS
		$dL['labels'] =  $label['new'] . $label['hot'] . $label['feat'];
		$tagLabelsOutput = PhocacartTag::getTagsRendered((int)$v->id, 1);
		if ($tagLabelsOutput != '') {
			$dL['labels'] .= $tagLabelsOutput;
		}


		// REVIEW - STAR RATING
		$dL['review'] = '';
		if ((int)$this->t['display_star_rating'] > 0) {
			$d							= array();
			$d['s']						= $this->s;
			$d['rating']				= isset($v->rating) && (int)$v->rating > 0 ? (int)$v->rating : 0;
			$d['size']					= 16;
			$d['display_star_rating']	= (int)$this->t['display_star_rating'];
			$dL['review'] = $layoutR->render($d);
		}

		// DESCRIPTION
		$dL['description'] = '';
		if ($this->t['cv_display_description'] == 1 && $v->description != '') {
			$dL['description'] = '<div class="ph-item-desc">' . JHtml::_('content.prepare', $v->description) . '</div>';
		}

		if ($lt == 'list') {
			echo $layoutIL->render($dL);
		} else if ( $lt == 'gridlist') {
			echo $layoutIGL->render($dL);
		} else  {
			echo $layoutIG->render($dL);
		}
		// --------------- END RENDER





		if ($i%(int)$this->t['columns_cat'] == 0) {
			echo '<div class="ph-cb"></div>';
		}
		$i++;


	}

	echo '</div>';// end row (row-flex)
	echo '<div class="ph-cb"></div>';


	echo $this->loadTemplate('pagination');

	echo '</div>'. "\n"; // end items
}


// FOOTER - NOT AJAX
if (!$this->t['ajax']) {

	echo '</div>';// end #phItemsBox
	echo '</div>';// end #ph-pc-category-box

	echo '<div id="phContainer"></div>';

	if (isset($popupAskAQuestion) && $popupAskAQuestion == 2) {

		echo '<div id="phContainerPopup">';
		$d						= array();
		$d['id']				= 'phAskAQuestionPopup';
		$d['title']				= JText::_('COM_PHOCACART_ASK_A_QUESTION');
		$d['icon']				= $this->s['i']['question-sign'];
		$d['t']					= $this->t;
		$d['s']					= $this->s;
		echo $layoutAAQ->render($d);
		echo '</div>';// end phContainerPopup
	}

	echo '<div>&nbsp;</div>';
	echo PhocacartUtilsInfo::getInfo();
}
?>
