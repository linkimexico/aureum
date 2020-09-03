<?php
/**
 * --------------------------------------------------------------------------------
 * APP - Retainful next order coupon
 * --------------------------------------------------------------------------------
 * @package     Joomla  3.x
 * @subpackage  J2 Store
 * @author      Sathyaseelan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2019 J2Store . All rights reserved.
 * @license     GNU/GPL license: v3 or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/j2html.php';

class JFormFieldMessageeditor extends JFormFieldList
{

    protected $type = 'messageeditor';

    public function getInput()
    {
        $value = $this->value;
        if (empty($value)) {
            $value = JText::_('J2STORE_COUPONNEXTORDER_COUPON_MESSAGE_DEFAULT');
        }
        $editor = JFactory::getEditor();
        return $editor->display($this->name, $value, '100%', '200px', 20, 50);
    }
}
