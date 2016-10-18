<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view');

class PhocaCartViewWishList extends JViewLegacy
{
	protected $t;
	protected $p;

	function display($tpl = null)
	{		
		$app								= JFactory::getApplication();
		$model								= $this->getModel();
		$document							= JFactory::getDocument();
		$this->p 							= $app->getParams();
		
		//$this->t['categories']				= $model->getCategoriesList();

		$this->t['cart_metakey'] 			= $this->p->get( 'cart_metakey', '' );
		$this->t['cart_metadesc'] 			= $this->p->get( 'cart_metadesc', '' );
		$this->t['load_bootstrap']			= $this->p->get( 'load_bootstrap', 0 );
		$this->t['hide_price']				= $this->p->get( 'hide_price', 0 );
		//$this->t['hide_addtocart']			= $this->p->get( 'hide_addtocart', 0 );
		//$this->t['category_addtocart']		= $this->p->get( 'category_addtocart', 1 );
		

		
		$uri 						= JFactory::getURI();
		$this->t['action']			= $uri->toString();
		$this->t['actionbase64']	= base64_encode($this->t['action']);
		$this->t['linkwishlist']	= JRoute::_(PhocaCartRoute::getWishListRoute());
		
		$wishlist = new PhocaCartWishList();
		$this->t['items'] = $wishlist->getFullItems();
		

		if (!empty($this->t['items'])) {

			foreach ($this->t['items'] as $k => $v) {
			
			/*	$this->t['items'][$k]['attr_options']= PhocaCartAttribute::getAttributesAndOptions((int)$v['id']);
				if (!empty($this->t['items'][$k]['attr_options'])) {
					$this->t['value']['attrib'] = 1;
				}
				
				$this->t['items'][$k]['specifications']= PhocaCartSpecification::getSpecificationGroupsAndSpecifications((int)$v['id']);
				if (!empty($this->t['items'][$k]['specifications'])) {
					foreach($this->t['items'][$k]['specifications'] as $k2 => $v2) {
						//$this->t['spec'][$k2] = $v2[0];
						$newV2 = $v2;
						unset($newV2[0]);
						if (!empty($newV2)) {
							foreach($newV2 as $k3 => $v3) {
								$this->t['spec'][$v2[0]][$v3['title']][$k] = $v3['value'];
								//$this->t['spec'][$k2][$k3][$k3] = $v3['value'];
							}
						}
					
					}
				}*/
				
				$stockStatus = PhocaCartStock::getStockStatus((int)$v['stock'], (int)$v['min_quantity'], (int)$v['min_multiple_quantity'], (int)$v['stockstatus_a_id'],  (int)$v['stockstatus_n_id']);
				$this->t['items'][$k]['stock'] = PhocaCartStock::getStockStatusOutput($stockStatus);
				if ($this->t['items'][$k]['stock'] != '') {
					$this->t['value']['stock'] = 1;
				}
			}
		}
		
		$media = new PhocaCartRenderMedia();
		$media->loadBootstrap($this->t['load_bootstrap']);
		//$media->loadChosen($this->t['load_chosen']);
		//$media->loadEqualHeights($this->t['equal_height']);
		
		$this->t['pathitem'] = PhocaCartPath::getPath('productimage');
		$this->_prepareDocument();
		parent::display($tpl);
		
	}
	
	protected function _prepareDocument() {
		PhocaCartRenderFront::prepareDocument($this->document, $this->p, false, false, JText::_('COM_PHOCACART_WISH_LIST'));
	}
}
?>