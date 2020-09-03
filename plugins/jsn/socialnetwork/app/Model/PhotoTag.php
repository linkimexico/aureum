<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class PhotoTag extends AppModel {
		
	public $belongsTo = array( 'Photo', 'User');
	
	public function getPhotos( $user_id = null, $page = 1 )
	{
		$photos = $this->find( 'all', array( 'conditions' => array( 'PhotoTag.user_id' => $user_id ), 
										     'order'	  => 'PhotoTag.id desc',
										     'limit'	  => RESULTS_LIMIT,
										     'page' 	  => $page
									) 	);
		
		return $photos;
	}

}
