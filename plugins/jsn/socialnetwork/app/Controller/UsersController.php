<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class UsersController extends AppController {
	
	public $paginate = array( 'order' => 'User.id desc', 'limit' => RESULTS_LIMIT );

	public function index()
	{
		$uid = $this->Session->read('uid');			
		$users = $this->User->getUsers();
		
		if ( !empty( $uid ) )
		{
			$this->loadmodel( 'Friend' );
			$this->loadModel( 'FriendRequest' );
			
			$friends = $this->Friend->getFriends( $uid );			
			$requests = $this->FriendRequest->getRequestsList( $uid );
			
			$friends_requests = array_merge($friends, $requests);
			
			$this->set('friends', $friends);
			$this->set('friends_requests', $friends_requests);
		}
							
		$this->loadModel('ProfileField');		
		$custom_fields = $this->ProfileField->find( 'all', array( 'conditions' => array( 'active' => 1,
																						 'searchable' => 1
												 ) ) );
												 
		// search value
		$values = array();
		foreach ( $this->request->named as $field => $value )
		{
			if ( strpos( $field, 'field_' ) === 0 && !empty( $value ) )
			{
				$field_id = explode( '_', $field );
				$field_id = $field_id[1];
				$values[$field_id] = array( 'id' => $field_id, 'value' => urldecode($value) );
			}
            
            if ( $field == 'online' )
                $this->set('online_filter', true);  
		}
		
		$this->set('custom_fields', $custom_fields);
		$this->set('values', $values);		
		$this->set('users', $users);
		$this->set('title_for_layout', __('People'));
	}
	
	/*
	 * Browse users based on $type
	 * @param string $type - possible value: all (default), friends, search, home
	 */
	public function ajax_browse( $type = null, $param = null )
	{
		$uid = $this->Session->read('uid');
		$this->loadmodel( 'Friend' );		
		
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		//$url  = ( !empty( $uid ) )	? $type . '/' . $uid : $type;
		
		switch ( $type )
		{
			case 'home':
			case 'friends':
				$this->_checkPermission();				
				$users = $this->Friend->getUserFriends( $uid, $page );				
					
				break;
				
			case 'search':
				$jsnsocial_setting = $this->_getSettings();
				
				if ( !$jsnsocial_setting['guest_search'] && empty( $uid ) )
					$this->_checkPermission();
									
				$params = array('User.active' => 1);
				
				
				

				if (!empty($this->request->data))
				{	
					JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jsn/models', 'JsnModel');
					$model = JModelLegacy::getInstance('List', 'JsnModel', array('ignore_request' => true));
					$model->filter=true;
					
					// Set application parameters in model
					$app = JFactory::getApplication();
					$appParams = $app->getParams();
					$appParams->set('orderDir','ASC');
					$model->setState('params', $appParams);

					// Set the filters based on the module params
					$model->setState('list.start', (int) ($page-1)*RESULTS_LIMIT);
					$model->setState('list.limit', (int) RESULTS_LIMIT);

					$appParams->set('orderDir','DESC');
					$appParams->set('orderCol','id');
					$appParams->set('where',null);

					$items = $model->getItems();
					$params['User.id']=array();
					foreach($items as $item)
					{
						$params['User.id'][]=$item->id;
					}
				/*$profile_params = array();
				$joins = array();
				$user_ids = array();
				$i = '';
	
				if (!empty($this->request->data))
				{
					if ( !empty($param) ) // ajax url search
                        $this->request->data['name'] = $param;
                        			
					if ( !empty($this->request->data['gender']) )
						$params['User.gender'] = $this->request->data['gender'];
	
					if ( !empty($this->request->data['email']) )
						$params['User.email'] = $this->request->data['email'];
					
					if ( !empty($this->request->data['picture']) )
						$params['User.avatar <> ?'] = '';
					
					if ( !empty($this->request->data['name']) )
						$params['MATCH(User.name) AGAINST(? IN BOOLEAN MODE)'] = urldecode($this->request->data['name']);
	
					// custom fields
					foreach ($this->request->data as $field => $value)
					{
						if ( strpos( $field, 'field_' ) === 0 && !empty( $value ) )
						{
							$field_id = explode( '_', $field );
							$field_id = $field_id[1];
							
							$profile_params['ProfileFieldValue'.$i.'.profile_field_id'] = $field_id;
							if (is_array($value))
							{
								$value = implode(' ', $value);
								$profile_params['MATCH(ProfileFieldValue'.$i.'.value) AGAINST(? IN BOOLEAN MODE)'] = urldecode($value);
							}
							else
								$profile_params['ProfileFieldValue'.$i.'.value'] = $value;
	
							if ($i >= 1)
								$joins[] = array( 'table' => 'profile_field_values', 
												  'alias' => 'ProfileFieldValue'.$i,	
												  'type' => 'INNER', 
												  'conditions' => array( 'ProfileFieldValue.user_id = ProfileFieldValue'.$i.'.user_id' )
												);
	
							$i = (int)$i + 1;
						}
					}
	
					if (!empty($profile_params))
					{
						$this->loadModel( 'ProfileFieldValue' );
						$user_ids = $this->ProfileFieldValue->find('list', array( 'conditions' => $profile_params, 
																				  'joins' => $joins, 
																				  'fields' => array('user_id') 
																	) );
	
						$params['User.id'] = $user_ids;
					}
                    
                    if ( !empty($this->request->data['online']) )
                    {
                        $online = $this->User->getOnlineUsers();
                        
                        if ( !empty( $user_ids ) )
                            $params['User.id'] = array_intersect($user_ids, $online['userids']);
                        else
                            $params['User.id'] = $online['userids'];
                        
                        // hide invisible users
                        $params['User.hide_online'] = 0;
                    }*/
					
					$users = $this->User->getUsers( 1, $params );
				}	
					
				break;
				
			default:
				$users = $this->User->getUsers( $page );
		}		

		// get current user friends and requests
		if ( !empty( $uid ) && in_array( $type, array( 'search', 'all' ) ) )
		{
			$this->loadModel( 'FriendRequest' );
			
			$friends = $this->Friend->getFriends( $uid );			
			$requests = $this->FriendRequest->getRequestsList( $uid );
			
			$friends_requests = array_merge($friends, $requests);
			
			$this->set('friends', $friends);
			$this->set('friends_requests', $friends_requests);
		} 
		
		$this->set('users', $users);
		$this->set('type', $type);
		$this->set('more_url', '/users/ajax_browse/' . h($type) . '/page:' . ( $page + 1 ) );
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_user');
		else
			$this->render('/Elements/lists/users_list');
	}
	
	public function login()
	{
		$this->autoRender = false;
		
		// log user in
		if ( !$this->_logMeIn( $this->request->data['email'], $this->request->data['password'], $this->request->data['remember'] ) )
		{
			$this->Session->setFlash( __('Invalid email or password'), 'default', array('class' => 'error-message'));
			$this->redirect( $this->referer() );
		}
				
		$url = $this->referer();
		
		// redirect to the previous page
		if ( !empty( $this->request->data['return_url'] ) )
		{
			$this->redirect( base64_decode( $this->request->data['return_url'] ) );
		}
		elseif ( strpos( $url, 'no-permission' ) === false && strpos( $url, 'error' ) === false && 
			 	 strpos( $url, 'recover' ) === false && strpos( $url, 'resetpass' ) === false )
		{
			$this->redirect( $url );
		}
		else
			$this->redirect( '/' );
	}

	public function do_logout()
	{
		// clean the sessions
		$this->Session->delete('uid');
        $this->Session->delete('admin_login');

		// delete cookies
		$this->Cookie->delete('email');
		$this->Cookie->delete('password');
		
		$app = JFactory::getApplication();
		$error = $app->logout();

		$this->redirect( '/' );
	}
	
	public function register()
	{				
		$uid = $this->Session->read('uid');
		
		if ( empty( $uid ) )
		{
			// check if registration is disabled
			$jsnsocial_setting = $this->_getSettings();
            
            if ( !empty( $jsnsocial_setting['site_offline'] ) )
                return;
			
			if ( $jsnsocial_setting['disable_registration'] )
                $this->_showError( __('The admin has disabled registration on this site') );
            
            // load spam challenge if enabled
            if ( $jsnsocial_setting['enable_spam_challenge'] )
            {
                $this->loadModel('SpamChallenge');                
                $challenges = $this->SpamChallenge->findAllByActive(1);
                
                if ( !empty( $challenges ) )
                {
                    $rand = array_rand( $challenges );
                    
                    $this->Session->write('spam_challenge_id', $challenges[$rand]['SpamChallenge']['id']);
                    $this->set('challenge', $challenges[$rand]);
                }
            }
		  
            $this->set('no_right_column', true);
			$this->set('title_for_layout', __('Registration'));
			
			$this->render('/Elements/registration');
		}
		else
			$this->redirect( '/' );
	}

	public function ajax_signup_step1()
	{
		$jsnsocial_setting = $this->_getSettings();	
		
		// check registration code		
		if ( $jsnsocial_setting['enable_registration_code'] && $this->request->data['registration_code'] != $jsnsocial_setting['registration_code'] )
		{
			$this->autoRender = false;
			echo '<span id="jsnsocialError">' . __('Invalid registration code') . '</span>';
			return;
		}
			
		$this->User->set( $this->request->data );
		
	    if ( $this->User->validates() )
	    {
	    	$this->loadModel('ProfileField');		
	    	$custom_fields = $this->ProfileField->getRegistrationFields();				
			$this->set('custom_fields', $custom_fields);
	    }
	    else
	    {
	    	$this->autoRender = false;
	    	$errors = $this->User->invalidFields();
	    	
	    	echo '<span id="jsnsocialError">' . current( current( $errors ) ) . '</span>';
	    }
	}

	public function ajax_signup_step2()
	{			
		$this->autoRender = false;
		$jsnsocial_setting = $this->_getSettings();
        
        // check spam challenge
        if ( $jsnsocial_setting['enable_spam_challenge'] )
        {
            $this->loadModel('SpamChallenge');
            
            $challenge = $this->SpamChallenge->findById( $this->Session->read('spam_challenge_id') );            
            $answers = explode("\n", $challenge['SpamChallenge']['answers']);
                
            $found = false;
            foreach ( $answers as $answer )
            {
                if ( strtolower( trim($answer) ) == strtolower( $this->request->data['spam_challenge'] ) )
                    $found = true;
            }
            
            if ( !$found )
            {
                echo __('Invalid security question');
                return;
            }
        }
		
        // check captcha
		if ( $jsnsocial_setting['recaptcha'] && !empty( $jsnsocial_setting['recaptcha_publickey'] ) && !empty( $jsnsocial_setting['recaptcha_privatekey'] ) )
		{
			// check captcha
			JsnApp::import('Vendor', 'recaptchalib');
			$resp = recaptcha_check_answer ( $jsnsocial_setting['recaptcha_privatekey'],
											 $_SERVER["REMOTE_ADDR"],
											 $_POST["recaptcha_challenge_field"],
											 $_POST["recaptcha_response_field"]);
	
			if (!$resp->is_valid)
			{
				echo __('Invalid security code');
				return;
			}
		}
		
		$this->_saveRegistration( $this->request->data );		
	}

	private function _saveRegistration( $data )
	{
		$jsnsocial_setting = $this->_getSettings();
		
		// check if registration is disabled			
		if ( $jsnsocial_setting['disable_registration'] )
		{
			echo '<span id="jsnsocialError">' . __('The admin has disabled registration on this site') . '</span>';
			return;
		}

		// check registration code			
		if ( $jsnsocial_setting['enable_registration_code'] && $data['registration_code'] != $jsnsocial_setting['registration_code'] )
		{
			echo '<span id="jsnsocialError">' . __('Invalid registration code') . '</span>';
			return;
		}
			
		$data['role_id']    = ROLE_MEMBER;
		$data['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$data['code'] 	    = md5( $data['email'] . microtime() );
		$data['confirmed']  = ( $jsnsocial_setting['email_validation'] ) ? 0 : 1;
		$data['last_login'] = date("Y-m-d H:i:s");
		$data['privacy']    = $jsnsocial_setting['profile_privacy'];
        $data['featured']   = 0;
        $data['username']   = '';
		
		$this->User->set( $data );
		
		if ( !$this->User->validates() )
	    {
	    	$errors = $this->User->invalidFields();	    	
	    	echo '<span id="jsnsocialError">' . current( current( $errors ) ) . '</span>';
			return;
	    }
		
		// check custom required fields
		$this->loadModel('ProfileField');					
		$custom_fields = $this->ProfileField->getRegistrationFields( true );
		
		foreach ($custom_fields as $field)
		{
			$value = $data['field_' . $field['ProfileField']['id']];
			
			if ( $field['ProfileField']['required'] && empty( $value ) && !is_numeric( $value ) )
			{
				echo $field['ProfileField']['name'] . __(' is required');
				return;
			}
		}

		if ( $this->User->save() ) // successfully saved
		{				
			// save profile field values
			$this->loadModel('ProfileFieldValue');	
			
			foreach ($custom_fields as $field)
			{
				$value = $data['field_' . $field['ProfileField']['id']];
				$value = ( is_array( $value ) ) ? implode( ', ', $value ) : $value;
				
				$this->ProfileFieldValue->create();
				$this->ProfileFieldValue->save( array( 'user_id' 		  => $this->User->id,
													   'profile_field_id' => $field['ProfileField']['id'],
													   'value' 			  => $value
											) 	);
			}
			
			// insert into activity feed
			$this->loadModel( 'Activity' );
			$this->Activity->save( array( 'type' 	=> APP_USER,
										  'action'  => 'user_create',
										  'user_id' => $this->User->id
								) );
			
			// Send an email to user
			$this->_sendEmail( $data['email'], 
							   __('Welcome to') . ' ' . $jsnsocial_setting['site_name'], 
							   'welcome_user', 
							   array( 'email' 	  => $data['email'],
									  'password'  => $data['password'],
									  'name' 	  => $data['name'],
							   		  'code' 	  => $data['code'],
							   		  'confirmed' => $data['confirmed']
							)	);
							
			// Send an email to admin if enabled
			if ( $jsnsocial_setting['registration_notify'] )				
				$this->_sendEmail( $jsnsocial_setting['site_email'], 'New Registration', null, null, null, null, '<a href="' . FULL_BASE_URL . $this->request->base . '/users/view/' . $this->User->id . '">' . $data['name'] . '</a> has just signed up on ' . $jsnsocial_setting['site_name'] );
			
			// Log user in
			$this->Session->write('uid', $this->User->id);
			
			if ( $jsnsocial_setting['email_validation'] )
				$this->Session->setFlash( __('An email has been sent to your email address<br />Please click the validation link to confirm your email') );
            
            return $this->User->id;
		}
		else
			echo __('Something went wrong. Please contact the administrators');
	}
	
	public function fb_register()
	{
		$this->loadModel('ProfileField');					
		$custom_fields = $this->ProfileField->getRegistrationFields( true );
		
		$fields = array( array( 'name' => 'name' ), 
						 array( 'name' => 'email' ),
						 array( 'name' => 'gender' ), 
						 array( 'name' => 'birthday' ),
						 array( 'name' => 'password' ),   
		);
		
		$jsnsocial_setting = $this->_getSettings();
		
		foreach ( $custom_fields as $field )
		{
			$options = array();
			
			if ( $field['ProfileField']['type'] == 'list' || $field['ProfileField']['type'] == 'multilist' )
			{
				$type = 'select';
				$values = explode("\n", $field['ProfileField']['values']);
				
				foreach ( $values as $val )
					$options[$val] = $val;
			}
			else				
				$type = 'text';			
			
			$tmp = array( 'name' 		=> 'field_' . $field['ProfileField']['id'], 
						  'description' => $field['ProfileField']['name'],
						  'type' 		=> $type							   
			);	
			
			if ( !empty( $options ) )
				$tmp['options'] = $options;				
			
			$fields[] = $tmp;
		}
		
		// handle registration code
		if ( $jsnsocial_setting['enable_registration_code'] )
			$fields[] = array( 'name' 		 => 'registration_code', 
							   'description' => __('Registration Code'),
							   'type' 		 => 'text'
							 );
		
		$fields[] = array( 'name' => 'captcha' );
		
		$this->set( 'fields', json_encode( $fields ) );
		$this->set( 'title_for_layout', __('Register with your Facebook account') );
	}

	public function do_fb_register()
	{
		$jsnsocial_setting = $this->_getSettings();
		$signed_request = $_REQUEST['signed_request'];
			
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

		// decode the data
		$sig = $this->_base64_url_decode( $encoded_sig );
		$data = json_decode( $this->_base64_url_decode($payload), true );
		
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			$this->Session->setFlash('An error has occurred (01)');
			$this->redirect( $this->referer() );
		}
		
		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $jsnsocial_setting['fb_app_secret'], $raw = true);
		if ($sig !== $expected_sig) {
			$this->Session->setFlash('An error has occurred (02)');
			$this->redirect( $this->referer() );
		}
		
		$reg_data = $data['registration'];
		
		// check to see if user already has an account here
		$user = $this->User->findByEmail( $reg_data['email'] );
		
		if ( empty( $user ) )
		{
			$tmp = explode('/', $reg_data['birthday']);
			
			$reg_data['birthday']  = array( 'year' => $tmp[2], 'month' => $tmp[0], 'day' => $tmp[1] );				
			$reg_data['timezone']  = 0;
			$reg_data['password2'] = $reg_data['password'];
			$reg_data['gender']	   = ucfirst( $reg_data['gender'] );
			
			$uid = $this->_saveRegistration( $reg_data );
			
			$this->redirect('/users/view/' . $uid);
		}
		else
		{
			// log in	
			$this->Session->write('uid', $user['User']['id']);
			$this->redirect('/');
		}
	}

	private function _base64_url_decode($input) {
	    return base64_decode(strtr($input, '-_', '+/'));
	}

	public function picture()
	{
		$this->_checkPermission();
        $uid = $this->Session->read('uid');
        
        $this->redirect('/users/view/' . $uid);
	}	

	/*
	 * Display user's profile
	 * @mixed $param - userid or username
	 */	
	public function view( $param = null )
	{		
		if ( is_numeric( $param ) ) // userid
		{
			$id   = $param;
			$user = $this->User->findById($id);			
			
			// redirect to SEO url if username exists
			/*if ( !empty( $user['User']['username'] ) && empty( $this->request->named['activity_id'] ) )
			{
				$this->redirect('/-' . $user['User']['username']);
				exit;
			}*/
		}
		else // username
		{
			$user = $this->User->findByUsername($param);
			$id   = $user['User']['id'];
		}
		
		$this->_checkExistence( $user );
		
		if ( !$user['User']['active'] )
		{
			$this->Session->setFlash( __('The user\'s account you were trying to view has been disabled') );
			$this->redirect( '/pages/error' );
			exit;
		}
		
		$uid = $this->Session->read('uid');
		$this->loadModel('Friend');	
		$areFriends = false;
				
		if ( !empty( $uid ) ) //  check if user is a friend
		{
			$areFriends = $this->Friend->areFriends( $uid, $user['User']['id'] );
			
			if ( $uid != $user['User']['id'] )
			{
				$mutual_friends = $this->Friend->getMutualFriends( $uid, $user['User']['id'], 5 );
				$this->set('mutual_friends', $mutual_friends);
			}
		}
		
		$friends = $this->Friend->getUserFriends( $id, null, 10 );		
		
		// check if a friend request exists
		if ( !empty( $uid ) )
		{
			$this->loadModel( 'FriendRequest' );
	
			$request_sent = $this->FriendRequest->existRequest( $uid, $id );			
			$this->set('request_sent', $request_sent);
		} 
        
        // get profile and cover album
        $this->loadModel('Album');
        
        /*if ( !empty( $user['User']['avatar'] ) )
        {
            $profile_album = $this->Album->find('first', array( 'conditions' => array( 'Album.user_id' => $user['User']['id'],
                                                                                       'Album.type'    => 'profile'
                                               ) ) );
                                             
            $this->set('profile_album_id', $profile_album['Album']['id']);               
        }*/
        
        if ( !empty( $user['User']['cover'] ) )
        {
            $cover_album = $this->Album->find('first', array( 'conditions' => array( 'Album.user_id' => $user['User']['id'],
                                                                                     'Album.type'    => 'cover'
                                             ) ) );
                                             
            $this->set('cover_album_id', $cover_album['Album']['id']);               
        }
        
        // check online status
        $online = $this->User->getOnlineUsers();
        if ( in_array( $id, $online['userids'] ) )
            $this->set('is_online', true);
		
		// check privacy
		$canView = $this->_canViewProfile( $user['User'] );
		
		if ( $canView )
		{
			$this->loadModel('Blog');	
			$blogs = $this->Blog->getBlogs( 'user', $id, null, 3 );
			
			$this->loadModel('GroupUser');
			$groups = $this->GroupUser->getGroups('user', $id);
			
			$this->loadModel('Video');	
			$videos = $this->Video->getVideos( 'user', $id, null, 2 );
			
			$this->set('blogs', $blogs);
			$this->set('groups', $groups);
			$this->set('videos', $videos);
		}

		if ( !empty( $this->request->named['activity_id'] ) ) // show the requested activity
		{
			$this->loadModel('Activity');
			$activity = $this->Activity->findById( $this->request->named['activity_id'] );
			
			$this->_checkExistence( $activity );			
			$activities = $this->Activity->getActivities( 'detail', $this->request->named['activity_id'] );
			$activity = $activities[0];
			
			// get activity likes
			if ( !empty( $uid ) )
			{					
				$this->loadModel('Like');				
				$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
				$this->set('activity_likes', $activity_likes);
			}	
			
			$this->set('activity', $activity);
		}
		elseif ( $canView )
			$this->_getProfileDetail( $user );		
		
		$this->set('user', $user);	
		$this->set('friends', $friends);		
		$this->set('areFriends', $areFriends);
		$this->set('canView', $canView);
		$this->set('title_for_layout', $user['User']['name']);
	}

    // check privacy
    private function _canViewProfile( $user )
    {
        $canView = false;
        $uid = $this->Session->read('uid');
        $cuser = $this->_getUser();
        
        if ( $uid == $user['id'] || !empty($cuser['Role']['is_super']) )
            $canView = true;
        else        
        {
            switch ( $user['privacy'] )
            {
                case PRIVACY_EVERYONE:
                    $canView = true;
                    break;
                        
                case PRIVACY_FRIENDS:  
                    $this->loadModel('Friend'); 
                    $areFriends = $this->Friend->areFriends( $uid, $user['id'] );
                                 
                    if ( $areFriends )
                        $canView = true;
                    
                    break;
                    
                case PRIVACY_ME:
                    if ( $uid == $user['id'] )
                        $canView = true;
                        
                    break;
            }           
        }   
        
        return $canView;
    }

	public function ajax_profile($id = null)
	{
		$id = intval($id);	
		$user = $this->User->findById($id);		
        $canView = $this->_canViewProfile( $user['User'] );
        
        if ( $canView )
        {
    		$this->_getProfileDetail( $user );
    		
    		$this->set('user', $user);		
    		$this->render('/Elements/ajax/profile_detail');
        }
        else
        {
            $this->autoRender = false;
            echo __('Access denied');
        }
	}
	
	private function _getProfileDetail( $user )
	{
		$uid = $this->Session->read('uid');
		
		$this->loadModel('ProfileFieldValue');
		$this->loadModel('Activity');
		$this->loadModel('Album');	
		
		$fields = $this->ProfileFieldValue->getValues( $user['User']['id'], true );

		$activities = $this->Activity->getActivities( 'profile', $user['User']['id'], $uid );
		
		// get activity likes
		if ( !empty( $uid ) )
		{					
			$this->loadModel('Like');				
			$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
			$this->set('activity_likes', $activity_likes);
		}
		
		$albums = $this->Album->getAlbums( 'user', $user['User']['id'], null, 4 );
		
		$this->set('fields', $fields);
		$this->set('activities', $activities);
		$this->set('albums', $albums);
		$this->set('admins', array( $user['User']['id'] ) );
	}
	
	/*
	 * Display user's information
	 */	
	public function ajax_info( $uid = null )
	{
		$uid = intval($uid);	
		$user   = $this->User->findById( $uid );
        $canView = $this->_canViewProfile( $user['User'] );
        
        if ( $canView )
        {
    		$this->loadModel('ProfileFieldValue');
            $this->loadModel('Like');
                
    		$fields = $this->ProfileFieldValue->getValues( $uid, false, true );
    		$items  = $this->Like->getAllUserLikes( $uid );
    		
    		$this->set('user', $user);
    		$this->set('fields', $fields);
    		$this->set('items', $items);
    		$this->set('unions', count($items));
        }
        else
        {
            $this->autoRender = false;
            echo __('Access denied');
        }
	}
	
	public function ajax_friends( $uid = null )
	{
		$uid = intval($uid);
		$this->loadModel('Friend');	
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$friends = $this->Friend->getUserFriends( $uid, $page );

		$this->set('users', $friends);
		$this->set('more_url', '/users/ajax_friends/' . $uid . '/page:' . ( $page + 1 ) );
		
		if ( $page > 1 )
			$this->render('/Elements/lists/users_list');
	}
	
	public function ajax_photos( $uid = null )
	{
		$uid = intval($uid);
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;		
		
		$this->loadModel('PhotoTag');		
		$photos = $this->PhotoTag->getPhotos( $uid, $page );	
		
		$this->set('photos', $photos);
		$this->set('more_url', '/users/ajax_photos/' . $uid . '/page:' . ( $page + 1 ) );
		$this->set('tag_uid', $uid);
		
		if ( $page == 1 )
		{
			$this->loadModel('Album');
			$albums = $this->Album->getAlbums( 'user', $uid );
			$this->set('albums', $albums);	
		}
		else
		{
			$this->set('page', $page);	
			$this->render('/Elements/lists/photos_list');
		}
	}
	
	public function ajax_albums( $uid = null )
	{
		$uid = intval($uid);
		$this->loadModel('Album');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$albums = $this->Album->getAlbums( 'user', $uid, $page );		
		
		$this->set('albums', $albums);
		$this->set('more_url', '/users/ajax_albums/' . $uid . '/page:' . ( $page + 1 ) );
		$this->set('user_id', $uid);
		
		if ( $page > 1 )
			$this->render('/Elements/lists/albums_list');		
	}
	
	public function ajax_blogs( $uid = null )
	{
		$uid = intval($uid);
		$this->loadModel('Blog');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$blogs = $this->Blog->getBlogs( 'user', $uid, $page );
		
		$this->set('blogs', $blogs);
		$this->set('more_url', '/users/ajax_blogs/' . $uid . '/page:' . ( $page + 1 ) );
		$this->set('user_id', $uid);
		$this->set('user_blog', true);
		
		if ( $page > 1 )
			$this->render('/Elements/lists/blogs_list');
	}
	
	public function ajax_topics( $uid = null )
	{
		$uid = intval($uid);
		$this->loadModel('Topic');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$topics = $this->Topic->getTopics( 'user', $uid, $page );
		
		$this->set('topics', $topics);
		$this->set('more_url', '/users/ajax_topics/' . $uid . '/page:' . ( $page + 1 ) );
		$this->set('user_id', $uid);
		
		if ( $page > 1 )
			$this->render('/Elements/lists/topics_list');
	}
	
	public function ajax_videos( $uid = null )
	{
		$uid = intval($uid);
		$this->loadModel('Video');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$videos = $this->Video->getVideos( 'user', $uid, $page );
		
		$this->set('videos', $videos);
		$this->set('more_url', '/users/ajax_videos/' . $uid . '/page:' . ( $page + 1 ) );
		$this->set('user_id', $uid);
		
		if ( $page > 1 )
			$this->render('/Elements/lists/videos_list');
	}
	
	public function ajax_avatar()
	{
		
	}
    
    public function ajax_cover()
    {
        $uid = $this->Session->read('uid');            
        $this->loadModel('Photo');
        
        $photo = $this->Photo->find( 'first', array( 'conditions' => array(  'Album.type' => 'cover', 
                                                                             'Album.user_id' => $uid ),
                                                     'limit' => 1,
                                                     'order' => 'Photo.id desc'
                                   ) );
                                   
        $this->set('photo', $photo);
    }	
	
	public function profile()
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		$this->_editProfile( $uid );
		
		$this->set('title_for_layout', __('Edit Profile'));
	}
	
	private function _editProfile( $uid = null )
	{		
		$this->loadModel('ProfileFieldValue');
		$this->loadModel('ProfileField');
		
		$values = array();
		if ( empty( $uid ) )
			$uid = $this->request->data['id'];
			
		if ( empty( $uid ) )
		{
			$this->Session->setFlash('Invalid user id', 'default', array('class' => 'error-message'));
			$this->redirect( $this->referer() );
			exit;
		}

		// get all the profile field values
		$vals = $this->ProfileFieldValue->getValues( $uid );
		
		// format the profile field values array
		foreach ($vals as $val)
		{
			$values[$val['ProfileFieldValue']['profile_field_id']] = array( 'id' 	=> $val['ProfileFieldValue']['id'],
																			'value' => $val['ProfileFieldValue']['value'] );
		}		
		
		if (!empty($this->request->data))
		{
			// Current User
			$cid = $this->Session->read('uid');
			$cuser = $this->User->findById( $cid );
			
			// Unset Featured and Role_id if not Admin or Super Admin
			if(!$cuser['Role']['is_super'] && !$cuser['Role']['is_admin'])
			{
				unset($this->request->data['role_id']);
				unset($this->request->data['featured']);
			}
			
			// Check Avaible Roles
			if(isset($this->request->data['role_id']))
			{
				if ( !$cuser['Role']['is_super']){
					$this->loadModel('Role');
					$denySuperRoles = $this->Role->find('list', array('conditions' => array('is_super' => 1) ,'field' => array('name')));
					if(isset($denySuperRoles[$this->request->data['role_id']])) unset($this->request->data['role_id']);
				}
			}
			
			
			// get all the custom fields EXCLUDING headings
            $custom_fields = $this->ProfileField->find( 'all', array( 'conditions' => array( 'active' => 1, 'type <> ?' => 'heading' ) ) );
                				
			$this->User->id = $uid;
			$errors = array();
			
			// check username
			if ( !empty( $this->request->data['username'] ) )
			{
			    if ( is_numeric( $this->request->data['username'] ) ) 
                {
				    $this->Session->setFlash( __('Username must not be a numeric value'), 'default', array('class' => 'error-message') );
    				$this->redirect( $this->referer() );
    				exit;
                }
                
                // check restricted usernames
                $jsnsocial_setting = $this->_getSettings();           
                    
                if ( !empty( $jsnsocial_setting['restricted_usernames'] ) )
                {
                    $usernames = explode( "\n", $jsnsocial_setting['restricted_usernames'] );
    
                    foreach ( $usernames as $un )
                    {
                        if ( !empty( $un ) && ( trim($un) == $this->request->data['username'] ) )
                        {
                            $this->Session->setFlash( __('Username is restricted'), 'default', array('class' => 'error-message') );
                            $this->redirect( $this->referer() );
                            exit;
                        }
                    }
                }
			}
			
			if ( !$this->User->save( $this->request->data ) ) // save basic info				
				$errors = $this->User->invalidFields();
			
			/* Save custom fields */
			
			foreach ($custom_fields as $field)
			{
				$value = $this->request->data['field_' . $field['ProfileField']['id']];
				
				if ( $field['ProfileField']['required'] && empty( $value ) && !is_numeric( $value ) ) // check if field is required
					$errors[0][0] = $field['ProfileField']['name'] . __(' is required');                
				else
				{
					$value = ( is_array( $value ) ) ? implode( ', ', $value ) : $value;
					
					if ( !isset( $values[$field['ProfileField']['id']] ) ) // save new value
					{							
						$this->ProfileFieldValue->create();
						$this->ProfileFieldValue->save( array( 'user_id' 		  => $uid,
															   'profile_field_id' => $field['ProfileField']['id'],
															   'value' 			  => $value
													) 	);
					}						
					else if ( $value != $values[$field['ProfileField']['id']]['value'] ) // update current value
					{
						$this->ProfileFieldValue->id = $values[$field['ProfileField']['id']]['id'];
						$this->ProfileFieldValue->save( array( 'value' => $value ) );
					}
				}
			}
			
			if ( !empty( $errors ) )
				$this->Session->setFlash( current( current( $errors ) ), 'default', array('class' => 'error-message') );
			else
				$this->Session->setFlash( __('Your changes have been saved') );
				
			$this->redirect( $this->referer() );
		}
		else
		{
			// get all the custom fields INCLUDING headings
            $custom_fields = $this->ProfileField->find( 'all', array( 'conditions' => array( 'active' => 1 ) ) );
                
			$this->set('custom_fields', $custom_fields);
			$this->set('values', $values);
		}
	}

    public function password()
    {
		exit();
        $this->_checkPermission();
        $uid = $this->Session->read('uid');
        
        if (!empty($this->request->data))
        {
            $this->User->id = $uid;
            $errors = array();
            $user = $this->User->read();
                
            if ( md5( $this->request->data['old_password'] . Configure::read('Security.salt') ) != $user['User']['password'] )
            {
                $this->Session->setFlash( __('Incorrect current password'), 'default', array('class' => 'error-message') );
                $this->redirect( $this->referer() );
                exit;
            }
            
            if ( !$this->User->save( $this->request->data ) )             
                $errors = $this->User->invalidFields();
            
            if ( !empty( $errors ) )
                $this->Session->setFlash( current( current( $errors ) ), 'default', array('class' => 'error-message') );
            else
                $this->Session->setFlash( __('Your password has been changed') );
                
            $this->redirect( $this->referer() );
        }
        
        $this->set('title_for_layout', __('Change Password'));
    }

	public function recover($state = null)
	{
		if (!empty($this->request->data))
		{
			if ( empty( $this->request->data['email'] ) )
			{
				$this->Session->setFlash( __('Please enter an email address'), 'default', array('class' => 'error-message') );
				$this->redirect( '/users/recover' );
				exit;
			}
			
			$user = $this->User->findByEmail($this->request->data['email']);
			
			if (!empty($user))
			{			
				$this->loadModel('PasswordRequest');	
				$code = md5( Configure::read('Security.salt') . time() );

				if ( $this->PasswordRequest->save( array('user_id' => $user['User']['id'], 'code' => $code) ) )
				{
					$this->_sendEmail( $this->request->data['email'], __('Password Change Request'), 'password_request', array('code' => $code) );
					$this->redirect( '/users/recover/sent' );
				}
			}
			else
			{
				$this->Session->setFlash( __('Email does not exist'), 'default', array('class' => 'error-message') );
				$this->redirect( '/users/recover' );
			}
		}

		$this->set('state', $state);
	}
	
	public function resetpass( $code = null )
	{
		$this->loadModel('PasswordRequest');
			
		if ( !empty( $this->request->data ) )
		{		
			$request = $this->PasswordRequest->findByCode( $this->request->data['code'] );
			$this->_checkExistence( $request );
		
			$this->User->id = $request['PasswordRequest']['user_id'];
			$user = $this->User->read();
			
			$this->User->set( $this->request->data );		
			
			if ( !$this->User->validates() )
		    {
				$errors = $this->User->invalidFields();
				
		    	$this->Session->setFlash( current( current( $errors ) ), 'default', array('class' => 'error-message') );
				$this->redirect( $this->referer() );
		    }
			
			$this->User->save( array( 'password' => $this->request->data['password'] ) );
			$this->PasswordRequest->delete( $request['PasswordRequest']['id'] );
			
			$this->Session->setFlash( __('Your password has been reset') );
			$this->redirect( '/' );
		}
		else
		{
			$request = $this->PasswordRequest->findByCode( $code );
			$this->_checkExistence( $request );		
			$this->set('code', $code);
		}
	}
	
	public function do_confirm( $code = null )
	{
		$this->autoRender = false;
		$user = $this->User->findByCode( $code );
		
		if ( !empty(  $user ) )
		{
			$this->User->id = $user['User']['id'];
			$this->User->save( array( 'confirmed' => 1 ) );
			$this->Session->setFlash( __('Your account has been validated!') );
		}
		else
			$this->Session->setFlash( __('Invalid code!'), 'default', array('class' => 'error-message') );
			
		$this->redirect( '/' );
	}
	
	/*
	 * Check if a username exists or not 
	 */
	public function ajax_username()
	{
		$this->autoRender = false;		
		$username = $this->request->data['username'];
		$res = array( 'result' => 0 );
		$jsnsocial_setting = $this->_getSettings();	
		
		if ( strlen( $username ) < 5 || strlen( $username ) > 50 )
			$res['message'] = __('Username must be between 5 and 50 characters long');
		elseif ( is_numeric( $username ) )
			$res['message'] = __('Username must not be a numeric value');
		elseif ( !ctype_alnum( $username ) )
			$res['message'] = __('Username must only contain alphanumeric characters (no special chars)');
		else		
		{
			// check restricted usernames
			$jsnsocial_setting = $this->_getSettings();			
				
			if ( !empty( $jsnsocial_setting['restricted_usernames'] ) )
			{
				$usernames = explode( "\n", $jsnsocial_setting['restricted_usernames'] );

				foreach ( $usernames as $un )
				{
					if ( !empty( $un ) && ( trim($un) == $username ) )
					{
						$res['message'] = __('Username is restricted');
						echo json_encode($res);						
						return;
					}
				}
			}
				
			// check available username
			$count = $this->User->find( 'count', array( 'conditions' => array( 'User.username' => $username ) ) );
		
			if ( $count )
				$res['message'] = __('Username is already taken');
			else
			{
				$res['result'] = 1;
				$res['message'] = __('Username is available');
			}
		}
		
		echo json_encode($res);
	}
	
	/*
	 * Deactivate user account
	 */
	public function deactivate()
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
        $cuser = $this->_getUser();
		
		if ( $cuser['Role']['is_super'] )
		{
			$this->Session->setFlash( __('Root admin account cannot be deactivated') , 'default', array('class' => 'error-message'));
			$this->redirect( $this->referer() );
		}
		else 
		{
			$this->User->id = $uid;
			$this->User->save( array( 'active' => 0 ) );
			
			$this->Session->setFlash( __('Your account has been successfully deactivated') );
			$this->do_logout();
		}
	}
	
	/*
	 * Request Deletetion
	 */
	public function request_deletion()
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		$cuser = $this->_getUser();
		
		$this->loadModel('AdminNotification');					
		$this->AdminNotification->save( array( 'user_id' => $uid,
											   'text' => __('requested to delete account'),
											   'url' => $this->request->base . '/admin/users/index/keyword:' . $cuser['email'].'/'
									) );
		
		$this->Session->setFlash( __('Your account deletion request has been submitted') );
		$this->redirect( $this->referer() );
	}
	
	/*
	 * Feature user
	 */
	public function admin_feature( $id = null )
	{
		if ( !empty( $id ) )
		{		
			$this->User->id = $id;		
			$this->User->save( array( 'featured' => 1 ) );
			
			$this->Session->setFlash( __('This user has been successfully featured') );
		}
					
		$this->redirect( $this->referer() );
	}
	
	/*
	 * Unfeature user
	 */
	public function admin_unfeature( $id = null )
	{
		if ( !empty( $id ) )
		{		
			$this->User->id = $id;		
			$this->User->save( array( 'featured' => 0 ) );
			
			$this->Session->setFlash( __('This user has been successfully unfeatured') );
		}
					
		$this->redirect( $this->referer() );
	}
	
	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/users/index/keyword:' . $this->request->data['keyword'].'/' );
			
		$cond = array();
		/*if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(User.name, User.email) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];*/
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['(User.name LIKE ? OR Profile.email LIKE ?)'] = array('%'.$this->request->named['keyword'].'%','%'.$this->request->named['keyword'].'%');
			
		$users = $this->paginate( 'User', $cond );	
		
		$this->set('users', $users);
		$this->set('title_for_layout', 'Users Manager');
	}
	
	public function admin_edit( $id = null )
	{
		if ( empty($this->request->data) )
		{
			if ( empty( $id ) )
			{
				$this->Session->setFlash('Invalid user id', 'default', array('class' => 'error-message'));
				$this->redirect( $this->referer() );
				exit;
			}
				
            $uid = $this->Session->read('uid');
			$user = $this->User->findById( $id );	
			$cuser = $this->User->findById( $uid );		
			$this->set('user', $user);            
            
            if ( !$cuser['Role']['is_super'] && $user['Role']['is_super'])
            {
				echo('You cannot edit other super admins');
                exit;
            }
            
            $this->loadModel('Role');
			if ( !$cuser['Role']['is_super']) $roles = $this->Role->find('list', array('conditions' => array('is_super' => 0) ,'field' => array('name')));
            else $roles = $this->Role->find('list', array('field' => array('name')));
            
            foreach ($roles as $key => $r)
                if ( $key == ROLE_GUEST )
                    unset($roles[$key]);
            
            $this->set('roles', $roles);
		}
		
		$this->_editProfile( $id );
	}

    public function admin_ajax_password( $id = null )
    {
        $this->set('id', $id);        
    }
    
    public function admin_do_password()
    {
        if (!empty($this->request->data))
        {
            $user = $this->User->findById( $this->request->data['id'] );
                
            $this->User->id = $this->request->data['id'];
            $this->User->set( $this->request->data );
            
            $this->_validateData($this->User);            
            $this->User->save();
            
            if ( !empty( $this->request->data['notify'] ) )
                $this->_sendEmail( $user['User']['email'], __('Your password has been changed'), null, null, null, null, __('The admin has changed your password to') . ': ' . $this->request->data['password'] );
            
            //$this->Session->setFlash( __('Password has been changed') );
            
            $response['result'] = 1;
            echo json_encode($response);
        }      
    }
	
	public function admin_avatar( $id = null )
	{
		if ( empty( $id ) )
			$this->Session->setFlash('Invalid user id', 'default', array('class' => 'error-message'));
		else
		{
			$this->User->id = $id;
			$user = $this->User->findById( $id );
			
			$this->User->removeAvatarFiles( $user['User'] );			
			$this->User->save( array('photo' => '', 'avatar' => '') );	
			
			$this->Session->setFlash('User\'s avatar has been removed');
		}	
			
		$this->redirect( $this->referer() );
	}
    
    public function admin_resend( $id = null )
    {
        $user = $this->User->findById( $id );
        $jsnsocial_setting = $this->_getSettings();
            
        $this->_sendEmail( $user['User']['email'], 
                           __('Welcome to') . ' ' . $jsnsocial_setting['site_name'], 
                           'welcome_user', 
                           array( 'name'      => $user['User']['name'],
                                  'code'      => $user['User']['code'],
                                  'confirmed' => $user['User']['confirmed']
                        )   );
                        
        $this->Session->setFlash('Validation email has been resent');   
        $this->redirect( $this->referer() );
    }
    
    public function admin_delete_content($id)
    {
        $this->_checkPermission(array('super_admin' => 1));
        $user = $this->User->findById($id);
        
        if ( !$user['Role']['is_super'] )
        {
            $this->_delete_user_contents($user);            
            $this->Session->setFlash( 'All user\'s content has been deleted' );
        }
        
        $this->redirect( $this->referer() );
    }
	
    public function delete()
    {
    	if(SocialNetwork::$deleteUser)
    	{
    		$users = $this->User->find( 'all', array( 'conditions' => array( 'User.id' => SocialNetwork::$deleteUser) ) );
    		foreach ( $users as $user )
			{
    			$this->_delete_user_contents($user);
				$this->User->delete( $user['User']['id'] );
			}
    	}
    }
	
	public function admin_delete()
	{
		SocialNetwork::$deleteUser = -1;
		
		$this->_checkPermission(array('super_admin' => 1));
        
		if ( !empty( $_POST['users'] ) )
		{
			$users = $this->User->find( 'all', array( 'conditions' => array( 'User.id' => $_POST['users'] ) ) );
			
			// Joomla Model
			require_once(JPATH_SITE . '/administrator/components/com_users/models/user.php');
			$dispatcher = JEventDispatcher::getInstance();
			$model=new UsersModelUser();
			$table = $model->getTable();
			
			foreach ( $users as $user )
			{
				$this->_delete_user_contents($user);
				$this->User->delete( $user['User']['id'] );
				
				// Joomla Delete
				if ($table->load($user['User']['id']))
				{	
						// Get users data for the users to delete.
						$user_to_delete = JFactory::getUser($user['User']['id']);
						// Fire the onUserBeforeDelete event.
						$dispatcher->trigger('onUserBeforeDelete', array($table->getProperties()));
						if (!$table->delete($user['User']['id']))
						{
							$model->setError($table->getError());
						}
						else
						{
							// Trigger the onUserAfterDelete event.
							$dispatcher->trigger('onUserAfterDelete', array($user_to_delete->getProperties(), true, $model->getError()));
						}
				}
				
				$this->Session->setFlash( 'Users deleted' );
			}	
		}
		
		$this->redirect( $this->referer() );
	}
    
    private function _delete_user_contents($user)
    {
        $this->loadModel('Activity');
        $this->loadModel('ActivityComment');
        $this->loadModel('Album');
        $this->loadModel('Blog');
        $this->loadModel('Comment');
        $this->loadModel('Conversation');
        $this->loadModel('ConversationUser');
        $this->loadModel('Event');
        $this->loadModel('EventRsvp');
        $this->loadModel('Friend');
        $this->loadModel('FriendRequest');
        $this->loadModel('Group');
        $this->loadModel('GroupUser');
        $this->loadModel('Like');
        $this->loadModel('Notification');
        $this->loadModel('Photo');
        $this->loadModel('PhotoTag');
        $this->loadModel('ProfileFieldValue');
        $this->loadModel('Report');
        $this->loadModel('Topic');
        $this->loadModel('Video'); 

        $this->User->removeAvatarFiles( $user['User'] );
        $this->User->removeCoverFile( $user['User'] );
        
        $this->Activity->deleteAll( array( 'Activity.user_id' => $user['User']['id'] ), true, true );
        $this->Activity->deleteAll( array( 'Activity.target_id' => $user['User']['id'], 'Activity.type' => APP_USER ), true, true );
        $this->ActivityComment->deleteAll( array( 'ActivityComment.user_id' => $user['User']['id'] ), true, true );
        
        $albums = $this->Album->findAllByUserId( $user['User']['id'] );
        foreach ( $albums as $album )
            $this->Album->deleteAlbum( $album['Album']['id'] );
            
        $blogs = $this->Blog->findAllByUserId( $user['User']['id'] );
        foreach ( $blogs as $blog )
            $this->Blog->deleteBlog( $blog['Blog']['id'] );
        
        $this->Comment->deleteAll( array( 'Comment.user_id' => $user['User']['id'] ), true, true );
        $this->Conversation->deleteAll( array( 'Conversation.user_id' => $user['User']['id'] ), true, true );
        $this->ConversationUser->deleteAll( array( 'ConversationUser.user_id' => $user['User']['id'] ), true, true );
        
        $events = $this->Event->findAllByUserId( $user['User']['id'] );
        foreach ( $events as $event )
            $this->Event->deleteEvent( $event );
        
        $this->EventRsvp->deleteAll( array( 'EventRsvp.user_id' => $user['User']['id'] ), true, true );
        $this->Friend->deleteAll( array( 'Friend.user_id' => $user['User']['id'] ), true, true );
        $this->Friend->deleteAll( array( 'Friend.friend_id' => $user['User']['id'] ), true, true );                 
        $this->FriendRequest->deleteAll( array( 'FriendRequest.user_id' => $user['User']['id'] ), true, true );
        $this->FriendRequest->deleteAll( array( 'FriendRequest.sender_id' => $user['User']['id'] ), true, true );
        
        $groups = $this->Group->findAllByUserId( $user['User']['id'] );
        foreach ( $groups as $group )
            $this->Group->deleteGroup( $group );
        
        $this->GroupUser->deleteAll( array( 'GroupUser.user_id' => $user['User']['id'] ), true, true );
        $this->Like->deleteAll( array( 'Like.user_id' => $user['User']['id'] ), true, true );
        $this->Notification->deleteAll( array( 'Notification.user_id' => $user['User']['id'] ), true, true );
        $this->Notification->deleteAll( array( 'Notification.sender_id' => $user['User']['id'] ), true, true );
        
        $photos = $this->Photo->findAllByUserId( $user['User']['id'] );
        foreach ( $photos as $photo )
            $this->Photo->deletePhoto( $photo );
        
        $this->PhotoTag->deleteAll( array( 'PhotoTag.user_id' => $user['User']['id'] ), true, true );
        $this->PhotoTag->deleteAll( array( 'PhotoTag.tagger_id' => $user['User']['id'] ), true, true );
        $this->ProfileFieldValue->deleteAll( array( 'ProfileFieldValue.user_id' => $user['User']['id'] ), true, true );
        $this->Report->deleteAll( array( 'Report.user_id' => $user['User']['id'] ), true, true );
        
        $topics = $this->Topic->findAllByUserId( $user['User']['id'] );
        foreach ( $topics as $topic )
            $this->Topic->deleteTopic( $topic['Topic']['id'] );
            
        $videos = $this->Video->findAllByUserId( $user['User']['id'] );
        foreach ( $videos as $video )
            $this->Video->deleteVideo( $video );
        
    }
}
 
