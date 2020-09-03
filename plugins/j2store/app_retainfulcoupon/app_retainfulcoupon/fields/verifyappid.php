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

class JFormFieldVerifyappid extends JFormFieldList
{

    protected $type = 'verifyappid';

    public function getInput()
    {
        F0FModel::addIncludePath(JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/models');
        $model = F0FModel::getTmpInstance('AppRetainfulCoupons', 'J2StoreModel')->getClone();
        $plugin = $model->getPlugin();
        $plugin_params = $model->getPluginParams();
        $url = "index.php?option=com_j2store&view=app&task=view&layout=view&id=" . $plugin->extension_id . "&appTask=checkAppID&tmpl=component";
        ?>
        <button class="btn btn-small button-apply btn-success" id="validate_app_key_btn"
                onclick="validateAppConnection()"
                type="button"><?php echo (!$plugin_params->get('is_retainful_connected', 0)) ? JText::_('J2STORE_VALIDATE_APP_CONNECTION') : JText::_('J2STORE_RE_VALIDATE_APP_CONNECTION'); ?></button>
        <br>
        <div id="app_id_error"><span
                    class="text-success"><?php echo ($plugin_params->get('is_retainful_connected', 0)) ? JText::_('J2STORE_NEXTORDER_APP_ID_CONNECTED_SUCCESSFULLY') : ''; ?></span>
        </div>
        <script>
            function validateAppConnection() {
                (function ($) {
                    $('#app_id_error').html('');
                    $.ajax({
                        url: '<?php echo $url;?>',
                        type: 'post',
                        cache: false,
                        data: {
                            app_id: $('#params_retainful_app_id').val()
                        },
                        dataType: 'json',
                        success: function (json) {
                            if (json['error']) {
                                $('#app_id_error').html('<span class="j2error">' + json['error'] + '</span>');
                                $("#params_is_retainful_connected").val(0);
                                $('#params_retainful_app_id').focus();
                                $('#params_retainful_app_id').val('');
                            }
                            if (json['success']) {
                                $('#app_id_error').html('<span class="text-success">' + json['success'] + '</span>');
                                $("#params_is_retainful_connected").val(1);
                                //window.location = json['redirect'];
                            }
                        }
                    });
                })(jQuery);
            }
        </script>
        <?php
    }
}
