UNINSTALLATION PROCEDURE FOR SOCIAL MEDIA EXTENSION:

After uninstalling the social media extension from magento connect manager,
to remove the system attributes and ensure the complete uninstallation of 
social media extension, 


It is required to run the following queries in to 
the database manually;

DELETE FROM `eav_attribute` WHERE `attribute_code`='is_facebook';

DELETE FROM `eav_attribute` WHERE `attribute_code`='is_twitter';

DELETE FROM `eav_attribute` WHERE `attribute_code`='special_offer';

or else need to place the uninstallation script(uninstal.php file)in magento 
root dir and run the uninstal script in the browser.