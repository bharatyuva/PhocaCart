<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * Why Items View or why category view? Category view always has category ID,
 * but items view is here for filtering and searching and this can be without category ID
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view');

class PhocaCartViewItems extends JViewLegacy
{
	protected $category;
	protected $subcategories;
	protected $items;
	protected $t;
	protected $p;
	
	function display($tpl = null) {		
		
		$app						= JFactory::getApplication();
		$this->p 					= $app->getParams();
		$uri 						= JFactory::getURI();
		$model						= $this->getModel();
		$document					= JFactory::getDocument();
		$this->t['categoryid']		= $app->input->get( 'id', 0, 'int' );// optional
		$this->t['limitstart']		= $app->input->get( 'limitstart', 0, 'int' );
		$this->items				= $model->getItemList();
		
		// PARAMS
		$this->t['display_new']				= $this->p->get( 'display_new', 0 );
		$this->t['cart_metakey'] 			= $this->p->get( 'cart_metakey', '' );
		$this->t['cart_metadesc'] 			= $this->p->get( 'cart_metadesc', '' );
		//$this->t['description']			= $this->p->get( 'description', '' );
		$this->t['load_bootstrap']			= $this->p->get( 'load_bootstrap', 0 );
		$this->t['equal_height']			= $this->p->get( 'equal_height', 0 );
		$this->t['image_width_cat']			= $this->p->get( 'image_width_cat', '' );
		$this->t['image_height_cat']		= $this->p->get( 'image_height_cat', '' );
		//$this->t['image_link']				= $this->p->get( 'image_link', 0 );
		$this->t['columns_cat']				= $this->p->get( 'columns_cat', 3 );
		$this->t['enable_social']			= $this->p->get( 'enable_social', 0 );
		$this->t['cv_display_subcategories']= $this->p->get( 'cv_display_subcategories', 5 );
		$this->t['display_back']			= $this->p->get( 'display_back', 3 );
		$this->t['display_compare']			= $this->p->get( 'display_compare', 0 );
		$this->t['display_wishlist']		= $this->p->get( 'display_wishlist', 0 );
		$this->t['display_quickview']		= $this->p->get( 'display_quickview', 0 );
		$this->t['fade_in_action_icons']	= $this->p->get( 'fade_in_action_icons', 0 );
		$this->t['dynamic_change_price']	= $this->p->get( 'dynamic_change_price', 0 );
		$this->t['items_addtocart']			= $this->p->get( 'items_addtocart', 1 );
		$this->t['load_chosen']				= $this->p->get( 'load_chosen', 1 );
		$this->t['dynamic_change_image']	= $this->p->get( 'dynamic_change_image', 0);
		$this->t['dynamic_change_price']	= $this->p->get( 'dynamic_change_price', 0 );
		$this->t['add_compare_method']		= $this->p->get( 'add_compare_method', 0 );
		$this->t['add_wishlist_method']		= $this->p->get( 'add_wishlist_method', 0 );
		$this->t['hide_price']				= $this->p->get( 'hide_price', 0 );
		$this->t['hide_addtocart']			= $this->p->get( 'hide_addtocart', 0 );
		if ($this->t['hide_addtocart'] == 1) {
			$this->t['items_addtocart']		= 0;
		}
		$this->t['display_view_product_button']	= $this->p->get( 'display_view_product_button', 1 );
		$this->t['product_name_link']			= $this->p->get( 'product_name_link', 0 );
		$this->t['switch_image_category_items']	= $this->p->get( 'switch_image_category_items', 0 );
		
		//$this->category					= $model->getCategory($this->t['categoryid']);
		//$this->subcategories				= $model->getSubcategories($this->t['categoryid']);
		$this->items						= $model->getItemList();
		$this->t['pagination']				= $model->getPagination();
		$this->t['ordering']				= $model->getOrdering();
		$this->t['photopathrel']			= JURI::base().'phocaphoto/';
		$this->t['photopathabs']			= JPATH_ROOT .'/phocaphoto/';
		$this->t['action']					= $uri->toString();
		//$this->t['actionbase64']			= base64_encode(htmlspecialchars($this->t['action']));
		$this->t['actionbase64']			= base64_encode($this->t['action']);
		$this->t['linkcheckout']			= JRoute::_(PhocaCartRoute::getCheckoutRoute(0));
		$this->t['linkcomparison']			= JRoute::_(PhocaCartRoute::getComparisonRoute(0));
		$this->t['linkwishlist']			= JRoute::_(PhocaCartRoute::getWishListRoute(0));
		
		
		if ($this->t['limitstart'] > 0 ) {
			$this->t['limitstarturl'] =  '&start='.$this->t['limitstart'];
		} else {
			$this->t['limitstarturl'] = '';
		}
		
		$media = new PhocaCartRenderMedia();
		$media->loadBootstrap($this->t['load_bootstrap']);
		$media->loadChosen($this->t['load_chosen']);
		$media->loadEqualHeights($this->t['equal_height']);
		$media->loadProductHover($this->t['fade_in_action_icons']);
		
		$this->t['class_thumbnail'] = 'thumbnail';
			if ($this->t['fade_in_action_icons']) {
				$this->t['class_thumbnail'] = '';
			}
		
		PhocaCartRenderJs::renderAjaxAddToCart();
		PhocaCartRenderJs::renderAjaxAddToCompare();
		PhocaCartRenderJs::renderAjaxAddToWishList();
		
		if ($this->t['display_quickview'] == 1) {
			PhocaCartRenderJs::renderAjaxQuickViewBox();
			if ($this->t['dynamic_change_price'] == 1) {
				PhocaCartRenderJs::renderAjaxChangeProductPriceByOptions(0, 'ph-item-price-box');// We need to load it here
			}
			$media->loadPhocaAttribute(1);// We need to load it here
			$media->loadPhocaSwapImage($this->t['dynamic_change_image']);// We need to load it here in ITEM (QUICK VIEW) VIEW
		}
			
		$media->loadPhocaMoveImage($this->t['switch_image_category_items']);// Move (switch) images in CATEGORY, ITEMS VIEW

		$this->_prepareDocument();
		$this->t['pathcat'] = PhocaCartPath::getPath('categoryimage');
		$this->t['pathitem'] = PhocaCartpath::getPath('productimage');
		
		
		parent::display($tpl);
		
	}
	

	protected function _prepareDocument() {
		$category = false;
		if (isset($this->category[0]) && is_object($this->category[0])) {
			$category = $this->category[0];
		}
		PhocaCartRenderFront::prepareDocument($this->document, $this->p, $category);
	}
}
?>