<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="rsepro_search_ajax<?php echo $suffix; ?>">
	<input type="text" name="rsepro_ajax" value="" placeholder="<?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_PLACEHOLDER'); ?>" autocomplete="off" id="rsepro_ajax" />
	<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro_ajax_loader', 'style' => 'display: none;'), true); ?> 
	<div class="rsepro_ajax_container">
		<ul class="rsepro_ajax_list" id="rsepro_ajax_list"></ul>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('#rsepro_ajax_list').css('height','auto');
	
	jQuery('#rsepro_ajax').on('keydown', function() {
		clearTimeout(this.timeout);
		if (jQuery(this).val().length == 0) {
			clearTimeout(this.timeout);
			jQuery('#rsepro_ajax_list').slideUp();
			return true;
		}
		this.timeout = setTimeout("rsepro_search('<?php echo JURI::root(); ?>',<?php echo $itemid; ?>,<?php echo $links; ?>);", 1000);
	});
	
	jQuery('#rsepro_ajax').on('blur', function() {
		if (jQuery(this).val() == '') {
			clearTimeout(this.timeout);
			jQuery('#rsepro_ajax_list').slideUp();
		}
	});
	
});
</script>