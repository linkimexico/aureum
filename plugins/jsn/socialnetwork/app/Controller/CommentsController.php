<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class CommentsController extends AppController 
{
		
	public function ajax_browse( $type = null, $target_id = null )
	{
		$target_id = intval($target_id);
		$uid = $this->Session->read('uid');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		
		$model = ucfirst( $type );
		$this->loadModel( $model );
		
		// get the item that was commented on
		$item = $this->$model->findById( $target_id );	
		$this->_checkExistence( $item );	
		
		$admins = array();
		
		// topic author cannot delete comments
		if ( $type != APP_TOPIC )
			$admins[] = $item[$model]['user_id'];
		
		// if it belongs to a group then the group admins can delete
		if ( !empty( $item[$model]['group_id'] ) )
		{
			$this->loadModel('GroupUser');				
							
			if ( $item['Group']['type'] == PRIVACY_PRIVATE )
			{
				$cuser = $this->_getUser();		
				$is_member = $this->GroupUser->isMember( $cuser['id'], $item[$model]['group_id'] );				
				
				if ( !$cuser['Role']['is_admin'] && !$is_member )
					return;
			}
			
			$group_admins = $this->GroupUser->getUsersList( $item[$model]['group_id'], GROUP_USER_ADMIN );
			$admins = array_merge( $admins, $group_admins );
		}
		
		$this->set('admins', $admins);
		
		$comments = $this->Comment->getComments( $target_id, $type, $page );		
		$this->set('comments', $comments);
		
		// get comment likes
		if ( !empty( $uid ) )
		{
			$this->loadModel( 'Like' );			
										
			$comment_likes = $this->Like->getCommentLikes( $comments, $uid );
			$this->set('comment_likes', $comment_likes);
		}
		
        $this->set('more_comments', '/comments/ajax_browse/' . h($type) . '/' . intval($target_id) . '/page:' . ($page + 1));        
		$this->render('/Elements/comments');
	}

	public function ajax_share()
	{
		$this->_checkPermission( array( 'confirm' => true ) );	
		$uid = $this->Session->read('uid');

		$this->request->data['user_id'] = $uid;

		if ($this->Comment->save($this->request->data))
		{
			$comment = $this->Comment->read();
			$this->set('comment', $comment);			
			
			switch ($this->request->data['type'])
			{
				case APP_CONVERSATION:
					$this->loadModel('Conversation');
					$this->loadModel('ConversationUser');
					
					// update unread var for participants, update modified field, message count field for convo, add noti and send email
					$this->Conversation->id = $this->request->data['target_id'];
					$conversation = $this->Conversation->read();

					$this->Conversation->save( array( 'lastposter_id' => $uid,
													  //'modified' => date("Y-m-d H:i:s"), 
													  'message_count' => $conversation['Conversation']['message_count'] + 1
											)	 );
											
					$participants = $this->ConversationUser->find( 'list', array( 'conditions' => array( 'conversation_id' => $this->request->data['target_id'],
																									     'ConversationUser.user_id <> '.$uid
																									),
																				  'fields' => array( 'ConversationUser.user_id' ),
																				  'group' => 'ConversationUser.user_id'
																)	);

					foreach ($participants as $key => $value)
					{
						$this->ConversationUser->id = $key;
						$this->ConversationUser->save( array( 'unread' => 1 ) );						
					}										
					
					$this->loadModel( 'Notification' );
					$this->Notification->record( array( 'recipients'  => $participants,
														'sender_id'   => $uid,
														'action'	  => 'message_reply',
														'params'	  => h($conversation['Conversation']['subject']),
														'url' 		  => '/conversations/view/'.$this->request->data['target_id']													
												) );
				break;

				case APP_TOPIC:
					$this->loadModel('Topic');
					
					// update last poster id, last post date and wall count					
					$this->Topic->id = $this->request->data['target_id'];
					$topic = $this->Topic->findById( $this->request->data['target_id'] );

					$this->Topic->save( array( 'lastposter_id' => $uid, 
											   'last_post' 	   => date("Y-m-d H:i:s"), 
											   'comment_count' => $topic['Topic']['comment_count'] + 1
									)	 );
									
					$this->_sendNotifications( $this->request->data );
				break;
				
				case APP_BLOG:
					$this->loadModel('Blog');
					$this->Blog->increaseCounter( $this->request->data['target_id'] );		
					$this->_sendNotifications( $this->request->data );
				break;

				default:			
					if ( $this->request->data['type'] != APP_PAGE )							
						$this->_sendNotifications( $this->request->data );
			}
		}

		if ( !empty( $this->request->data['activity'] ) )
			$this->set('activity', true);
	}

	private function _sendNotifications( $data )
	{
		$uid = $this->Session->read('uid');
		$cuser = $this->_getUser();
		
		$this->loadModel('Notification');
		
		$model = ucfirst( $data['type'] );		
		$this->loadModel( $model );
				
		$obj = $this->$model->findById( $data['target_id'] );
		
		// group topic / video
		if ( !empty( $obj[$model]['group_id'] ) )
			$url = '/groups/view/' . $obj[$model]['group_id'] . '/' . $data['type'] . '_id:' . $data['target_id'];
		else
			$url = '/' . $data['type'] . 's/view/' . $data['target_id'];
		
		// send notifications to anyone who commented on this item within a day
		$users = $this->Comment->find( 'list', array( 'conditions' => array( 'Comment.target_id' => $data['target_id'], 
																			 'Comment.type' => $data['type'],
																			 'Comment.user_id <> '.$uid.' AND Comment.user_id <> '.$obj['User']['id'],
																			 'DATE_SUB(CURDATE(),INTERVAL 1 DAY) <= Comment.created'
																		),
													  'fields' => array('Comment.user_id'),
													  'group' => 'Comment.user_id'
											)	);
		
		if ($data['type'] == APP_PHOTO)
		{
			$action = 'photo_comment';
			$params = serialize( array( 'actor' => $cuser, 'owner' => $obj['User'] ) );
			$url .= '#content';
		}			
		else
		{
			$action = 'item_comment';
			$params = h($obj[$model]['title']);
		}
		
		if ( !empty( $users ) )
		{
			$this->Notification->record( array( 'recipients'  => $users,
												'sender_id'   => $uid,
												'action'	  => $action,
												'url' 		  => $url,
												'params'	  => $params
										) );
		}
		
		//$privacy = ( !empty( $obj[$model]['privacy'] ) ) ? $obj[$model]['privacy'] : PRIVACY_EVERYONE;
		$content = summary( $data['message'], 160 );
		
		// insert into activity feed
		$this->loadModel( 'Activity' );
		
		if ( $data['type'] == APP_PHOTO ) // update item comment activity
		{
            // check privacy of album and group of this photo, if it's not for everyone then do not show it at all
            $update_activity = false;
            
            switch ( $obj['Photo']['type'] )
            {
                case APP_GROUP:
                    $this->loadModel('Group');
                    $group = $this->Group->findById( $obj['Photo']['target_id'] );
                    
                    if ( $group['Group']['type'] != PRIVACY_PRIVATE )
                        $update_activity = true;
                    
                    break;
                    
                case APP_ALBUM:
                    $this->loadModel('Album');
                    $album = $this->Album->findById( $obj['Photo']['target_id'] );
                    
                    if ( $album['Album']['privacy'] == PRIVACY_EVERYONE )
                        $update_activity = true;
                    
                    break;   
            }
            
            if ( $update_activity )
            {
                $activity = $this->Activity->find( 'first', array( 
                                                        'conditions' => array( 
                                                            'Activity.item_type' => APP_PHOTO, 
                                                            'Activity.item_id'   => $data['target_id'],
                                                            'Activity.params'    => 'no-comments',
                                                            'Activity.type'      => 'user'
                )   )   );
                
                if ( !empty( $activity ) ) // update the latest one
                {
                    $this->Activity->id = $activity['Activity']['id'];
                    $this->Activity->save( array( 'user_id' => $uid, 
                                                  'content' => $content
                                         ) );
                }
                else // insert new      
                    $this->Activity->save( array( 'type'      => 'user',
                                                  'action'    => 'comment_add',
                                                  'user_id'   => $uid,
                                                  'content'   => $content,
                                                  'item_type' => $data['type'],
                                                  'item_id'   => $data['target_id'],
                                                  'query'     => 1,
                                                  'params'    => 'no-comments'
                                        ) );
    	   }
		}
		else // update item activity
		{
			$activity = $this->Activity->getItemActivity( $data['type'], $data['target_id'] );
			
			if ( !empty( $activity ) )
			{
				$this->Activity->id = $activity['Activity']['id'];
				$this->Activity->save( array( 'modified' => date("Y-m-d H:i:s") ) );
			}
		}

		// send notification to author
		if ( $uid != $obj['User']['id'] )
		{
			if ($data['type'] == APP_PHOTO)
				$action = 'own_photo_comment';
	
			$this->Notification->record( array( 'recipients'  => $obj['User']['id'],
												'sender_id'   => $uid,
												'action' 	  => $action,
												'url' 		  => $url,
												'params'	  => $params
										) );
		}
	}
	
	public function ajax_remove()
	{
		$this->autoRender = false;		
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$comment = $this->Comment->findById( $this->request->data['id'] );
		$this->_checkExistence( $comment );
		
		$model = ucfirst( $comment['Comment']['type'] );
		$this->loadModel( $model );
		
		// get the item that was commented on
		$item = $this->$model->findById( $comment['Comment']['target_id'] );	
		$this->_checkExistence( $item );	
		
		$admins = array( $comment['Comment']['user_id'] );
		
		// topic author cannot delete comments
		if ( $comment['Comment']['type'] != APP_TOPIC )
			$admins[] = $item[$model]['user_id'];
		
		// if it belongs to a group then the group admins can delete
		if ( !empty( $item[$model]['group_id'] ) )
		{
			$this->loadModel('GroupUser');
			
			$group_admins = $this->GroupUser->getUsersList( $item[$model]['group_id'], GROUP_USER_ADMIN );
			$admins = array_merge( $admins, $group_admins );
		}
		
		$this->_checkPermission( array( 'admins' => $admins ) );
		$this->Comment->delete( $this->request->data['id'] );
		
		// descrease comment count
		if ( in_array( $comment['Comment']['type'], array( APP_TOPIC, APP_BLOG ) ) )
			$this->$model->decreaseCounter( $comment['Comment']['target_id'] );
		
		// delete activity
		$this->loadModel('Activity');
		$this->Activity->deleteAll( array( 'action' => 'comment_add', 'Activity.item_type' => $comment['Comment']['type'], 'item_id' => $comment['Comment']['target_id'] ), true, true );
	}
}
