<?php
/**
 *  Upgrade script 1.0.0 -> 1.0.1
 *
 * @category   SocialMedia
 * @package    Techinflo_SocialMedia
 * @author     Techinflo Team
 */

/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer = Mage::getResourceModel('catalog/setup', 'core_setup');

$installer->startSetup();
$installer->updateAttribute('catalog_product', 'is_facebook', 'used_in_product_listing', 1);
$installer->updateAttribute('catalog_product', 'is_twitter', 'used_in_product_listing', 1);
$installer->updateAttribute('catalog_product', 'special_offer', 'used_in_product_listing', 1);
$installer->endSetup();
