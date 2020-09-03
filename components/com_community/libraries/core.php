<?php

defined('_JEXEC') or die;

/*$app=JFactory::getApplication();
if($app->isAdmin() && JRequest::getVar('option','')=='com_users') require_once(JPATH_SITE.'/components/com_community/libraries/fault.php');
else */require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');


class CFactory
{
	public static function load(){
		return true;
	}
	public static function getUser($id=null)
	{
		$user=new CUser($id);
		if($id==0) $user->extra_anonymous=1;
		else $user->extra_anonymous=0;
		return $user;
	}
}

class CActivityStream
{
	public static function add(){
		return true;
	}
}

class CRoute
{
	public static function _($url) {
		$url=str_replace('com_community', 'com_jsn', $url);
		$url=str_replace('userid', 'id', $url);
		//var_dump($url);//die();
		return JRoute::_($url,false);
	}
}

class CUser extends JsnUser
{
	public function getDisplayName() {
		return $this->name;
	}
	public function getFriendCount() {
		return 0;
	}
	public function getViewCount() {
		return 0;
	}
	public function getStatus() {
		return '';
	}
	public function getAvatar() {
		if($this->extra_anonymous){
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('params')->from('#__jsn_fields')->where('alias=\'avatar\'');
			$db->setQuery($query);
			$params=$db->loadResult();
			$registry = new JRegistry;
			$registry->loadString($params);
			$params = $registry->toArray();
			if(!empty($params['image_defaultvalue']))
				$this->avatar=$params['image_defaultvalue'];
			else
				$this->avatar='components/com_jsn/assets/img/default.jpg';
		}
		if(empty($this->avatar_clean)) return JUri::root().$this->avatar.'" avatar="'.htmlentities($this->getFormatName()).'"';
		return JUri::root().$this->avatar;
	}
	
	public function getThumbAvatar() {
		if($this->extra_anonymous){
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('params')->from('#__jsn_fields')->where('alias=\'avatar\'');
			$db->setQuery($query);
			$params=$db->loadResult();
			$registry = new JRegistry;
			$registry->loadString($params);
			$params = $registry->toArray();
			if(!empty($params['image_defaultvalue']))
				$this->avatar_mini=$params['image_defaultvalue'];
			else
				$this->avatar_mini='components/com_jsn/assets/img/default.jpg';
		}
		if(empty($this->avatar_clean)) return JUri::root().$this->avatar_mini.'" avatar="'.htmlentities($this->getFormatName()).'"';
		return JUri::root().$this->avatar_mini;
	}
}

class CUserPoints
{
	public static function assignPoint($var1,$var2=null,$var3=null) {
		return true;
	}
}
?>