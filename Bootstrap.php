<?php
ob_start();
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initRegistry(){
		$this->bootstrap('db');
		$db = $this->getResource('db');
		$db->setFetchMode(Zend_Db::FETCH_OBJ);
		Zend_Registry::set('db', $db);
	}
	
    protected function _initAutoload ()
    { 
	
		//code by saroj for store session in db

		//if($request->getModuleName()!='default')
		//{
		//echo "sdfsd".$request->getModuleName();exit;
		$this->bootstrap('db'); 
		//$this->bootstrap('session');
		//end code by saroj
		Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');
		Zend_Session::setOptions(array('cookie_domain' => '.goo2oapps.com'));
		Zend_Session::start();
		//}
		//echo 'dfdsf';exit;
		/*echo "<pre>";
		print_r($_SESSION);
		echo "<pre>";*/
		//echo "<pre>";
		//print_r( $_REQUEST );
		
		$SESSION = new Zend_Session_Namespace('SESSION');
		
		if($_REQUEST['unsetrequest']==1)
		{
		
			unset($_REQUEST);
			unset($_SESSION);
			
		}
		$jsonString=$_REQUEST['sessionvalue'];
		if(!empty($_REQUEST['sessionvalue'])){
	
			if( get_magic_quotes_gpc() ) {
 			   $jsonString = stripslashes($jsonString);
			   $sessCode = Zend_Json::decode($jsonString);
			   $_SESSION['USERSESSION'] = $sessCode;
			 
			    $_SESSION['authid'] = trim($_REQUEST['authid']);
			  // print_r($sessCode['userDetails'][0]);
			  $SESSION->sess = $sessCode['userDetails'][0];
			 // echo $SESSION->id;
			//	print_r($SESSION);
		}
		}
		if($_SESSION['authid']=='')
		{
		
			header('Location:http://o2ocheckout.com/secure/login?request='.$this->curPageURL()."&self=1");
		}
		else
		{
		$url = "http://o2ocheckout.com/api/services/session/type/xml/session_id/".$_SESSION['authid'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch,CURLOPT_USERPWD, 'skywalker:dexter');
		$abc = curl_exec($ch);
		//print_r($abc );
		$xmldata = simplexml_load_string($abc);
		//$jsondata = $this->xml2array($xmldata);
		$xmlArrayData = $this->xml2array($xmldata);
		//echo 'test<pre>';print_r($xmlArrayData);;
		//return $jsondata;
		curl_close($ch);
		$xml = json_decode($abc);
		}
		
		
		//$SESSION = new Zend_Session_Namespace('SESSION');
		$USER = new Zend_Session_Namespace('USER');
		$CART = new Zend_Session_Namespace('CART');
		$autoloader = new Zend_Application_Module_Autoloader(
		array('namespace' => 'Default', 
		'basePath' => APPLICATION_PATH . '/modules/default'));
		//echo $SESSION->sessionId = Zend_Session::getId();
		//$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/logs/admin-app.log');
        //$logger = new Zend_Log($writer);
		
      //  Zend_Registry::set("log", $logger);
		include_once ('loader.php');
		require_once ('Zend/Cache.php');
		include('constants.php');
		include('generic_functions.php');
		include('jsLibrary.php');
                include('DML.php');
				$GENERAL_OBJ = new General();
				
                return $autoloader;
				
    }
	function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
	public function xml2array($xml) {
		$arr = '';
		foreach ($xml as $element) {
			$tag = $element->getName();
			$e = get_object_vars($element);
			if (!empty($e)) {
			 $arr[] = $element instanceof SimpleXMLElement ? $this->xml2array($element) : $e;
			} else {
			 $arr[$tag] = trim($element);
			}
		}
		//echo '<pre>';print_r($arr);
		return $arr;
	}
	
	
}
