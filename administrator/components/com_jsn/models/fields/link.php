<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


JFormHelper::loadFieldClass('url');

/**
 * Form Field class for the Joomla Platform.
 * Provides spacer markup to be used in form layouts.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldLink extends JFormFieldUrl
{

	public $type = 'Link';

	public function getLink()
	{
		$html=array();
		
		if($this->value) $html[]='<a rel="noopener noreferrer" target="_blank" rel="'.$this->element['rel'].'" href="'.$this->value.'">'.$this->value.'</a>';
		return implode('', $html);
	}

}
