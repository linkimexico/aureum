<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('formbehavior.chosen','.mod-rsepro-chosen');
$today = JFactory::getDate()->format('Y-m-d');
$cal_attribs = array('size' => '10', 'class' => 'input-small', 'clear' => false); ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php if ($start || $end) { ?>rs_check_dates();<?php } ?>
	
	<?php if ($price && $priceFilter) { ?>
	jQuery('#module-price-field<?php echo $module->id; ?>').slider({
		tooltip_split : true,
		formatter: function(value) {
			var mask = '<?php echo rseventsproHelper::currency(0,true); ?>';
			
			if (value == 0) {
				return '<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE',true); ?>';
			}
			
			return mask.replace('{price}', value);
		}
	});
	<?php } ?>
});
</script>

<div class="rsepro_search_form<?php echo $suffix; ?>">
	<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=search',true,$itemid); ?>">
		<?php if ($categories) { ?>
		<div>
			<label for="rscategories"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_CATEGORIES_LABEL'); ?></label>
			<?php echo $categorieslist; ?>
		</div>
		<?php } ?>
		<?php if ($locations) { ?>
		<div>
			<label for="rslocations"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_LOCATIONS_LABEL'); ?></label>
			<?php echo $locationslist; ?>
		</div>
		<?php } ?>
		<?php if ($start) { ?>
		<div class="rs_date">
			<label for="rsstart"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_START_LABEL'); ?></label>
			<input type="checkbox" class="<?php echo rseventsproHelper::tooltipClass(); ?>" id="enablestart" value="1" title="<?php echo rseventsproHelper::tooltipText(JText::_('MOD_RSEVENTSPRO_SEARCH_START_DATE_INFO')); ?>" onchange="rs_check_dates();" name="enablestart"/> 
			<?php echo JHTML::_('rseventspro.rscalendar', 'rsstart', $today, true, false, null, $cal_attribs); ?>
		</div>
		<?php } ?>
		<?php if ($end) { ?>
		<div class="rs_date">
			<label for="rsend"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_END_LABEL'); ?></label>
			<input type="checkbox" class="<?php echo rseventsproHelper::tooltipClass(); ?>" id="enableend" value="1" title="<?php echo rseventsproHelper::tooltipText(JText::_('MOD_RSEVENTSPRO_SEARCH_END_DATE_INFO')); ?>" onchange="rs_check_dates();" name="enableend"/>
			<?php echo JHTML::_('rseventspro.rscalendar', 'rsend', $today, true, false, null, $cal_attribs); ?>
		</div>
		<?php } ?>
		<?php if ($archive) { ?>
		<div>
			<label for="rsarchive"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_ARCHIVE_LABEL'); ?></label>
			<?php echo $archivelist; ?>
		</div>
		<?php } ?>
		
		<?php if ($price && $priceFilter) { ?>
		<div>
			<label for="rsepro-module-price<?php echo $module->id; ?>" class="rsepro-module-price-label">
				<span id="rsepro-price-show"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_SHOW_PRICE_LABEL'); ?></span>
				<span id="rsepro-price-hide" style="display:none;"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_HIDE_PRICE_LABEL'); ?></span>
			</label>
			<input type="checkbox" style="display:none;" id="rsepro-module-price<?php echo $module->id; ?>" value="1" onchange="rs_check_price(this);" name="enableprice" />
			<div class="rsepro-module-price" style="display:none;">
				<input id="module-price-field<?php echo $module->id; ?>" name="rsprice" type="text" value="0" data-slider-min="0" data-slider-max="<?php echo $maxPrice; ?>" data-slider-step="1" data-slider-value="[0,<?php echo $maxPrice; ?>]" />
			</div>
		</div>
		<?php } ?>
		
		<div>
			<label for="rskeyword"><?php echo JText::_('MOD_RSEVENTSPRO_SEARCH_KEYWORDS_LABEL'); ?></label>			
			<div class="input-append">
				<input type="text" name="rskeyword" id="rse_keyword" value="" autocomplete="off" class="input-small" />
				<button type="submit" class="btn">
					<i class="icon-search"></i>
				</button>
			</div>
		</div>
		<input type="hidden" name="option" value="com_rseventspro" />
		<input type="hidden" name="layout" value="search" />
		<input type="hidden" name="repeat" value="<?php echo $params->get('repeat',1); ?>" />
	</form>
</div>