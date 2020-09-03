<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ConversationsController extends AppController 
{
	public function ajax_browse()
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');	
		
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$this->loadModel( 'ConversationUser' );

		$this->Conversation->unbindModel(
			array('belongsTo' => array('User'))
		);
        
        $this->Conversation->unbindModel(
            array('hasMany' => array('Comment'))
        );
		
		/*$this->Conversation->unbindModel(
			array('hasMany' => array('ConversationUser', 'Comment'))
		);
		
		$this->ConversationUser->unbindModel(
			array('belongsTo' => array('User'))
		);*/
		
		$this->ConversationUser->recursive = 3;
		$conversations = $this->ConversationUser->find( 'all', array( 'conditions' => array( 'ConversationUser.user_id' => $uid ), 
																	  'limit' => RESULTS_LIMIT, 
												 					  'page' => $page,
																	  'order' => 'modified desc'
													)	);

		$this->set('conversations', $conversations);
		$this->set('more_url', '/conversations/ajax_browse/page:' . ( $page + 1 ) ) ;
		
		if ( $page > 1 )
			$this->render('/Elements/lists/messages_list');
	}
	
	public function ajax_send($recipient = null)
	{
		$this->_checkPermission( array( 'confirm' => true ) );
		$uid = $this->Session->read('uid');

		if ( !empty($recipient) )
		{
			$this->loadModel( 'User' );
			$to = $this->User->findById($recipient);
			$this->_checkExistence( $to );
			$this->set('to', $to);
		}
	}

	public function ajax_doSend()
	{			
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$uid = $this->Session->read('uid');

		$this->request->data['user_id'] = $uid;
		$this->request->data['lastposter_id'] = $uid;
		
		$this->Conversation->set( $this->request->data );
		$this->_validateData( $this->Conversation );
		
		// @todo: validate recipients
		
		if ( !empty($this->request->data['friends']) )
		{
			$recipients = explode( ',', $this->request->data['friends'] );
				
			if ( $this->Conversation->save() ) // successfully saved	
			{
				// save convo users
				$participants = array();
			
				foreach ( $recipients as $participant )
					$participants[] = array('conversation_id' => $this->Conversation->id, 'user_id' => $participant);

				// add sender to convo users array
				$participants[] = array('conversation_id' => $this->Conversation->id, 'user_id' => $uid, 'unread' => 0);

				$this->loadModel( 'ConversationUser' );
				$this->ConversationUser->saveAll( $participants );
				
				$this->loadModel( 'Notification' );
				$this->Notification->record( array( 'recipients' => $recipients,
													'sender_id' => $uid,
													'action' => 'message_send',
													'url' => '/conversations/view/'.$this->Conversation->id
				) );
				
				
				$response['result'] = 1;
                $response['id'] = $this->Conversation->id;
                echo json_encode($response);
			}
		}
		else
			$this->_jsonError(__('Recipient is required'));
	}

	public function view($id)
	{
		$id = intval($id);
		$this->_checkPermission();
		$uid = $this->Session->read('uid');

		$conversation = $this->Conversation->findById($id);
		$this->_checkExistence( $conversation );

		// check permission to view
		$this->loadModel('ConversationUser');
		$convo_users = $this->ConversationUser->findAllByConversationId($id);
		$users_array = array();

		foreach ($convo_users as $user)
		{
			$users_array[] = $user['ConversationUser']['user_id'];

			if ( $uid == $user['ConversationUser']['user_id'] )
				$convo_user = $user['ConversationUser'];
		}

		$this->_checkPermission( array( 'admins' => $users_array ) );

		// set to read if unread
		if ( $convo_user['unread'] )
		{
			$this->ConversationUser->id = $convo_user['id'];
			$this->ConversationUser->save( array( 'unread' => 0 ) );
		}

		// get messages
		$this->loadModel('Comment');
		$comments = $this->Comment->getComments( $id, APP_CONVERSATION );
		
		// get friends
		$this->loadModel( 'Friend' );
		$friends = $this->Friend->getFriends($uid);
		
		$this->set('convo_users', $convo_users);
		$this->set('comments', $comments);
		$this->set('friends', $friends);
		$this->set('conversation', $conversation);
		$this->set('more_comments', '/comments/ajax_browse/conversation/' . $id . '/page:2');
		$this->set('title_for_layout', $conversation['Conversation']['subject']);
	}
	
	public function ajax_add($msg_id = null)
	{
		$msg_id = intval($msg_id);
		$this->_checkPermission( array( 'confirm' => true ) );

		$this->set('msg_id', $msg_id);
	}
	
	public function ajax_doAdd()
	{			
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		
		if ( !empty($this->request->data['friends']) )
		{		
			$msg_id = $this->request->data['msg_id'];
			$uid = $this->Session->read('uid');
            $friends = explode(',', $this->request->data['friends']);
			
			$this->loadModel( 'ConversationUser' );
			$users = $this->ConversationUser->getUsersList( $msg_id );
			$this->_checkPermission( array( 'admins' => $users ) ); // check to see if the user is a participant
			
			//$this->ConversationUser->save( array('conversation_id' => $msg_id, 'user_id' => $uid) );
			
			$participants = array();
			foreach ( $friends as $participant )
                if ( !in_array($participant, $users) )
				    $participants[] = array('conversation_id' => $msg_id, 'user_id' => $participant);
	
            if ( !empty($participants) )
            {
    			$this->ConversationUser->saveAll( $participants );
    			
    			$this->loadModel( 'Notification' );
    			$this->Notification->record( array( 'recipients' => $friends,
    												'sender_id' => $uid,
    												'action' => 'conversation_add',
    												'url' => '/conversations/view/'.$msg_id
    			) );
            }
            
            $response['result'] = 1;
		}
		else
        {
            $response['result'] = 0;
            $response['message'] = __('Please select at least one person');        
        }
        
        echo json_encode($response);
	}
	
	public function do_leave( $msg_id = null )
	{
		$msg_id = intval($msg_id);
		$this->_checkPermission( array( 'confirm' => true ) );
		$uid = $this->Session->read('uid');
		
		$this->loadModel( 'ConversationUser' );
		$this->ConversationUser->deleteAll( array( 'conversation_id' => $msg_id, 'ConversationUser.user_id' => $uid ), true, true );
		
		$this->redirect( '/home/index/tab:messages' );
	}
}

?>
