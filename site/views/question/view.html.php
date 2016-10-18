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

class PhocaCartViewQuestion extends JViewLegacy
{
	protected $t;
	protected $p;
	protected $u;
	protected $category;
	protected $item;
	protected $form;
	
	public function display($tpl = null) {
	
		$app								= JFactory::getApplication();
		$document							= JFactory::getDocument();
		$session 							= JFactory::getSession();
		
		$uri 								= JFactory::getURI();
		$this->t['action']					= $uri->toString();
		$this->t['actionbase64']			= base64_encode($this->t['action']);
		$this->u							= JFactory::getUser();
		
		$this->p 							= $app->getParams();
		$this->t['load_chosen']				= $this->p->get( 'load_chosen', 1 );
		$this->t['load_bootstrap']			= $this->p->get( 'load_bootstrap', 0 );
		$this->t['question_description']	= $this->p->get( 'question_description', '' );
		
		$this->t['enable_ask_question'] 	= $this->p->get('enable_ask_question', 0);
		if ($this->t['enable_ask_question'] == 0) {
			throw new Exception(JText::_('COM_PHOCACART_ASK_QUESTION_DISABLED'), 500);
			return false;
		}
		
		// Security
		$namespace  = 'phccrt' . $this->p->get('session_suffix');
		$session->set('form_id', PhocaCartUtils::getRandomString(mt_rand(6,10)), 'phocacart');

		if((int)$this->p->get('enable_time_check_question', 0) > 0) {
			$sesstime = $session->get('time', time(), $namespace);
			$session->set('time', $sesstime, $namespace);
		}	

		// Securitry Hidden Field
		if ($this->p->get('enable_hidden_field_question', 0) == 1) {
			$this->p->set('hidden_field_position', PhocaCartSecurity::setHiddenFieldPos($this->p->get('display_name_form'), $this->p->get('display_email_form'), $this->p->get('display_phone_form'), $this->p->get('display_message_form')));
			
			$session->set('hidden_field_id', 'hf'.PhocaCartUtils::getRandomString(mt_rand(6,10)), $namespace);
			$session->set('hidden_field_name', 'hf'.PhocaCartUtils::getRandomString(mt_rand(6,10)), $namespace);
			$session->set('hidden_field_class', 'pc'.PhocaCartUtils::getRandomString(mt_rand(6,10)), $namespace);
				
			$this->p->set('hidden_field_id', $session->get('hidden_field_id', '', $namespace));
			$this->p->set('hidden_field_name', $session->get('hidden_field_name', '', $namespace));
			$this->p->set('hidden_field_class', $session->get('hidden_field_class', '', $namespace));

			$document->addCustomTag('<style type="text/css"> .'.$this->p->get('hidden_field_class').' { '."\n\t".'display: none !important;'."\n".'}</style>');
		} else {
			$this->p->set('hidden_field_position', -1);
		}

		
		$id						= $app->input->get('id', 0, 'int');
		$catid					= $app->input->get('catid', 0, 'int');
		$tmpl					= $app->input->get('tmpl', '', 'string');
	
		
		if ($id > 0 && $catid > 0) {
			//$modelP	= $this->getModel('Item', 'PhocaCartModel');
			jimport('joomla.application.component.model');
			JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_phocacart/models');
			$modelP = JModelLegacy::getInstance( 'Item', 'PhocaCartModel' );
				
			$this->category			= $modelP->getCategory($id, $catid);
			
			$this->item				= $modelP->getItem($id, $catid);
			$this->t['catid']		= 0;
			if (isset($this->category[0]->id)) {
				$this->t['catid']	= (int)$this->category[0]->id;
			}

		}
		
		if ($tmpl == 'component') {
			
			$document->addCustomTag( "<style type=\"text/css\"> \n" 
			." #ph-pc-question-box {
				margin: 20px
			} \n"
			." </style> \n");
		}
		
		$this->t['pathitem'] = PhocaCartpath::getPath('productimage');
		
		$this->form		= $this->get('Form');
		
		if (!empty($this->form) && $id > 0) {
			$this->form->setValue('product_id', null, (int)$id);
		}
		if (!empty($this->form) && $catid > 0) {
			$this->form->setValue('category_id', null, (int)$catid);
		}
		
		
		$media = new PhocaCartRenderMedia();
		$media->loadBootstrap($this->t['load_bootstrap']);
		$media->loadChosen($this->t['load_chosen']);
		
		$this->_prepareDocument();

		parent::display($tpl);	
	}
	
	protected function _prepareDocument() {
	
		PhocaCartRenderFront::prepareDocument($this->document, $this->p, false, false, JText::_('COM_PHOCACART_ASK_A_QUESTION'));
	}
}
?>