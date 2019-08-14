<?php
/**
 * Installation script
 *
 * @category   SocialMedia
 * @package    Techinflo_SocialMedia
 * @author     Techinflo Team
 */

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'core_setup');

$installer->startSetup();
if (!$installer->getAttributeId('catalog_product', 'is_facebook')) {
    $installer->addAttribute('catalog_product', 'is_facebook', array(
        'group' => 'General',
        'label' => 'In Facebook',
        'required' => false,
        'input' => 'select',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => 'none',
        'position' => 1,
        'sort_order' => 15,
        'visible' => 1,
    ));
}

if (!$installer->getAttributeId('catalog_product', 'is_twitter')) {
    $installer->addAttribute('catalog_product', 'is_twitter', array(
        'group' => 'General',
        'label' => 'In Twitter',
        'required' => false,
        'input' => 'select',
        'source' => 'eav/entity_attribute_source_boolean',
        'default' => 'none',
        'position' => 1,
        'sort_order' => 16,
        'visible' => 1,
    ));
}

if (!$installer->getAttributeId('catalog_product', 'special_offer')) {
    $installer->addAttribute('catalog_product', 'special_offer', array(
        'group' => 'General',
        'label' => 'Facebook Tagline',
        'required' => false,
        'input' => 'textarea',
        'default' => 'none',
        'position' => 1,
        'sort_order' => 14,
        'visible' => 1,
    ));
}

$installer->endSetup();
