<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include( JPATH_SITE . '/components/com_users/views/registration/tmpl/default.php');

$config = JComponentHelper::getParams('com_jsn');
$layout_width=$config->get('layout_width','full');
if( $layout_width == 'full' ) $max_width = 'none';
else $max_width = $config->get('layout_maxwidth','500');
$layout=$config->get('layout_form','horizontal');

?>
<style>
div.registration{max-width:<?php if ( empty($max_width) ) echo 'none'; else  echo $max_width.'px'; ?>;margin:auto;}
div.registration.formfullwidth{max-width:none;}
#profile_tabs{font-weight:bold;}
#member-registration.form-horizontal .control-group.privacy + .control-group .controls > input,
#member-registration.form-horizontal .control-group.privacy + .control-group .controls > .input-prepend input,
#member-registration.form-horizontal .control-group.privacy + .control-group .controls > fieldset.radio,
#member-registration.form-horizontal .control-group.privacy + .control-group .controls > fieldset.checkboxes,
#member-registration.form-horizontal .control-group.privacy + .control-group .controls > textarea{padding-right:45px !important;}
#member-registration .controls > input,#member-registration .controls > textarea,#member-registration .controls > .input-prepend,#member-registration .controls > .input-prepend input{width:100%;box-sizing:border-box;height:auto;}
#member-registration .controls > input[type="file"]{width:auto;}
#member-registration .control-label label{font-weight:bold;}
.jsn_registration_controls{border-top:1px solid #ccc;padding:20px 0 0;margin-top:20px;clear:both;}
</style>
<script>
jQuery(document).ready(function($){
	if( $('div.registration').parent().width() < 800 ) {
		$('div.registration').addClass('formfullwidth');
	}
	<?php if ( $layout == 'horizontal' ) : ?>
	if( $('div.registration').parent().width() < 500 ) {
		$('#member-registration').removeClass('form-horizontal');
	}
	<?php endif; ?>
	<?php if ( $layout == 'vertical' ) : ?>$('#member-registration').removeClass('form-horizontal');<?php endif; ?>
	$(window).resize(function(){
		if( $('div.registration').parent().width() < 800 ) {
			$('div.registration').addClass('formfullwidth');
		}
		else {
			$('div.registration').removeClass('formfullwidth');
		}
		<?php if ( $layout == 'horizontal' ) : ?>
		if( $('div.registration').parent().width() < 500 ) {
			$('#member-registration').removeClass('form-horizontal');
		}
		else {
			$('#member-registration').addClass('form-horizontal');
		}
		<?php endif; ?>
	});
	$('#member-registration a.btn:not([class*="btn-"])').addClass('btn-danger');
	$('#member-registration .control-group > .control-label > label').each(function(){
		$(this).closest('.control-group').addClass($(this).attr('id').replace('jform_','').replace('-lbl','-group'));
		$(this).append('<span style="display:none;"> ('+$(this).closest('fieldset').children('legend').text()+')</span>');
	});
	if($('#system-message-container').length) $('#system-message-container').prependTo("#member-registration");
	else $("#member-registration").prepend('<div id="system-message-container" />');
});
</script>