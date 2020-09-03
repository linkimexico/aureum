<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class HomeController extends AppController {
	
	public function index()
	{		
		$uid = $this->Session->read('uid');
		$jsnsocial_setting = $this->_getSettings();		
		
		$this->loadModel( 'Tag' );
		$tags = $tags = $this->Tag->getTags(null, $jsnsocial_setting['popular_interval']);	
		
		$this->loadModel( 'EventRsvp' );
		$events_count = $this->EventRsvp->getMyEventsCount( $uid );		
			
		if ( !empty( $this->request->named['tab'] ) ) // open a specific tab
		{
			$this->_checkPermission();	
			$this->set('tab', str_replace(':','-',$this->request->named['tab']));
		}
		else
		{
			if ( !empty( $uid ) || ( empty( $uid ) && $jsnsocial_setting['default_feed'] == 'everyone' && !$jsnsocial_setting['hide_activites'] ) )
            {   
    			$this->loadModel( 'Activity' );
                $activity_feed = $jsnsocial_setting['default_feed'];
                
                // save activity feed that you selected
                if ( !empty( $uid ) && $jsnsocial_setting['feed_selection'] && $this->Cookie->read('activity_feed') )
                    $activity_feed = $this->Cookie->read('activity_feed');
                
    			$activities = $this->Activity->getActivities( $activity_feed, $uid );
    			
    			// get activity likes
    			if ( !empty( $uid ) )
    			{					
    				$this->loadModel('Like');				
    				$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
    				$this->set('activity_likes', $activity_likes);
    			}
    			
    			$this->set('activities', $activities);
                $this->set('activity_feed', $activity_feed);
            }
		}
				
		$this->set('events_count', $events_count);
		$this->set('tags', $tags);
		$this->set('title_for_layout', __('Home'));		
	}

	public function ajax_theme() {}
	public function ajax_lang() {}
	
	public function do_theme( $theme_key )
	{
		if ( !empty( $theme_key ) )
			$this->Cookie->write('theme', $theme_key);
		
		$this->redirect( $this->referer() );
	}
    
    /*public function do_fullsite()
    {
        $jsnsocial_setting = $this->_getSettings();
        $this->Cookie->write('theme', $jsnsocial_setting['default_theme']);
        
        $this->redirect( $this->referer() );
    }*/
    
    public function do_fullsite()
    {
        $this->Session->write('fullsite', 1);
        $this->redirect( $this->referer() );
    }
    
    public function do_mobile()
    {
        $this->Session->delete('fullsite');
        $this->redirect( $this->referer() );
    }
	
	public function do_language( $key )
	{
		if ( !empty( $key ) )
		{
			$this->Cookie->write('language', $key);
			
			$uid = $this->Session->read('uid');
			
			// update user profile if logged in
			if ( !empty( $uid ) )
			{
				$this->loadModel('User');
				
				$this->User->id = $uid;
				$this->User->save( array( 'lang' => $key ) );
			}
		}
		
		$this->redirect( $this->referer() );
	}
	
	public function contact()
	{
		if ( !empty( $this->request->data ) )
		{
			if ( Validation::email( trim( $this->request->data['email'] ) ) )
			{
				$setting = $this->_getSettings();
				$this->_sendEmail( $setting['site_email'], $this->request->data['subject'], null, null, trim( $this->request->data['email'] ), $this->request->data['name'], $this->request->data['message'] );
				
				$this->Session->setFlash( __('Thanks you! Your message has been sent') );
			}
			else
				$this->Session->setFlash( __('Invalid email address'), 'default', array( 'class' => 'error-message' ) );
			
			$this->redirect( $this->referer() );
		}
	}


	public function admin_index()
	{
		$this->loadModel('User');
		$this->loadModel('Photo');
		$this->loadModel('Blog');
		$this->loadModel('Group');
		$this->loadModel('Event');
		$this->loadModel('Topic');
		$this->loadModel('Video');
		$this->loadModel('AdminNotification');
        $this->loadModel('Activity');
		
		$admin_notifications = $this->AdminNotification->find('all', array( 'limit' => RESULTS_LIMIT ));
        
        /*JsnApp::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();
        
        $content = $HttpSocket->get( 'https://www.easy-profile.com/news-rss.feed?type=rss' ) ;
        
        libxml_use_internal_errors(true); 
        try 
        {
		    $xml = new SimpleXMLElement( $content );
            $this->set('feeds', $xml->channel);
        }
        catch (Exception $e) {}*/
        
        $stats = Cache::read('admin_stats');
        
        if (!$stats) 
        {        
            $date = new DateTime();
            $stats = array();
            
            for ( $i = 1; $i <= 7; $i++ )
            {
                //$date->sub(new DateInterval('P1D'));
                $date->modify('-1 day');
                
                $stats[$date->format('M j')]['users'] = $this->User->find('count', array( 'conditions' => array( 
                    'Profile.registerDate >= ?' => $date->format('Y-m-d') . ' 00:00:00',
                    'Profile.registerDate <= ?' => $date->format('Y-m-d') . ' 23:59:59'
                ) ) );
                
                $stats[$date->format('M j')]['activities'] = $this->Activity->find('count', array( 'conditions' => array( 
                    'Activity.created >= ?' => $date->format('Y-m-d') . ' 00:00:00',
                    'Activity.created <= ?' => $date->format('Y-m-d') . ' 23:59:59'
                ) ) );
            }
        
            $stats = array_reverse( $stats, true );
            
            Cache::write('admin_stats', $stats);
        }
		
        $this->set('stats', $stats);
		$this->set('admin_notifications', $admin_notifications);
		$this->set('user_count', $this->User->find( 'count' ));
		$this->set('photo_count', $this->Photo->find( 'count' ));
		$this->set('blog_count', $this->Blog->find( 'count' ));
		$this->set('group_count', $this->Group->find( 'count' ));
		$this->set('event_count', $this->Event->find( 'count' ));
		$this->set('topic_count', $this->Topic->find( 'count' ));
		$this->set('video_count', $this->Video->find( 'count' ));
		
		$this->set('title_for_layout', 'Admin Dashboard');
	}
	
	public function admin_login()
	{
		if ( !empty( $this->request->data ) )
		{		
			$this->loadModel('User');
    
            // find the user
            $user = $this->User->find( 'first', array( 'conditions' => array( 'email'    => trim( $this->request->data['admin_email'] ), 
                                                                              'password' => md5( trim( $this->request->data['admin_password'] ) . Configure::read('Security.salt') )
                                    )   )   );
    
            if (!empty($user)) // found                        
                $this->Session->write('admin_login', 1);
			else
				$this->Session->setFlash('Invalid email or password', 'default', array( 'class' => 'error-message' ) );
			
			$this->redirect( '/admin/home' );
		}
	}
}

