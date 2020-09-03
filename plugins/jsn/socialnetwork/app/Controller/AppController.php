<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
JsnApp::uses('Controller', 'Controller');

class AppController extends Controller 
{	
	public $components = array('Cookie', 'Session');
	public $helpers = array('Html', 'Text', 'Form', 'Session', 'Time', 'Jsnsocial');

	/*
	* Initialize the system
	*/
	public function beforeFilter()
	{		
		// check for config file
		if ( !file_exists( APP . 'Config/config.php' ) )
		{
			$this->redirect( '/install' );
			exit;
		}
		
		$this->Cookie->name = 'jsnsocial';
		$this->Cookie->key  = Configure::read('Security.salt');
		$this->Cookie->time = 60 * 60 * 24 * 30;
		
		// get the global settings	
		$jsnsocial_setting = $this->_getSettings();
		global $SOCIALNETWORK_DEFAULT_PRIVACY; $SOCIALNETWORK_DEFAULT_PRIVACY=$jsnsocial_setting['profile_privacy'];
		
		$this->set('jsnsocial_setting', $jsnsocial_setting);
		
		$this->checkUpdate($jsnsocial_setting);
		
		// load bootstrap javascript library
		JHtml::_('bootstrap.framework');
		
		// get langs
		$this->loadModel('Language');
        $site_langs = $this->Language->getLanguages();
        
        // select lang
		$language=JFactory::getLanguage()->getTag();
		$language=str_replace('-','_',$language);
		Configure::write('Config.language', $language);
        
        // set locale
        $locales=JFactory::getLanguage()->getLocale();
		setlocale(LC_TIME, $locales);
		
		// ban ip addresses
		if ( !empty( $jsnsocial_setting['ban_ips'] ) )
		{
			$ips = explode( "\n", $jsnsocial_setting['ban_ips'] );
			foreach ( $ips as $ip )
			{
				if ( !empty( $ip ) && strpos( $_SERVER['REMOTE_ADDR'], trim($ip) ) === 0 )
				{
					$this->autoRender = false;
					echo __('You are not allowed to view this site');
					exit;
				}
			}
		}   
        
        // themes
        $this->loadModel('Theme');
        $site_themes = $this->Theme->getThemes();
        
        // select theme
        /*if ( $this->request->is('mobile') && !$this->Session->check('fullsite') )
            $this->theme = 'mobile';
        else*/if ( $this->Cookie->check('theme') && array_key_exists( $this->Cookie->read('theme'), $site_themes ) )
            $this->theme = $this->Cookie->read('theme');
        //if ( $this->Cookie->check('theme') && array_key_exists( $this->Cookie->read('theme'), $site_themes ) )
          //  $this->theme = $this->Cookie->read('theme');
        
        if ( empty( $this->theme ) )
            $this->theme = $jsnsocial_setting['default_theme'];
        
		// get the current logged in user
		$this->Session->write('uid',JFactory::getUser()->id);	
		$uid = $this->Session->read('uid');	
			
		// auto login
		if ( empty( $uid ) && $this->Cookie->read('email') && $this->Cookie->read('password') )
			$uid = $this->_logMeIn( $this->Cookie->read('email'), $this->Cookie->read('password') );
		
		// get current user
		if ( empty( $cuser ) )
		{
			$cuser = $this->_getUser();
			$this->set('cuser', $cuser);
		}
		
		// set lang to user's chosen lang
		/*if ( !empty( $cuser['lang'] ) )
		{
			$language=str_replace('-','_',$cuser['lang']);
			Configure::write('Config.language', $language);
		}*/
			
		
		// site is offline?
		if ( !empty( $jsnsocial_setting['site_offline'] ) && empty($cuser['Role']['is_super']) )
		{
			$this->layout = '';
			$this->set('offline_message', $jsnsocial_setting['offline_message']);
			$this->render('/Elements/misc/offline');
			return;
		}
		
		// force login
		if ( empty( $uid ) && ( $this->request->here != $this->request->webroot ) && $jsnsocial_setting['force_login'] && !in_array( $this->request->controller, array( 'pages', 'home' ) ) &&
			 !in_array( $this->request->action, array( 'login', 'do_logout', 'register', 'ajax_signup_step1', 'ajax_signup_step2', 'fb_register', 'do_fb_register', 'recover', 'resetpass', 'do_confirm' ) ) )
		{
			$this->Session->setFlash( __('Please login to view the site'), 'default', array( 'class' => 'error-message') );
			$this->redirect( '/' );
			exit;
		}
		
        if ( empty( $uid ) && $jsnsocial_setting['force_login'] )
            $this->set('no_right_column', true);
        
		// detect ajax request
		if ( $this->request->is('ajax') )
			$this->layout = '';
			
		if ( strpos( $this->request->action, 'do_' ) !== false )
			$this->autoRender = false;

		if ( isset( $this->request->params['admin'] ) ) // admin area
		{
			$this->_checkPermission( array( 'admin' => true ) );
			
			/*if ( $this->request->action != 'admin_login' && !$this->Session->read('admin_login') )
			{
				$this->redirect( '/admin/home/login' );
				exit;
			}
			
			if ( $this->Session->read('admin_login') )
				$this->Session->write('admin_login', 1); */
		}
        
        $role_id = $this->_getUserRoleId();
		
		// hooks
		if ( $this->layout != '' && $this->autoRender != false && !$this->request->is('post') ) // only reach here if the request is not an ajax/post request or within admin area
		{ 		            
            // load the hooks
            $this->loadModel('Hook');
            $hook_pos = $this->Hook->loadAll( $this->request->controller, $this->request->action, $role_id );
            
            foreach ($hook_pos as $hooks)
                foreach ( $hooks as $hook )
                {                       
                    $component = Inflector::camelize($hook['Hook']['key']);
                    $component_file = APP . 'Controller' . DS . 'Component' . DS . $component . 'Component.php';
                    
                    if ( file_exists( $component_file ) )
                    {
                        $this->$component = $this->Components->load($component);
                        $this->$component->run( $this, unserialize( $hook['Hook']['settings'] ) );
                    }
                } 
            
            $this->set('site_hooks', $hook_pos);
		}

		// plugins
		if ( $this->layout != '' && $this->autoRender != false && !$this->request->is('post') ) // only reach here if the request is not an ajax/post request or within admin area
		{ 		            
            // load the plugins
            $this->loadModel('Plugin');
            $plugins = $this->Plugin->loadAll( $role_id );

            $this->set('site_plugins', $plugins);
            
            // get plugin info
            $plugin = array();
            
            foreach ($plugins as $p)
                $plugin[$p['Plugin']['key']] = $p['Plugin'];
                
            $this->set('plugin', $plugin);
            
            // load menu pages
            $this->loadModel('Page');
            $pages = $this->Page->loadMenuPages( $role_id );

            $this->set('menu_pages', $pages);
		}
		
		// site timezone
		$utz = ( !is_numeric($jsnsocial_setting['timezone']) ) ? $jsnsocial_setting['timezone'] : 'UTC';
        
        // user timezone
        if ( !empty( $cuser['timezone'] ) )
            $utz = $cuser['timezone'];
		
		// return url
		if ( !empty( $this->request->named['return_url'] ) )
			$this->set('return_url', $this->request->named['return_url']);
		
		$this->set('uid', $uid);
		$this->set('uacos', $this->_getUserRoleParams());	
        $this->set('utz', $utz);	
        $this->set('site_themes', $site_themes);
        $this->set('site_langs', $site_langs);
		
		if ( !$this->request->is('ajax') ) // only run cron when it's not an ajax request
            $this->_runCron();
	}

	/**
	 * Get the current logged in user
	 * @return array
	 */
	protected function _getUser()
	{
		static $loggedUserCheck=false;
		
		$uid = $this->Session->read('uid');
		$cuser = array();
		
		if ( !empty( $uid ) ) // logged in users
		{
			$this->loadModel('User');
			$this->User->cacheQueries = $loggedUserCheck;
			
			$loggedUserCheck=true;
			
			$user = $this->User->findById( $uid );
			
			if ( !$user['User']['active'] )
			{
				$this->Session->delete('uid');			
				$this->Session->setFlash( __('This account has been disabled'), 'default', array( 'class' => 'error-message') );
				
				return;
			}
            
            $cuser = $user['User'];
            $cuser['Role'] = $user['Role']; 

            $juser = JFactory::getUser();
            if($juser->authorise('core.admin')){
			    $cuser['Role']['is_admin'] = 1;
			    $cuser['Role']['is_super'] = 1;
			}           
		}

		return $cuser;			
	}
    
    /**
     * Get the current logged in user's role id
     * @return int
     */
    protected function _getUserRoleId()
    {
        $cuser = $this->_getUser();        
        $role_id = (empty($cuser)) ? ROLE_GUEST : $cuser['role_id'];

        return $role_id;          
    }
    
    /**
     * Get the current logged in user's role params
     * @return array
     */
    protected function _getUserRoleParams()
    {
        $cuser = $this->_getUser();
        
        if ( !empty($cuser) )
            $params = explode(',', $cuser['Role']['params'] );
        else
        {
            $params = Cache::read('guest_role');
            
            if ( empty($params) )
            {
                $this->loadModel('Role');
                $guest_role = $this->Role->findById(ROLE_GUEST);
                
                $params = explode(',', $guest_role['Role']['params']);
                Cache::write('guest_role', $params);
            }
        }

        return $params;          
    }
	
	/**
	 * Get global site settings
	 * @return array
	 */
	public function _getSettings()
	{
		$this->loadModel('Setting');
		$this->Setting->cacheQueries = true;
		
		$settings = $this->Setting->find('list', array( 'fields' => array('field', 'value') ) );
		
		$settings['site_name']=JFactory::getApplication()->getCfg('sitename');
		$settings['disable_registration']=!JComponentHelper::getParams('com_users')->get('allowUserRegistration',1);
        $settings['timezone']=JFactory::getApplication()->getCfg('offset');
		$settings['site_email']=JFactory::getApplication()->getCfg('mailfrom');
		$setting['default_language']=JFactory::getLanguage()->getDefault();
		
		return $settings;			
	}
	
	/**
	 * Check if user has permission to view page
	 * @param array $options - array( 'roles' => array of role id to check
	 * 								  'confirm' => boolean to check email confirmation
	 * 								  'admins' => array of user id to check ownership
	 * 								  'admin' => boolean to check if logged in user is admin
	 * 								  'super_admin' => boolean to check if logged in user is super admin
     *                                'aco' => string of aco to check against user's role
	 * 								 )
	 */
	protected function _checkPermission( $options = array() )
	{
		$cuser 		= $this->_getUser();
		$authorized = true;
		$hash 		= '';
		$return_url = '/return_url:' . base64_encode( $this->request->here );
		
        // check aco
        if ( !empty( $options['aco'] ) )
        {
            $acos = $this->_getUserRoleParams();                
            
            if ( !in_array( $options['aco'], $acos ) )
            {
                $authorized = false;
                $msg        = __('Access denied');
            }
        }
        else
        {
    		// check login
    		if ( !$cuser )
    		{
    			$authorized = false;
    			$msg 		= __('Please login or register');			
    		}
    		else
    		{
    			// check role
    			if ( !empty( $options['roles'] ) && !in_array( $cuser['role_id'], $options['roles'] ) )
    			{
    				$authorized = false;
    				$msg 		= __('Access denied');
    			}
                
                // check admin
                if ( !empty( $options['admin'] ) && !$cuser['Role']['is_admin'] )
                {
                    $authorized = false;
                    $msg        = __('Access denied');
                }
    
                // check super admin
                if ( !empty( $options['super_admin'] ) && !$cuser['Role']['is_super'] )
                {
                    $authorized = false;
                    $msg        = __('Access denied');
                }
    			
    			// check confirmation
    			/*if ( !empty( $options['confirm'] ) && !$cuser['confirmed'] )
    			{
    				$authorized = false;
    				$msg 		= __('You have not confirmed your email address! Check your email (including junk folder) and click on the validation link to validate your email address');
    			}*/
    			
    			// check owner
    			if ( !empty( $options['admins'] ) && !in_array( $cuser['id'], $options['admins'] ) && !$cuser['Role']['is_admin'] )
    			{					
    				$authorized = false;
    				$msg 		= __('Access denied');
    			}
    		}
    	}
		
		if ( !$authorized )
		{
			if ( empty( $this->layout ) )	
			{
				$this->autoRender = false;
				echo $msg;
			}
			else
			{
				if ( !empty( $msg ) )
					$this->Session->setFlash($msg, 'default', array( 'class' => 'error-message' ) );
				
				$this->redirect( '/pages/no-permission' . $return_url );
			}
			exit;
		}
	}
	
	/**
	 * Check if an item exists
	 * @param mixed $item - array or object to check
	 */
	protected function _checkExistence( $item = null )
	{
		if ( empty( $item ) )
            $this->_showError( __('Item does not exist') );
	}
    
    protected function _showError( $msg )
    {
        $this->Session->setFlash( $msg, 'default', array( 'class' => 'error-message' ) );
        $this->redirect( '/pages/error' );
        exit;
    }
    
    protected function _jsonError( $msg )
    {
        $this->autoRender = false;
        
        $response['result'] = 0;
        $response['message'] = $msg;
        
        echo json_encode($msg);
        exit;
    }
	
	/**
	 * Validate submitted data
	 * @param object $model - Cake model
	 */
	protected function _validateData( $model = null )
	{
		if ( !$model->validates() )
	    {
	    	$errors = $model->invalidFields();	
			
			$response['result'] = 0;    	
	    	$response['message'] = current( current( $errors ) );
			
			echo json_encode($response);
			exit;
	    }
	}
	
	/**
	 * Check if current user is allowed to view item
	 * @param string $privacy - privacy setting
	 * @param int $owner - user if of the item owner
	 * @param boolean $areFriends - current user and owner are friends or not
	 */
	protected function _checkPrivacy( $privacy, $owner, $areFriends = null )
	{	
		$uid = $this->Session->read('uid');	
		if ( $uid == $owner ) // owner
			return;
		
		switch ( $privacy )
		{
			case PRIVACY_FRIENDS:				
				if ( empty( $areFriends ) )
				{
					$areFriends = false;
					
					if ( !empty($uid) ) //  check if user is a friend
					{
						$this->loadModel( 'Friend' );
						$areFriends = $this->Friend->areFriends( $uid, $owner );
					}
				}
				
				if ( !$areFriends )
				{
					$this->Session->setFlash( __('Only friends of the poster can view this item'), 'default', array( 'class' => 'error-message') );
					$this->redirect( '/pages/no-permission' );
				}
				
				break;
				
			case PRIVACY_ME:
				$this->Session->setFlash( __('Only the poster can view this item'), 'default', array( 'class' => 'error-message') );
				$this->redirect( '/pages/no-permission' );
				break;
		}
	}
	
	/**
	 * Log the user in
	 * @param string $email - user's email
	 * @param string $password - user's password
	 * @param boolean $remember - remember user or not
	 * @return uid if successful, false otherwise 
	 */
	protected function _logMeIn( $email, $password, $remember = false )
	{
		if ( !is_string( $email ) || !is_string( $password ) )
			return false;
		
		$this->loadModel('User');
	
		// find the user
		$user = $this->User->find( 'first', array( 'conditions' => array( 'email' => trim( $email ) ) ) );

		if (!empty($user)) // found
		{
			if ( $user['User']['password'] != md5( trim( $password ) . Configure::read('Security.salt') ) ) // wrong password
			    return false;
                							
			if ( !$user['User']['active'] )
			{	
				$this->Session->setFlash( __('This account has been disabled'), 'default', array( 'class' => 'error-message') );				
				return false;
			}				
			else
			{		
				// save user id and user data in session
				$this->Session->write('uid', $user['User']['id']);
	
				// handle cookies
				if ( $remember )
				{
					$this->Cookie->write('email', $email, true, 60 * 60 * 24 * 30);
					$this->Cookie->write('password', $password, true, 60 * 60 * 24 * 30);
				}
	
				// update last login
				$this->User->id = $user['User']['id'];
				$this->User->save( array( 'last_login' => date("Y-m-d H:i:s") ) );
				
				return $user['User']['id'];
			}
		}
		else
			return false;
	}
	
	private function _runCron()
	{
		$setting   = $this->_getSettings();
		$yesterday = time() - 60*60*24;
		
		if ( $setting['cron_last_run'] < $yesterday )
		{
			// update last run
			$this->loadModel('Setting');
			$this->Setting->updateAll( array( 'value' => '"' . time() . '"' ), array( 'field' => 'cron_last_run' ) );
		
			// send notifications summary emails
			$emails = array();
			$this->loadModel('Notification');
			
			$notifications = $this->Notification->getRecentNotifications();
			
			foreach ( $notifications as $noti )
				$emails[$noti['User']['email']][] = $noti;

			foreach ( $emails as $email => $data )
			{
				$language = ( $data[0]['User']['lang'] ) ? $data[0]['User']['lang'] : $setting['default_language'];	
				$language=str_replace('-','_',$language);
				Configure::write('Config.language', $language);
					
				$this->_sendEmail( $email, 
								   __('Your Notifications Summary'), 
								   'notification', 
								   array( 'data' => $data )
								);
			}		
		}
	} 

	/**
	 * System wide send email method
	 * @param string $to - recipient's email address
	 * @param string $subject
	 * @param string $template - email template to use
	 * @param array $vars - array of vars to set in email
	 * @param string $from_email - sender's email address
	 */
	protected function _sendEmail( $to, $subject, $template, $vars, $from_email = '', $from_name = '', $body = '' )
	{
		JsnApp::uses('CakeEmail', 'Network/Email');
		$jsnsocial_setting = $this->_getSettings();
		
		$vars['request']   	  = $this->request;
		$vars['jsnsocial_setting']  = $jsnsocial_setting;
		
		if ( empty( $from_email ) )
			$from_email = $jsnsocial_setting['site_email'];
			
		if ( empty( $from_name ) )
			$from_name = $jsnsocial_setting['site_name'];
		
		$email = new CakeEmail();
		$email->from($from_email, $from_name)
			  ->to($to)
			  ->subject($subject)
			  ->template($template)
			  ->viewVars($vars)
			  ->helpers(array('Jsnsocial'))
			  ->emailFormat('html')
              ->transport( $jsnsocial_setting['mail_transport'] );
              
        if ( $jsnsocial_setting['mail_transport'] == 'Smtp' )
        {
            $config = array( 'host' => $jsnsocial_setting['smtp_host'], 'timeout' => 30 );
            
            if ( !empty( $jsnsocial_setting['smtp_username'] ) && !empty( $jsnsocial_setting['smtp_password'] ) )
            {
                $config['username'] = $jsnsocial_setting['smtp_username'];
                $config['password'] = $jsnsocial_setting['smtp_password'];
            }
            
            if ( !empty( $jsnsocial_setting['smtp_port'] ) )
                $config['port'] = $jsnsocial_setting['smtp_port'];
            
            $email->config( $config );
        }

        $email->send($body);   
	}

    private function _getLocales($lang) {
    	$lang=strtolower($lang);

        // Loading the L10n object
        JsnApp::uses('L10n', 'I18n');
        $l10n = new L10n();

        // Iso2 lang code
        $iso2 = $l10n->map($lang);
        if($iso2==''){
        	$lang=substr($lang,0,2);
        	$iso2 = $l10n->map($lang);
        }
        $catalog = $l10n->catalog($lang);
    
        $locales = array(
            $iso2.'_'.strtoupper($iso2).'.'.strtoupper(str_replace('-', '', $catalog['charset'])),    // fr_FR.UTF8
            $iso2.'_'.strtoupper($iso2),    // fr_FR
            $catalog['locale'],                // fre
            $catalog['localeFallback'],        // fre
            $iso2                            // fr
        );
        return $locales;
    }

	private function checkUpdate($jsnsocial_setting)
	{
		if($jsnsocial_setting['version']=='1.0.1' || $jsnsocial_setting['version']=='1.0.0')
		{
			$db=JFactory::getDbo();
			$query="UPDATE `#__jsnsocial_settings` SET `value`='%B %e at %l:%M' WHERE `field`='date_format'";
			$db->setQuery($query);
			$db->execute();
			$query="DELETE FROM `#__jsnsocial_hooks`";
			$db->setQuery($query);
			$db->execute();
			$query="INSERT IGNORE `#__jsnsocial_hooks` (`name`, `key`, `controller`, `action`, `position`, `weight`, `enabled`, `version`, `settings`, `permission`) VALUES
			('Featured Members', 'featured_members', 'home', 'index', 'home_sidebar', 6, 1, '2.0', 'a:0:{}', ''),
			('Popular Blogs', 'popular_blogs', 'blogs', 'index', 'blogs_sidebar', 2, 1, '2.0', 'a:1:{s:9:\"num_blogs\";s:1:\"5\";}', ''),
			('Popular Albums', 'popular_albums', 'photos', 'index', 'photos_sidebar', 3, 1, '2.0', 'a:1:{s:10:\"num_albums\";s:1:\"5\";}', ''),
			('Today Birthdays', 'today_birthdays', 'home', 'index', 'home_sidebar', 4, 1, '2.0', 'a:1:{s:16:\"friend_birthdays\";s:1:\"1\";}', ''),
			('Online Users', 'online_users', '', '', 'home_sidebar', 5, 1, '2.0', 'a:1:{s:16:\"num_online_users\";s:0:\"\";}', ''),
			('Recently Joined', 'recently_joined', 'home', 'index', 'home_sidebar', 7, 1, '2.0', 'a:1:{s:15:\"num_new_members\";s:2:\"12\";}', ''),
			('Popular Events', 'popular_events', 'events', 'index', 'events_sidebar', 8, 1, '2.0', 'a:1:{s:10:\"num_events\";s:1:\"5\";}', ''),
			('Popular Groups', 'popular_groups', 'groups', 'index', 'groups_sidebar', 9, 1, '2.0', 'a:1:{s:10:\"num_groups\";s:1:\"5\";}', ''),
			('Popular Topics', 'popular_topics', 'topics', 'index', 'topics_sidebar', 10, 1, '2.0', 'a:1:{s:10:\"num_topics\";s:1:\"5\";}', ''),
			('Popular Videos', 'popular_videos', 'videos', 'index', 'videos_sidebar', 11, 1, '2.0', 'a:1:{s:10:\"num_videos\";s:1:\"5\";}', ''),
			('Friend Suggestions', 'friend_suggestions', '', '', 'home_sidebar', 12, 1, '2.0', 'a:1:{s:22:\"num_friend_suggestions\";s:1:\"2\";}', ''),
			('Featured Groups', 'featured_groups', 'groups', 'index', 'groups_sidebar', 13, 1, '2.0', 'a:0:{}', '');";
			$db->setQuery($query);
			$db->execute();
			
			if(file_exists(JPATH_SITE.'/plugins/jsn/socialnetwork/app/View/Themed/Blue/Layouts/default.ctp')) unlink(JPATH_SITE.'/plugins/jsn/socialnetwork/app/View/Themed/Blue/Layouts/default.ctp');
		}
		if($jsnsocial_setting['version']=='1.0.9' || $jsnsocial_setting['version']=='1.0.8' || $jsnsocial_setting['version']=='1.0.7' || $jsnsocial_setting['version']=='1.0.6' || $jsnsocial_setting['version']=='1.0.5' || $jsnsocial_setting['version']=='1.0.4' || $jsnsocial_setting['version']=='1.0.3' || $jsnsocial_setting['version']=='1.0.2' || $jsnsocial_setting['version']=='1.0.1' || $jsnsocial_setting['version']=='1.0.0')
		{
			if(file_exists(JPATH_SITE.'/media/socialnetwork/js/jquery-ui.js')) unlink(JPATH_SITE.'/media/socialnetwork/js/jquery-ui.js');
			if(file_exists(JPATH_SITE.'/media/socialnetwork/js/global.js')) unlink(JPATH_SITE.'/media/socialnetwork/js/global.js');
			if(file_exists(JPATH_SITE.'/media/socialnetwork/theme/blue/css/main.css')) unlink(JPATH_SITE.'/media/socialnetwork/theme/blue/css/main.css');
			copy(JPATH_SITE.'/plugins/jsn/socialnetwork/app/webroot/js/jquery-ui.js',JPATH_SITE.'/media/socialnetwork/js/jquery-ui.js');
			copy(JPATH_SITE.'/plugins/jsn/socialnetwork/app/webroot/js/global.js',JPATH_SITE.'/media/socialnetwork/js/global.js');
			copy(JPATH_SITE.'/plugins/jsn/socialnetwork/app/webroot/theme/blue/css/main.css',JPATH_SITE.'/media/socialnetwork/theme/blue/css/main.css');
		}
		if($jsnsocial_setting['version']!='1.1.7')
		{
			$db=JFactory::getDbo();
			$query="UPDATE `#__jsnsocial_settings` SET `value`='1.1.7' WHERE `field`='version'";
			$db->setQuery($query);
			$db->execute();
			$query="DELETE FROM `#__extensions` WHERE `name`='pkg_jsnsocial'";
			$db->setQuery($query);
			$db->execute();
		}
		//$db=JFactory::getDbo();
		//$query="ALTER TABLE #__jsnsocial_activities MODIFY COLUMN `text` text COLLATE utf8_unicode_ci NOT NULL DEFAULT ''";
		//$db->setQuery($query);
		//$db->execute();
		
	}
}
