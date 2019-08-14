<?php 

require_once("lib/techinflo/facebook/facebook_sdk/src/facebook.php");

require_once("lib/techinflo/twitter/twitter_sdk/src/twitter.php");

class Techinflo_SocialMedia_Model_Blockobserver
{
		public function promo_rule($observer)
		{
			$block = $observer->getEvent()->getBlock();
				if (!isset($block)) {
					return $this;
				}
				
				if ($block->getType() == 'adminhtml/promo_quote_edit_tab_main') {       
			
				$form = $block->getForm();

				 $fieldset = $form->addFieldset('socialmedia_fieldset',
					array('legend' => Mage::helper('salesrule')->__('Social Media Information'))
				);
				
				if (Mage::getStoreConfig('techinflosocialmedia/facebook/fbenabled')) {
				
					$fieldset->addField('is_facebook', 'select', array(
						'label'     => Mage::helper('salesrule')->__('In Facebook'),
						'title'     => Mage::helper('salesrule')->__('In Facebook'),
						'name'      => 'is_facebook',
						'required' => true,
						'options'    => array(
							'Yes' => Mage::helper('salesrule')->__('Yes'),
							'No' => Mage::helper('salesrule')->__('No'),
						),
					));
				}
					
				if (Mage::getStoreConfig('techinflosocialmedia/twitter/twenabled')) {
				
					$fieldset->addField('is_twitter', 'select', array(
						'label'     => Mage::helper('salesrule')->__('In Twitter'),
						'title'     => Mage::helper('salesrule')->__('In Twitter'),
						'name'      => 'is_twitter',
						'required' => true,
						'options'    => array(
							'Yes' => Mage::helper('salesrule')->__('Yes'),
							'No' => Mage::helper('salesrule')->__('No'),
						),
					));
					}
				}
				
				if ($block->getType() == 'adminhtml/promo_catalog_edit_tab_main') {       
				
				 $form = $block->getForm();
							
				 $fieldset = $form->addFieldset('socialmedia_catalog_fieldset',
					array('legend' => Mage::helper('catalogrule')->__('Social Media Information'))
				);
				
					if (Mage::getStoreConfig('techinflosocialmedia/facebook/fbenabled')) {
						$fieldset->addField('is_facebook', 'select', array(
							'label'     => Mage::helper('catalogrule')->__('In Facebook'),
							'title'     => Mage::helper('catalogrule')->__('In Facebook'),
							'name'      => 'is_facebook',
							'required' => true,
							'options'    => array(
								'Yes' => Mage::helper('catalogrule')->__('Yes'),
								'No' => Mage::helper('catalogrule')->__('No'),
							),
						));
					}
					
					if (Mage::getStoreConfig('techinflosocialmedia/twitter/twenabled')) {
						$fieldset->addField('is_twitter', 'select', array(
							'label'     => Mage::helper('catalogrule')->__('In Twitter'),
							'title'     => Mage::helper('catalogrule')->__('In Twitter'),
							'name'      => 'is_twitter',
							'required' => true,
							'options'    => array(
								'Yes' => Mage::helper('catalogrule')->__('Yes'),
								'No' => Mage::helper('catalogrule')->__('No'),
							),
						));
					}
			}
		}	
}