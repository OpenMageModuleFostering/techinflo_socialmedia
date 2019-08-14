<?php 

require_once("lib/techinflo/facebook/facebook_sdk/src/facebook.php");

require_once("lib/techinflo/twitter/twitter_sdk/src/twitter.php");

class Techinflo_SocialMedia_Model_Observer
{

	/*Event Observer that is called once the product is saved/ edited*/
	
	public function catalog_product_save_after($observer)
	{	/*Facebook API */
		$config = array();
		$config['appId'] = $this->getApiKey();
		$config['secret'] = $this->getSecretKey();
		$config['fileUpload'] = false; // optional
		$fb = new Facebook($config);
		$access_token = $this->getAccessToken();
		
		/*Twitter API */
				
		$twitter_api_key = $this->getTwApiKey();
		$twitter_api_secret = $this->getTwSecretKey();
		$twitter_oauth_token = $this->getTwOauthKey();
		$twitter_oauth_secret = $this->getTwOauthSecretKey();
		
		define("CONSUMER_KEY", $twitter_api_key);
		define("CONSUMER_SECRET", $twitter_api_secret);
		define("OAUTH_TOKEN", $twitter_oauth_token);
		define("OAUTH_SECRET", $twitter_oauth_secret);
		
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$content = $connection->get('account/verify_credentials');

		/*Product Information*/
		$product = $observer->getProduct();
		$product_info = $this->getProductInfo($observer);
		$product_title = $this->getProductName($observer);
		$product_tagline =  $this->getTagText($observer);
		$product_image = $this->getProductImage($observer);
		$product_status = $product->getStatus();
			
		/*Check if the products are postable on social media*/
		$is_fb_postable = $this->getProductPostable($observer);
		$is_tw_postable = $this->getTwProductPostable($observer);
		
		$params = array(
		  "access_token" => $access_token, // this is the main access token (facebook profile)
		  "message" => $product_info["short_description"],
		  "link" => $product_info["url"],
		 // "picture" => $product_image,
		  "name" => $product_title,
		  "caption" => $product_tagline,
		  "description" => $product_info["description"],
		);
		
	 /******************Facebook Post Start****************/
		/*Get the status of the facebook configuration*/
		$fb_enabled = $this->getFbStatus();
		
		/*Get the product dates to check whether it is new or not*/
		$newFromDate = $product->getNewsFromDate();
		$newToDate = $product->getNewsToDate();
		$now = date("Y-m-d H:m:s");

		if($fb_enabled) {
		/* Check if the product is enabled */
		if ($product_status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
		/*If the product is new to the store*/
		if($newFromDate < $now && $newToDate > $now) {
		/* Check if the product is postable */
		if($is_fb_postable == 'Yes' || $is_fb_postable == 'yes' ){
		try {
		    $ret = $fb->api('/me/feed', 'POST', $params);
			//echo 'Successfully posted to Facebook Personal Profile';
		} catch(Exception $e) {
		   //echo $e->getMessage();
		}
	   }
	  }
	
	  /* Check if the product price is reduced */
	 else if($product->getFinalPrice() < $product->getPrice()){
	  /* Check if the product is postable */
		if($is_fb_postable == 'Yes' || $is_fb_postable == 'yes' ){
		try {
		    $ret = $fb->api('/me/feed', 'POST', $params);
			//echo 'Successfully posted to Facebook Personal Profile';
		} catch(Exception $e) {
		    //echo $e->getMessage();
		}
	  }
	}
   }
  }
	/******************Facebook Post Ends****************/
	
	/******************Twitter Post Start****************/
	/*Get the status of the facebook configuration*/
	$tw_enabled = $this->getTwStatus();
		
	if($tw_enabled) {
			
	/* Check if the product is enabled */
		if ($product_status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
		/*If the product is new to the store*/
		if($newFromDate < $now && $newToDate > $now) {
		/* Check if the product is postable */
		if($is_tw_postable == 'Yes' || $is_tw_postable == 'yes' ){
		try {
			$message .= $product_title;
			$message .= ' ';
			$message .= 'SKU:';
			$message .= $product_info['model'];
			$message .= ' ';
			$message .= 'Price:';
			$message .= $product_info["price"];
			$message .= ' ';
			$message .= 'Desc:';
			$message .= $product_info["short_description"];
		
			if(strlen($message) > 140)
			$message = substr($message,0,140);
			$post = $connection->post('statuses/update', array('status' => $message));
		}   catch(Exception $e) {//echo $e->getMessage();
		}
	   }
	  }
	  
	  /* Check if the product price is reduced */
	 else if($product->getFinalPrice() < $product->getPrice()){
	  /* Check if the product is postable */
		if($is_tw_postable == 'Yes' || $is_tw_postable == 'yes' ){
		try { 
			$message .= $product_title;
			$message .= ' ';
			$message .= 'SKU:';
			$message .= $product_info["model"];
			$message .= ' ';
			$message .= 'Price:';
			$message .= $product_info["price"];
			$message .= ' ';
			$message .= 'Special Price:';
			$message .= $product_info["special price"];
			$message .= ' ';
			$message .= 'Desc:';
			$message .= $product_info["short_description"];
			
			if(strlen($message) > 140)
			$message = substr($message,0,140);
			$post = $connection->post('statuses/update', array('status' => $message));
		}   catch(Exception $e) { //echo $e->getMessage(); 
		}
	  }
	}
   }
 }
}
	/******************Twitter Post Ends****************/
	
	/**************************  START OF FACEBOOK API FUNCTIONS **************************/
	
	/*Returns String
	  Stored facebook access token from the configuration
	 */
	public function getAccessToken(){
	 
	 return Mage::getStoreConfig('techinflosocialmedia/facebook/fbaccesstoken');
	}
	
	 /*Returns String
	   Stored facebook api key from the configuration
	 */
	public function getApiKey(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/facebook/fbapikey');
	}
	
	/*Returns String
	   Stored facebook secret key from the configuration
	 */
	public function getSecretKey(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/facebook/fbsecretkey');
	}
	
	 /*Returns String
	   Stored product information from the configuration
	 */
	public function getProductInfo($observer){
	
		$product_data = array();
		
		$product=$observer->getProduct();
		$product_data["id"]=$product->getId();
		//$product_data["name"]=$product()->getName();
		$product_data["short_description"]=$product->getShortDescription();
		$product_data["description"]=$product->getDescription();
		$product_data["price"]=$product->getPrice();		
		$product_data["image"]=$product->getImage();
		$product_data["special price"]=$product->getFinalPrice();
		$product_data["model"]=$product->getSku();
		$product_data["url"]=$product->getProductUrl();
		$product_data["url_key "]=$product->getAttributeText("url_key");
				
		return $product_data;
	}
	
	 /*Returns String
	   Stored product name from the configuration
	 */
	 public function getProductName($observer){
		return $product_data["name"] = $observer->getProduct()->getName();
	}
	
	 /*Returns String
	   Stored product image from the configuration
	 */
	 public function getProductImage($observer){

	 if(!$observer->getProduct()->getImage())
	 {
		 $prod_id = $observer->getProduct()->getId(); 
		 $product = Mage::getModel('catalog/product')->load($prod_id);
		 $full_path_url = Mage::helper('catalog/image')->init($product, 'thumbnail');
		 $product_data["image"] =   $full_path_url; //$observer->getProduct()->getImage();
	}
	else 
	     $product_data["image"] = "http://magento.techinflo.com/images/noimg.jpg";
		 
		return $product_data["image"];  
	}
	
	/*Returns string
	  Gets the store product information from the product configuration
	 */
	 public function getTagText($observer){
		$product=$observer->getProduct();
		
		$tag_text = $product->getAttributeText("special_offer");
		$prod_sku = $product->getSku();
		$price = $product->getPrice();
		$spl_price = $product->getFinalPrice();
		
		$product_info["tagline"] = $tag_text;
		$product_info["tagline"].= 'Product SKU: ' . ' ' . $prod_sku;
		$product_info["tagline"].= 'Price:' . ' ' . $price;
		$product_info["tagline"].= 'Special Price:' . ' ' . $spl_price;
		 
		return $product_info["tagline"];
	}
	
	/*Returns String
	   Stored facebook configuration status from the configuration
	 */
	public function getFbStatus(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/facebook/fbenabled');
	}
	
	 /*Returns String
	   Stored is_facebook postable from the configuration
	 */
	 public function getProductPostable($observer){
		return $product_data["fb_postable"] = $observer->getProduct()->getAttributeText("is_facebook");
	}
	
	/**************************  END OF FACEBOOK API FUNCTIONS ************************* */
	
	/************************* START OF TWITTER API FUNCTIONS ***********************/
	
	 /*Returns String
	   Stored twitter api key from the configuration
	 */
	public function getTwApiKey(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/twitter/twapikey');
	}
	
	 /*Returns String
	   Stored twitter secret key from the configuration
	 */
	public function getTwSecretKey(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/twitter/twsecretkey');
	}
	
	/*Returns String
	   Stored twitter oauth key from the configuration
	 */
	public function getTwOauthKey(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/twitter/twoauth');
	}
	
	/*Returns String
	   Stored twitter oauth secret key from the configuration
	 */
	public function getTwOauthSecretKey(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/twitter/twoauthsecretkey');
	}
	
	/*Returns String
	   Stored twitter configuration status from the configuration
	 */
	public function getTwStatus(){
	
	 return Mage::getStoreConfig('techinflosocialmedia/twitter/twenabled');
	}
	
	/*Returns String
	   Stored is_twitter postable from the configuration
	 */
	 public function getTwProductPostable($observer){
		return $product_data["tw_postable"] = $observer->getProduct()->getAttributeText("is_twitter");
	}
	/************************* END OF TWITTER API FUNCTIONS **************************/
	
	
	public function salesrule_save_after($observer)
	{		
		$data = $observer->getRequest()->getPost();
	
		$rule_name =  $data['name'];
		$rule_description =  $data['description'];
		$rule_status =  $data['is_active'];
		$rule_from_date =  $data['from_date'];
		$rule_to_date =  $data['to_date'];
		$rule_facebook_postable =  $data['is_facebook'];
		$rule_twitter_postable =  $data['is_twitter'];
		
		$store_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		
		$validity = 'Valid from' . ' ' . $rule_from_date . ' to ' . $rule_to_date;
		
		/*Facebook API */
		$config = array();
		$config['appId'] = $this->getApiKey();
		$config['secret'] = $this->getSecretKey();
		$config['fileUpload'] = false; // optional
		$fb = new Facebook($config);
		$access_token = $this->getAccessToken();

		/******************Facebook Post Start****************/
		/*Get the status of the facebook configuration*/
		$fb_enabled = $this->getFbStatus();
		
		/*Get the rule dates to check whether it is new or not*/
		$newFromDate = $rule_from_date;
		$newToDate = $rule_to_date;
		$now = date("Y-m-d H:m:s");
		
		$params = array(
		  "access_token" => $access_token, // this is the main access token (facebook profile)
		  "message" => $rule_description,
		  "name" => $rule_name,
		  "link" => $store_url,
		  "caption" => $rule_description,
		  "description" => $validity
		);

		if($fb_enabled) {
		/* Check if the rule is enabled */
		if ($rule_status) {

		/* Check if the rule is postable */
		if($rule_facebook_postable == 'Yes' || $rule_facebook_postable == 'yes' ){
		try {
		    $ret = $fb->api('/me/feed', 'POST', $params);
			//echo 'Successfully posted to Facebook Personal Profile';
			
		} catch(Exception $e) {
		    //echo $e->getMessage();
			//exit(0);
			}
		}
		}
	  }
	  /******************Facebook Post Start****************/
	  
	  /*Twitter API */
				
		$twitter_api_key = $this->getTwApiKey();
		$twitter_api_secret = $this->getTwSecretKey();
		$twitter_oauth_token = $this->getTwOauthKey();
		$twitter_oauth_secret = $this->getTwOauthSecretKey();
		
		define("CONSUMER_KEY", $twitter_api_key);
		define("CONSUMER_SECRET", $twitter_api_secret);
		define("OAUTH_TOKEN", $twitter_oauth_token);
		define("OAUTH_SECRET", $twitter_oauth_secret);
		
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$content = $connection->get('account/verify_credentials');
		
	/******************Twitter Post Start****************/
	
	/*Get the status of the facebook configuration*/
	$tw_enabled = $this->getTwStatus();
		
	if($tw_enabled) {
			
	/* Check if the product is enabled */
		if ($rule_status) {
		/* Check if the product is postable */
		if($rule_twitter_postable == 'Yes' || $rule_twitter_postable == 'yes' ){
		try {
			$message .= $rule_name;
			$message .= ':';
			$message .= ' ';
			$message .= $rule_description;
			$message .= '.';
			$message .= ' ';
			$message .= $validity;
		
			if(strlen($message) > 140)
			$message = substr($message,0,140);
			$post = $connection->post('statuses/update', array('status' => $message));
		   }   catch(Exception $e) {echo $e->getMessage();}
	   }
	  }
	/******************Twitter Post Ends****************/	
	}
  } 
		
	
	public function catalogrule_save_after($observer)
	{
		$data = $observer->getRequest()->getPost();

		$rule_name =  $data['name'];
		$rule_description =  $data['description'];
		$rule_status =  $data['is_active'];
		$rule_from_date =  $data['from_date'];
		$rule_to_date =  $data['to_date'];
		$rule_facebook_postable =  $data['is_facebook'];
		$rule_twitter_postable =  $data['is_twitter'];
		
		$store_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		
		$validity = 'Valid from' . ' ' . $rule_from_date . ' to ' . $rule_to_date;
		
		/*Facebook API */
		$config = array();
		$config['appId'] = $this->getApiKey();
		$config['secret'] = $this->getSecretKey();
		$config['fileUpload'] = false; // optional
		$fb = new Facebook($config);
		$access_token = $this->getAccessToken();

		/******************Facebook Post Start****************/
		/*Get the status of the facebook configuration*/
		$fb_enabled = $this->getFbStatus();
		
		/*Get the rule dates to check whether it is new or not*/
		$newFromDate = $rule_from_date;
		$newToDate = $rule_to_date;
		$now = date("Y-m-d H:m:s");
		
		$params = array(
		  "access_token" => $access_token, // this is the main access token (facebook profile)
		  "message" => $rule_description,
		  "name" => $rule_name,
		  "link" => $store_url,
		  "caption" => $rule_description,
		  "description" => $validity
		);

		if($fb_enabled) {
		/* Check if the rule is enabled */
		if ($rule_status) {

		/* Check if the rule is postable */
		if($rule_facebook_postable == 'Yes' || $rule_facebook_postable == 'yes' ){
		try {
		    $ret = $fb->api('/me/feed', 'POST', $params);
			//echo 'Successfully posted to Facebook Personal Profile';
			
		} catch(Exception $e) {
		    //echo $e->getMessage();
			//exit(0);
			}
		}
		}
	  }
	  /******************Facebook Post Start****************/
	  
	  /*Twitter API */
				
		$twitter_api_key = $this->getTwApiKey();
		$twitter_api_secret = $this->getTwSecretKey();
		$twitter_oauth_token = $this->getTwOauthKey();
		$twitter_oauth_secret = $this->getTwOauthSecretKey();
		
		define("CONSUMER_KEY", $twitter_api_key);
		define("CONSUMER_SECRET", $twitter_api_secret);
		define("OAUTH_TOKEN", $twitter_oauth_token);
		define("OAUTH_SECRET", $twitter_oauth_secret);
		
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
		$content = $connection->get('account/verify_credentials');
		
	/******************Twitter Post Start****************/
	
	/*Get the status of the facebook configuration*/
	$tw_enabled = $this->getTwStatus();
		
	if($tw_enabled) {
			
	/* Check if the product is enabled */
		if ($rule_status) {
		/* Check if the product is postable */
		if($rule_twitter_postable == 'Yes' || $rule_twitter_postable == 'yes' ){
		try {
			$message .= $rule_name;
			$message .= ':';
			$message .= ' ';
			$message .= $rule_description;
			$message .= '.';
			$message .= ' ';
			$message .= $validity;
		
			if(strlen($message) > 140)
			$message = substr($message,0,140);
			$post = $connection->post('statuses/update', array('status' => $message));
		   }   catch(Exception $e) {echo $e->getMessage();}
	   }
	  }
	/******************Twitter Post Ends****************/	
	}
  }  
  
} 
?>