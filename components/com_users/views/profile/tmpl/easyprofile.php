<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if(class_exists('FieldsHelper')) FieldsHelper::prepareForm('com_users.user', $this->form, $this->data);

include( JPATH_SITE . '/components/com_users/views/profile/tmpl/edit.php');

$config = JComponentHelper::getParams('com_jsn');
$layout_width=$config->get('layout_width','full');
if( $layout_width == 'full' ) $max_width = 'none';
else $max_width = $config->get('layout_maxwidth','500');
$layout=$config->get('layout_form','horizontal');

$session = JFactory::getSession();
$profile_menuid = $session->get('jsn_profile_item_id_'.(JFactory::getApplication()->input->get('user_id',JFactory::getUser()->id)),false);
if($profile_menuid) $Itemid = '&Itemid='.$profile_menuid;
else $Itemid = '';
?>
<style>
div.profile-edit{max-width:<?php if ( empty($max_width) ) echo 'none'; else  echo $max_width.'px'; ?>;margin:auto;}
div.profile-edit.formfullwidth{max-width:none;}
#profile_tabs{font-weight:bold;}
#member-profile.form-horizontal .control-group.privacy + .control-group .controls > input,
#member-profile.form-horizontal .control-group.privacy + .control-group .controls > .input-prepend input,
#member-profile.form-horizontal .control-group.privacy + .control-group .controls > fieldset.radio,
#member-profile.form-horizontal .control-group.privacy + .control-group .controls > fieldset.checkboxes,
#member-profile.form-horizontal .control-group.privacy + .control-group .controls > textarea{padding-right:45px !important;}
#member-profile .controls > input,#member-profile .controls > textarea,#member-profile .controls > .input-prepend,#member-profile .controls > .input-prepend input{width:100%;box-sizing:border-box;height:auto;}
#member-profile .controls > input[type="file"]{width:auto;}
#member-profile .control-label label{font-weight:bold;}
.jsn_registration_controls{border-top:1px solid #ccc;padding:20px 0 0;margin-top:20px;clear:both;}
</style>
<script>
jQuery(document).ready(function($){
	$('#member-profile > .control-group .controls a').attr('href',"<?php echo JRoute::_('index.php?option=com_jsn&view=profile'.$Itemid.'&id='.JFactory::getApplication()->input->get('user_id',''),false); ?>");
	if( $('div.profile-edit').parent().width() < 800 ) {
		$('div.profile-edit').addClass('formfullwidth');
	}
	<?php if ( $layout == 'horizontal' ) : ?>
	if( $('div.profile-edit').parent().width() < 500 ) {
		$('#member-profile').removeClass('form-horizontal');
	}
	<?php endif; ?>
	<?php if ( $layout == 'vertical' ) : ?>$('#member-profile').removeClass('form-horizontal');<?php endif; ?>
	$(window).resize(function(){
		if( $('div.profile-edit').parent().width() < 800 ) {
			$('div.profile-edit').addClass('formfullwidth');
		}
		else {
			$('div.profile-edit').removeClass('formfullwidth');
		}
		<?php if ( $layout == 'horizontal' ) : ?>
		if( $('div.profile-edit').parent().width() < 500 ) {
			$('#member-profile').removeClass('form-horizontal');
		}
		else {
			$('#member-profile').addClass('form-horizontal');
		}
		<?php endif; ?>
	});
	$('#member-profile a.btn:not([class*="btn-"])').addClass('btn-danger');
	$('#member-profile .control-group > .control-label > label').each(function(){
		$(this).closest('.control-group').addClass($(this).attr('id').replace('jform_','').replace('-lbl','-group'));
		$(this).append('<span style="display:none;"> ('+$(this).closest('fieldset').children('legend').text()+')</span>');
	});
	if($('#system-message-container').length) $('#system-message-container').prependTo("#member-profile");
	else $("#member-profile").prepend('<div id="system-message-container" />');
});
</script>