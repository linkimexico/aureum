<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class User extends AppModel
{
	public $belongsTo = array( 'Role' );
	
	// Identical field validation rule
	public function identicalFieldValues( $field=array(), $compare_field=null ) 
    {
        foreach( $field as $key => $value ){
            $v1 = $value;
            $v2 = $this->data[$this->name][ $compare_field ];                 
            if($v1 !== $v2) {
                return FALSE;
            } else {
                continue;
            }
        }
        return TRUE;
    } 
	
	// Hash the password before saving user data
	public function beforeSave( $options = array() )
	{
		if ( !empty( $this->data['User']['password'] ) )
			$this->data['User']['password'] = md5( $this->data['User']['password'] . Configure::read('Security.salt') );
			
		return true;
	}
	
	/*
	 * Get current online users
	 * @param int $interval - interval to check
	 * @return array $res
	 */
	public function getOnlineUsers( $limit = 12, $interval = 1200 )
	{
		static $res=null;
		if($res) return $res;
		$userids = array();
		$guests = 0;
		$time = time() - intval($interval);
		//$sessions = $this->query( 'SELECT data FROM ' . $this->tablePrefix . 'cake_sessions WHERE expires > ' . $time );
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('userid')->from('#__session')->where('client_id=0')->setLimit($limit);
		$db->setQuery($query);
		$results=$db->loadColumn();
		foreach($results as $result)
		{
			if($result==0) $guests++;
			else $userids[]=$result;
		}
		
		$members = array();
		if ( !empty( $userids ) )
            $members = $this->find( 'all', array( 'conditions' => array( 'User.id' => $userids, 'User.hide_online' => 0 ), 'limit' => intval($limit) ) );
		
		$total = $guests + count( $userids );
		
		$res = array( 'guests'    => $guests,
					  'members'   => $members,
					  'total'     => $total,
					  'userids'   => $userids
					);
		
		return $res;
	}
	
	/*
	 * Get array of users based on $conditions
	 * @param int $page
	 * @param array $conditions
	 * @return array $users
	 */
	public function getUsers( $page = 1, $conditions = null, $limit = RESULTS_LIMIT )
	{		
		if ( empty( $conditions ) )
			$conditions = array( 'User.active' => 1 );
			
		/* Remove Users excluded */
		if(SocialNetwork::$exclude_ids!='') $conditions[]='User.id NOT IN ('.SocialNetwork::$exclude_ids.')';
		if(is_array(SocialNetwork::$include_groups) && count(SocialNetwork::$include_groups)) $conditions['Usergroups.group_id']= SocialNetwork::$include_groups;
			
		$users = $this->find('all', array( 'conditions' => $conditions, 
										   'limit' 		=> $limit,
										   'page'  		=> $page,
										   'order' 		=> 'User.id desc'
								)	);
		return $users;
	}
	
	public function afterFind($results,$primary=false){
		global $SOCIALNETWORK_DEFAULT_PRIVACY;
		require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
		foreach($results as &$result){
			if((isset($result['User']) && isset($result['User']['id'])) || (isset($result['Profile']) && isset($result['Profile']['id'])))
			{
				if(!isset($result['User']['id']))
				{
					$role_id=2;
					$db=JFactory::getDbo();
					$query="SELECT user_id FROM #__user_usergroup_map WHERE user_id=".$result['Profile']['id']." AND group_id=8";
					$db->setQuery($query);
					if($user_id=$db->loadResult()){
						$role_id=1;
					}
					$query="INSERT INTO #__jsnsocial_users(id,privacy,role_id) VALUES(".$result['Profile']['id'].",".$SOCIALNETWORK_DEFAULT_PRIVACY.",".$role_id.")";
					$db->setQuery($query);
					$db->execute();
					$result['User']['id']=$result['Profile']['id'];
					$result['User']['role_id']=$role_id;
					$result['User']['photo_count']=0;
					$result['User']['friend_count']=0;
					$result['User']['notification_count']=0;
					$result['User']['friend_request_count']=0;
					$result['User']['blog_count']=0;
					$result['User']['topic_count']=0;
					$result['User']['conversation_user_count']=0;
					$result['User']['video_count']=0;
					$result['User']['hide_online']=0;
					$result['User']['cover']='';
					$result['User']['featured']=0;
					$result['User']['privacy']=$SOCIALNETWORK_DEFAULT_PRIVACY;
				}
				$jsnUser=JsnHelper::getUser($result['User']['id']);
				$result['User']['active']=!$jsnUser->block;
				$result['User']['username']=$jsnUser->username;
				$result['User']['name']=$jsnUser->getFormatName();
				$result['User']['photo']=$jsnUser->avatar;
				$result['User']['avatar']=$jsnUser->avatar_mini;
				$result['User']['avatar_clean']=$jsnUser->avatar_clean;
				$result['User']['email']=$jsnUser->email;
				$result['User']['created']=$jsnUser->registerDate;
				$result['User']['last_login']=$jsnUser->lastvisitDate;
				$result['User']['online']=JsnHelper::isOnLine((int) $result['User']['id']);
				if(isset($jsnUser->params['timezone']) && !empty($jsnUser->params['timezone'])) $result['User']['timezone']=$jsnUser->params['timezone'];
				else $result['User']['timezone']=JFactory::getApplication()->getCfg('offset');
				if(isset($jsnUser->params['language']) && !empty($jsnUser->params['language'])) $result['User']['lang']=$jsnUser->params['language'];
				else $result['User']['lang']=JFactory::getLanguage()->getTag();
			}
			if((isset($result['Sender']) && isset($result['Sender']['id'])))
			{
				$jsnUser=JsnHelper::getUser($result['Sender']['id']);
				$result['Sender']['active']=!$jsnUser->block;
				$result['Sender']['username']=$jsnUser->username;
				$result['Sender']['name']=$jsnUser->getFormatName();
				$result['Sender']['photo']=$jsnUser->avatar;
				$result['Sender']['avatar']=$jsnUser->avatar_mini;
				$result['Sender']['avatar_clean']=$jsnUser->avatar_clean;
				$result['Sender']['email']=$jsnUser->email;
				$result['Sender']['created']=$jsnUser->registerDate;
				$result['Sender']['last_login']=$jsnUser->lastvisitDate;
				$result['Sender']['online']=JsnHelper::isOnLine((int) $result['Sender']['id']);
				if(isset($jsnUser->params['timezone']) && !empty($jsnUser->params['timezone'])) $result['Sender']['timezone']=$jsnUser->params['timezone'];
				else $result['Sender']['timezone']=JFactory::getApplication()->getCfg('offset');
				if(isset($jsnUser->params['language']) && !empty($jsnUser->params['language'])) $result['Sender']['lang']=$jsnUser->params['language'];
				else $result['Sender']['lang']=JFactory::getLanguage()->getTag();
			}
			if((isset($result['LastPoster']) && isset($result['LastPoster']['id'])))
			{
				$jsnUser=JsnHelper::getUser($result['LastPoster']['id']);
				$result['LastPoster']['active']=!$jsnUser->block;
				$result['LastPoster']['username']=$jsnUser->username;
				$result['LastPoster']['name']=$jsnUser->getFormatName();
				$result['LastPoster']['photo']=$jsnUser->avatar;
				$result['LastPoster']['avatar']=$jsnUser->avatar_mini;
				$result['LastPoster']['avatar_clean']=$jsnUser->avatar_clean;
				$result['LastPoster']['email']=$jsnUser->email;
				$result['LastPoster']['created']=$jsnUser->registerDate;
				$result['LastPoster']['last_login']=$jsnUser->lastvisitDate;
				$result['LastPoster']['online']=JsnHelper::isOnLine((int) $result['LastPoster']['id']);
				if(isset($jsnUser->params['timezone']) && !empty($jsnUser->params['timezone'])) $result['LastPoster']['timezone']=$jsnUser->params['timezone'];
				else $result['LastPoster']['timezone']=JFactory::getApplication()->getCfg('offset');
				if(isset($jsnUser->params['language']) && !empty($jsnUser->params['language'])) $result['LastPoster']['lang']=$jsnUser->params['language'];
				else $result['LastPoster']['lang']=JFactory::getLanguage()->getTag();
			}
		}
		return $results;
	}
	
	public function save($data = null, $validate = true, $fieldList = array()){
		if(isset($data['id']) && isset($data['active'])){
			if(isset($data['id'])) $id=$data['id'];
			else $id=$this->id;
			$block=($data['active'] ? 0 : 1);
			$db=JFactory::getDbo();
			$query="UPDATE #__users SET block=".$block." WHERE id=".$id;
			$db->setQuery($query);
			$db->execute();
			unset($data['active']);
		}
		parent::save($data,$validate,$fieldList);
	}
	
	/*
	 * Remove user's avatar files
	 * @param object $user
	 */
	public function removeAvatarFiles( $user )
	{
		$path = WWW_ROOT . 'uploads' . DS . 'avatars';
		
		if ($user['photo'] && file_exists($path . DS .$user['photo']))
			unlink($path . DS . $user['photo']);
			
		if ($user['avatar'] && file_exists($path . DS .$user['avatar']))
			unlink($path . DS . $user['avatar']);
	}
    
    /*
     * Remove user's cover file
     * @param object $user
     */
    public function removeCoverFile( $user )
    {
        $path = WWW_ROOT . 'uploads' . DS . 'covers';
        
        if ($user['cover'] && file_exists($path . DS .$user['cover']))
            unlink($path . DS . $user['cover']);
    }
	
	public function getTodayBirthday()
	{
		if(SocialNetwork::$birthday_field=='') return array();
		
		$birthday_users = Cache::read('birthday_users');
        
        /*if ( !is_array( $birthday_users ) ) 
        {
            $today = date('m-d');
            $birthday_users = $this->find( 'all', array( 'conditions' => array( 'active' => 1, 'SUBSTRING(birthday, 6)' => $today ) ) );
        
            Cache::write('birthday_users', $birthday_users);
        }*/
		if ( !is_array( $birthday_users ) ) 
        {
			$today = date('m-d');
			$users=array();
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('id')->from('#__jsn_users AS a')->where('SUBSTR(a.'.SocialNetwork::$birthday_field.', 6,5)="'.$today.'"');
			$db->setQuery($query);
			$results=$db->loadColumn();
			foreach($results as $result)
			{
				$users[]=$result;
			}
			$birthday_users = $this->find( 'all', array( 'conditions' => array( 'User.active' => 1, 'User.id' => $users ) ) );
			Cache::write('birthday_users', $birthday_users);
		}
		return $birthday_users;
	}
	
	public function getLatestUsers( $limit = 10 )
	{
		$conditions = array('active' => 1);
		if(SocialNetwork::$exclude_ids!='') $conditions[]='User.id NOT IN ('.SocialNetwork::$exclude_ids.')';

		$users = $this->find( 'all', array( 'conditions' => $conditions, 
											'order' => 'User.id desc', 
											'limit' => $limit
							)	);
		
		return $users;
	}
	
	public function getFeaturedUsers()
	{
		$users = $this->find( 'all', array( 'conditions' => array( 'active' => 1, 'featured' => 1 ), 
											'order' => 'User.id desc', 
											'limit' => 10
							)	);
		
		return $users;
	}

	public function findById( $id = 0)
	{
		$cuser = parent::findById($id);
		$juser = JFactory::getUser($id);
            if($juser->authorise('core.admin')){
			    $cuser['Role']['is_admin'] = 1;
			    $cuser['Role']['is_super'] = 1;
			}   
		return $cuser;
	}
}
