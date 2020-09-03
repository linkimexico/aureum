<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Friend extends AppModel {
		
	public $belongsTo = array( 'User'  => array('counterCache' => true	));

	/*
	 * Return a list of friends for dropdown list
	 * @param int $uid
	 * @param array $excludes an array of user ids to exclude
	 */
	public function getFriendsList( $uid, $excludes = array() )
	{
		$this->unbindModel(
			array('belongsTo' => array('User'))
		);

		$this->bindModel(
			array('belongsTo' => array(
					'User' => array(
						'className' => 'User',
						'foreignKey' => 'friend_id'
					)
				)
			)
		);

		$cond = array( 'Friend.user_id' => $uid, 'User.active' => 1 );
		
		if ( !empty( $excludes ) )
			$cond['NOT'] = array( 'Friend.friend_id' => $excludes );
		
		$friends = $this->find( 'all', array( 'conditions' => $cond, 
											  'fields' 	   => array( 'User.id' ),
											  'order'	   => 'User.name asc'
							) 	); // have to do this because find(list) does not work with bindModel
		$friend_options = array();

		foreach ($friends as $friend)
			$friend_options[$friend['User']['id']] = $friend['User']['name'];

		return $friend_options;
	}

	/*
	 * Return an array of friend ids
	 */
	public function getFriends( $uid )
	{
		$friends = $this->find( 'list' , array( 'conditions' => array( 'Friend.user_id' => $uid ), 
												'fields' => array( 'friend_id' ) 
							) );	
		return $friends;
	}
	
	/*
	 * Return a list of friends for displaying
	 */
	public function getUserFriends( $uid, $page = 1, $limit = RESULTS_LIMIT )
	{
		$this->unbindModel(
			array('belongsTo' => array('User'))
		);

		$this->bindModel(
			array('belongsTo' => array(
					'User' => array(
						'className' => 'User',
						'foreignKey' => 'friend_id'
					)
				)
			)
		);

		$friends = $this->find('all', array( 'conditions' => array( 'Friend.user_id' => $uid, 'User.active' => 1 ), 
		                                     'order' => 'Friend.id desc',
											 'limit' => $limit, 
											 'page' => $page)
		);

		return $friends;
	}
    
    /*
     * Return a list of friends for searching
     */
    public function searchFriends( $uid, $q )
    {
        $this->unbindModel(
            array('belongsTo' => array('User'))
        );

        $this->bindModel(
            array('belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'friend_id'
                    )
                )
            )
        );
        
        $friends = $this->find( 'all', array( 'conditions' =>  array( 'Friend.user_id' => $uid, 
                                                                      'User.active' => 1,
                                                                      'User.name LIKE ?' => $q . '%' ), 
                                              'fields'     => array( 'User.id' ),
                                              'order'      => 'User.name asc'
                            )   ); 

        // have to do this because find(list) does not work with bindModel
        $friend_options = array();

        foreach ($friends as $friend)
            $friend_options[] = array( 'id' => $friend['User']['id'], 'name' => $friend['User']['name'], 'avatar' => $friend['User']['avatar'] );

        return $friend_options;
    }
	
	/*
	 * Get friend suggestions of $uid (mutual friends)
	 * @param int $uid
	 * @param boolean $bigList - view all list or not (right column block)
	 * @return array $suggestions
	 */
	
	public function getFriendSuggestions( $uid, $bigList = false, $limit = 2 )
	{
		// get friends of current user
		$friends = $this->getFriends( $uid );
		$suggestions = array();			
		
		if ( !empty( $friends ) )
		{
			JsnApp::import('Model', 'FriendRequest');
			
			// get friend requests of current users
			$req = new FriendRequest();
			$requests = $req->find( 'list', array( 'conditions' => array('FriendRequest.sender_id' => $uid), 
												   'fields' => array('FriendRequest.user_id') 
								) 	);
								
			// merge with friends list
			$not_in = array_merge($friends, $requests);
			$not_in[] = $uid;			
						
			$this->unbindModel(
				array('belongsTo' => array('User'))
			);
	
			$this->bindModel(
				array('belongsTo' => array(
						'User' => array(
							'className' => 'User',
							'foreignKey' => 'friend_id'
						)
					)
				)
			);
	
			if ( $bigList )
			{
				$suggestions = $this->find('all', array('conditions' => array( 'Friend.user_id' => $friends, 
																			   'User.active' => 1, 
																			   'NOT' => array('Friend.friend_id' => $not_in)
																			 ), 																		 
														'fields' => array( 'DISTINCT User.id', 
																		   '(SELECT count(*) FROM ' . $this->tablePrefix . 'friends WHERE user_id = User.id AND friend_id IN (' . implode(',', $friends) . ') ) as count'
																		 ),	
														'order' => 'count desc',																 
														'limit' => RESULTS_LIMIT * 2));
			}
			else {
				$suggestions = $this->find('all', array('conditions' => array( 'Friend.user_id' => $friends, 
																			   'User.active' => 1, 
																			   'NOT' => array('Friend.friend_id' => $not_in),
																			   'Friend.friend_id >= (SELECT FLOOR( MAX(id) * RAND()) FROM ' . $this->tablePrefix . 'users)'
																			 ), 																		 
														'fields' => array( 'DISTINCT User.id', 
																		   '(SELECT count(*) FROM ' . $this->tablePrefix . 'friends WHERE user_id = User.id AND friend_id IN (' . implode(',', $friends) . ') ) as count'
																		 ),																	 
														'limit' => $limit));
			}
			
		}
		
		return $suggestions;
	}
	
	public function getMutualFriends( $uid1, $uid2, $limit = RESULTS_LIMIT, $page = 1 )
	{
		// get friends of the first user
		$friends = $this->getFriends( $uid1 );
		$mutual_friends = array();			
		
		if ( !empty( $friends ) )
		{			
			$this->unbindModel(
				array('belongsTo' => array('User'))
			);
	
			$this->bindModel(
				array('belongsTo' => array(
						'User' => array(
							'className' => 'User',
							'foreignKey' => 'friend_id'
						)
					)
				)
			);	

			$mutual_friends = $this->find('all', array('conditions' => array( 'Friend.user_id' => $uid2, 
																		   	  'User.active' => 1, 
																		  	  'Friend.friend_id' => $friends																		   	  
																		 ), 																		 
													   'fields' => array( 'DISTINCT User.id', 'User.friend_count', 'User.photo_count'),
													   'limit' => $limit,
													   'page' => $page
			)	);
		}
		
		return $mutual_friends;
	}

	/*
	 * Are we friends?
	 */
	public function areFriends( $uid1, $uid2 )
	{
		$this->cacheQueries = true;
		
		$count = $this->find( 'count', array( 'conditions' => array( 'Friend.user_id' => $uid1, 'Friend.friend_id' => $uid2 ) ) );
		return $count;		
	}
}
 