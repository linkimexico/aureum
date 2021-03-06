<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;



JFormHelper::loadFieldClass('password');

/**
 * Form Field class for the Joomla Platform.
 * Provides spacer markup to be used in form layouts.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldConfirmpassword extends JFormFieldPassword
{

	public $type = 'Confirmpassword';

	protected function getInput()
	{
		$html = parent::getInput();
		$html.= '<div class="message-regex">'.JText::_('COM_JSN_NOT_EQUAL_PASSWORD').'</div>';
		return $html;
	}



}
