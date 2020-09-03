<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;




defined('_JEXEC') or die;

class PlgJsnSocialnetwork extends JPlugin
{
	private function recurse_copy($src,$dst) { 
	    $dir = opendir($src); 
	    @mkdir($dst); 
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 
	    closedir($dir); 
	}
	
	public function __construct(& $subject, $config)
	{
		global $JSNSOCIAL;
		$JSNSOCIAL=true;
		
		// Check cache directory
		if(!file_exists(dirname(__FILE__).'/app/tmp/cache'))
		{
			mkdir(dirname(__FILE__).'/app/tmp/cache');
			mkdir(dirname(__FILE__).'/app/tmp/cache/models');
			mkdir(dirname(__FILE__).'/app/tmp/cache/persistent');
		}
		// Copy static files
		if(!file_exists(JPATH_SITE.'/media/socialnetwork'))
		{
			$this->recurse_copy(dirname(__FILE__).'/app/webroot',JPATH_SITE.'/media/socialnetwork');
		}
		
		parent::__construct($subject, $config);
		
		if(JRequest::getVar('option','')=='com_jsn' && JRequest::getVar('view','')=='social')
		{
			// Check AJAX Request
			$ajaxEnv='';
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				$ajaxEnv = $_SERVER['HTTP_X_REQUESTED_WITH'];
			} elseif (isset($_ENV['HTTP_X_REQUESTED_WITH'])) {
				$ajaxEnv = $_ENV['HTTP_X_REQUESTED_WITH'];
			} elseif (getenv('HTTP_X_REQUESTED_WITH') !== false) {
				$ajaxEnv = getenv('HTTP_X_REQUESTED_WITH');
			}
			
			$route=explode('/',JRequest::getVar('route','/'));
			$isAdmin=(isset($route[1]) && $route[1]=='admin');
			if( $ajaxEnv == 'XMLHttpRequest'/* || $isAdmin*/ || (isset($route[1]) && $route[1]=='attachments'))
			{
				JRequest::setVar('format','raw');
				JFactory::$document = JDocument::getInstance('raw');
			}
			if(isset($route[1]) && isset($route[2]) && isset($route[3]) && $route[1]=='users' && $route[2]=='view'){
				$app=JFactory::getApplication();
				$profileMenu=$app->getMenu()->getItems('link','index.php?option=com_jsn&view=profile',true);
				if(isset($profileMenu->id)) $Itemid=$profileMenu->id;
				else $Itemid='';
				$app->redirect(JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$Itemid.'&id='.$route[3],false));
			}
			if(isset($route[1]) && (substr($route[1],0,1)==':' || substr($route[1],0,1)=='-')){
				$db = JFactory::getDbo();
				$dbquery = $db->getQuery(true);
				$dbquery->select('a.id')->from('#__users AS a')->where('a.username = '. $db->quote(substr($route[1],1)));
				$db->setQuery( $dbquery );
				$id=$db->loadResult();
				$app=JFactory::getApplication();
				$app->redirect(JRoute::_('index.php?option=com_jsn&view=profile&id='.$id,false));
			}
		}
		if(JRequest::getVar('option','')=='com_jsn' && JRequest::getVar('view','profile')=='profile')
		{
			JRequest::setVar('layout','social');
			JRequest::setVar('route','/users/view/'.JFactory::getUser(JRequest::getVar('id',null))->id);
		}
		
		SocialNetwork::$search_fields=$this->params->get('search_fields',array());
		SocialNetwork::$birthday_field=$this->params->get('birthday_field','');
		SocialNetwork::$exclude_ids=$this->params->get('exclude_ids','');
		SocialNetwork::$include_groups=$this->params->get('include_groups',array());
	}
	
	public function renderPlugin()
	{
		if(JRequest::getVar('view','')=='social')
		{
			JHtml::_('jquery.framework');
			$social=SocialNetwork::getInstance();
			$doc=JFactory::getDocument();
			$doc->_custom[]=$social::$head;
			echo $social::$menu;
			echo $social::$content;
		}
		
	}
	
	public function renderBeforeFields($data, $config)
	{
		
	}
	
	public function renderAfterFields($data, $config)
	{
		
	}
	
	public function renderTabs($data, $config)
	{
		$plugin=array('<span id="profileTabSocialnetwork">'.JText::_('COM_JSN_SOCIAL_INFO').'</span>');
		$social=SocialNetwork::getInstance();
		$plugin[]= $social::$content;
		return $plugin;
	}
	
	public function renderAfterProfile($data, $config)
	{
		
	}
	
	public function renderBeforeProfile($data, $config)
	{	
		$social=SocialNetwork::getInstance();
		$doc=JFactory::getDocument();
		$doc->_custom[]=$social::$head;
		echo $social::$menu;
	}
	
	public function triggerFieldAvatarUpdate($user,$data,$changed,$isNew){
		if(!empty($data['avatar']))
		{
			SocialNetwork::$new_avatar=$data['avatar'];
			if($isNew) JRequest::setVar('route','/upload/avatarnew');
			else JRequest::setVar('route','/upload/avatar');
			$social=SocialNetwork::getInstance();
		}
	}

	public function onUserAfterDelete($user, $success, $msg)
	{
		if( SocialNetwork::$deleteUser!= -1 ){
			SocialNetwork::$deleteUser = $user->id;
			JRequest::setVar('route','/users/delete');
			$social=SocialNetwork::getInstance();
		}
	}

}

class SocialNetwork
{
	public static $doc;
	public static $head;
	//public static $body;
	public static $content;
	public static $menu;
	
	public static $birthday_field;
	public static $search_fields;
	public static $exclude_ids;
	public static $include_groups;
	
	public static $new_avatar;

	public static $deleteUser = false;
	
	public function __construct()
	{		
			$locales=JFactory::getLanguage()->getLocale();
			setlocale(LC_TIME, $locales);
			
			JHtml::register('email.cloak', array('SocialNetwork', 'cloak'));
		
			if (!defined('DS')) {
				define('DS', DIRECTORY_SEPARATOR);
			}
			if (!defined('ROOT')) {
				define('ROOT', JPATH_SITE);
			}
			if (!defined('APP_DIR')) {
				define('APP_DIR', 'plugins' . DS . 'jsn' . DS . 'socialnetwork' . DS . 'app');
			}
			if (!defined('CAKE_CORE_INCLUDE_PATH')) {
				define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'plugins' . DS . 'jsn' . DS . 'socialnetwork' . DS . 'lib');
			}
			if (!defined('WEBROOT_DIR')) {
				define('WEBROOT_DIR', 'socialnetwork');
			}
			if (!defined('WWW_ROOT')) {
				define('WWW_ROOT', JPATH_SITE. DS . 'media' . DS . 'socialnetwork' . DS);
			}
			if (!defined('CORE_PATH')) {
				define('APP_PATH', ROOT . DS . APP_DIR . DS);
				define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
			}
			if (!include(CORE_PATH . 'Cake' . DS . 'bootstrap.php')) {
				trigger_error("CakePHP core could not be found.", E_USER_ERROR);
			}
			if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] == '/favicon.ico') {
				return;
			}

			JsnApp::uses('Dispatcher', 'Routing');
			$Dispatcher = new Dispatcher();
			try{
				$Dispatcher->dispatch(new CakeRequest(), new CakeResponse(array('charset' => Configure::read('App.encoding'))));
			}
			catch (Exception $e) {
				JError::raiseError($e->getCode(),$e->getMessage());
			}
			
			SocialNetwork::$doc=str_replace('media/socialnetwork/uploads/avatars/','',SocialNetwork::$doc);
			SocialNetwork::$doc=str_replace('uploads/avatars/','',SocialNetwork::$doc);
			
			if(JRequest::getVar('format','')=='raw'){
				echo SocialNetwork::$doc;
			}
			else{
				$pattern = "/<head>(.*?)<\/head>/s";
				preg_match($pattern, SocialNetwork::$doc, $matches);
				if(isset($matches[1])) SocialNetwork::$head=$matches[1];
			
				$pattern = "/<jsnmenu>(.*?)<\/jsnmenu>/s";
				preg_match($pattern, SocialNetwork::$doc, $matches);
				if(isset($matches[1])) SocialNetwork::$menu=$matches[1];
				
				/*$pattern = "/<jsncontent>(.*?)<\/jsncontent>/s";
				preg_match($pattern, SocialNetwork::$doc, $matches);
				if(isset($matches[1])) SocialNetwork::$content=$matches[1];*/
				SocialNetwork::$content=substr(SocialNetwork::$doc,strpos(SocialNetwork::$doc,'<jsncontent>')+12);
				SocialNetwork::$content=substr(SocialNetwork::$content,0,strpos(SocialNetwork::$content,'</jsncontent>'));
			}
	}
	
	public static function getInstance()
	{
		static $cache=null;
		if (!isset($cache))
		{
			$cache=new SocialNetwork();
		}
		return $cache;
	}
	
	public static function cloak($mail, $mailto = true, $text = '', $email = true)
	{
		// Convert text
		$mail = SocialNetwork::convertEncoding($mail);

		// Split email by @ symbol
		$mail = explode('@', $mail);
		$mail_parts = explode('.', $mail[1]);

		// Random number
		$rand = rand(1, 100000);

		$replacement = '<div id="cloak'.$rand.'">'.JText::_('JLIB_HTML_CLOAKING').'</div>'."<script type='text/javascript'>";
		$replacement .= "\n <!--";
		$replacement .= "\n jQuery('#cloak$rand').html('');";
		$replacement .= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
		$replacement .= "\n var path = 'hr' + 'ef' + '=';";
		$replacement .= "\n var addy" . $rand . " = '" . @$mail[0] . "' + '&#64;';";
		$replacement .= "\n addy" . $rand . " = addy" . $rand . " + '" . implode("' + '&#46;' + '", $mail_parts) . "';";

		if ($mailto)
		{
			// Special handling when mail text is different from mail address
			if ($text)
			{
				if ($email)
				{
					// Convert text
					$text = static::convertEncoding($text);

					// Split email by @ symbol
					$text = explode('@', $text);
					$text_parts = explode('.', $text[1]);
					$replacement .= "\n var addy_text" . $rand . " = '" . @$text[0] . "' + '&#64;' + '" . implode("' + '&#46;' + '", @$text_parts)
						. "';";
				}
				else
				{
					$replacement .= "\n var addy_text" . $rand . " = '" . $text . "';";
				}

				$replacement .= "\n jQuery('#cloak$rand').append('<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>'+addy_text" . $rand . "+'<\/a>');";
			}
			else
			{
				$replacement .= "\n jQuery('#cloak$rand').append('<a ' + path + '\'' + prefix + ':' + addy" . $rand . " + '\'>');";
				$replacement .= "\n jQuery('#cloak$rand').append(addy" . $rand . ");";
				$replacement .= "\n jQuery('#cloak$rand').append('<\/a>');";
			}
		}
		else
		{
			$replacement .= "\n jQuery('#cloak$rand').append(addy" . $rand . ");";
		}

		$replacement .= "\n //-->";
		$replacement .= '\n </script>';

		return $replacement;
	}

	protected static function convertEncoding($text)
	{
		// Replace vowels with character encoding
		$text = str_replace('a', '&#97;', $text);
		$text = str_replace('e', '&#101;', $text);
		$text = str_replace('i', '&#105;', $text);
		$text = str_replace('o', '&#111;', $text);
		$text = str_replace('u', '&#117;', $text);

		return $text;
	}
	
}



?>