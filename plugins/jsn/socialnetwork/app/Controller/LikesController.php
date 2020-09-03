<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class LikesController extends AppController {

	public function ajax_add($type = null, $id = null, $thumb_up = null)
	{
		$id = intval($id);
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );

		$uid = $this->Session->read('uid');
		
		$model = ucfirst( $type );
		$this->loadModel( $model );
		
		$item = $this->$model->findById( $id );	
		$this->_checkExistence( $item );
        
        // check to see if user already liked this item
        $like = $this->Like->getUserLike( $id, $uid, $type );
        $this->$model->id = $id;
        $re = array('like_count' => $item[$model]['like_count'], 'dislike_count' => $item[$model]['dislike_count']);
        
        if ( !empty( $like ) ) // user already thumb up/down this item
        {
            if ( $like['Like']['thumb_up'] != $thumb_up )
            {
                if ( $thumb_up ) // user thumbed down before
                {
                    $this->$model->increaseCounter($id, 'like_count');
                    $this->$model->decreaseCounter($id, 'dislike_count');
                    
                    $re['like_count']++;
                    $re['dislike_count']--;
                }
                else
                {
                    $this->$model->increaseCounter($id, 'dislike_count');
                    $this->$model->decreaseCounter($id, 'like_count');
                    
                    $re['dislike_count']++;
                    $re['like_count']--;
                }
                    
                $this->Like->id = $like['Like']['id'];
                $this->Like->save( array( 'thumb_up' => $thumb_up ) );
            }
            else // remove the entry
            {
                if ( $thumb_up )
                {
                    $this->$model->decreaseCounter($id, 'like_count');
                    $re['like_count']--;
                }
                else
                {
                    $this->$model->decreaseCounter($id, 'dislike_count');
                    $re['dislike_count']--;
                }
                  
                $this->Like->delete( $like['Like']['id'] );              
            }
        }
        else 
        {
    		$data = array('type' => $type, 'target_id' => $id, 'user_id' => $uid, 'thumb_up' => $thumb_up);
    		$this->Like->save($data);
    		
    		if ( $thumb_up )
            {
                $this->$model->increaseCounter($id, 'like_count');
                $re['like_count']++;
                
                // do not send notification when user like comment
                if ( !in_array( $type, array('activity_comment', 'comment') ) )
                {       
                    // send notification to author
                    if ( $uid != $item['User']['id'] )
                    {                                   
                        switch ( $type )
                        {
                            case APP_PHOTO:
                                $action = 'photo_like';
                                $params = '';
                                break;
                                
                            case 'activity':
                                $action = 'activity_like';
                                $params = '';
                                break;
                                
                            default:
                                $action = 'item_like';
                                $params = h($item[$model]['title']);
                        }
                                        
                        if ( !empty( $item[$model]['group_id'] ) ) // group topic / video
                        {
                            $url = '/groups/view/' . $item[$model]['group_id'] . '/' . $type . '_id:' . $id;
                        }
                        elseif ( $type == 'activity' ) // activity
                        {
                            $url = '/users/view/' . $item['User']['id'] . '/activity_id:' . $id;
                        }
                        else
						{
                            $url = '/' . $type . 's/view/' . $id;
							
							if ( $type == APP_PHOTO )
								$url .= '#content';
						}
                        
                        $this->loadModel( 'Notification' );
                        $this->Notification->record( array( 'recipients'  => $item['User']['id'],
                                                            'sender_id'   => $uid,
                                                            'action'      => $action,
                                                            'url'         => $url,
                                                            'params'      => $params
                                                    ) );
                    }
                }
            }
            else
            {
                $this->$model->increaseCounter($id, 'dislike_count');
                $re['dislike_count']++;
            }
        }		

		echo json_encode($re);
	}

	public function ajax_show($type = null, $id = null)
	{
		$id = intval($id);
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
			
		$users = $this->Like->getLikes( $id, $type, RESULTS_LIMIT, $page );
		
		$this->set( 'users', $users );
		$this->set('page', $page);
		$this->set('more_url', '/likes/ajax_show/' . $type . '/' . $id . '/page:' . ( $page + 1 ) );
		
		$this->render('/Elements/ajax/user_overlay');	
	}
}

