<?php
/**
 * Uninstallation script
 *
 * @category   SocialMedia
 * @package    Techinflo_SocialMedia
 * @author     Techinflo Team
 */

/* @var $uninstaller Mage_Catalog_Model_Resource_Setup */

require_once('app/Mage.php');

Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
$uninstaller = new Mage_Sales_Model_Mysql4_Setup;
$uninstaller->startSetup();

if ($uninstaller->getAttributeId('catalog_product', 'is_facebook')) {
$uninstaller->removeAttribute('catalog_product', 'is_facebook');
}

if ($uninstaller->getAttributeId('catalog_product', 'is_twitter')) {
$uninstaller->removeAttribute('catalog_product', 'is_twitter');
}

if ($uninstaller->getAttributeId('catalog_product', 'special_offer')) {
$uninstaller->removeAttribute('catalog_product', 'special_offer');
}

$uninstaller->endSetup();
?>