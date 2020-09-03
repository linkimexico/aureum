<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Language extends AppModel 
{
    public $validate = array(   
                        'name' =>   array(   
                            'rule' => 'notEmpty',
                            'message' => 'Name is required'
                        ),
                        'key' =>    array(   
                            'key' => array(
                                  'rule' => 'alphaNumeric',
                                  'allowEmpty' => false,
                                  'message' => 'Key must only contain letters and numbers'
                            ),
                            'uniqueKey' => array(
                                  'rule' => 'isUnique',
                                  'message' => 'Key already exists'
                            )
                        )                                           
    );
    
    public $order = 'Language.name asc';
	
	public function getLanguages()
	{
		$site_langs = Cache::read('site_langs');
        
        if ( empty($site_langs) ) 
        {
            $site_langs = $this->find('list', array( 'fields' => array( 'Language.key', 'Language.name' ) ) );
            Cache::write('site_langs', $site_langs);
        }

		return $site_langs;
	}

}
