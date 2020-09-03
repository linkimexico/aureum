<?php
/*------------------------------------------------------------------------
# mod_j2store_search - J2Store Search
# ------------------------------------------------------------------------
# author    Gokila Priya - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$url = JRoute::_('index.php?option=com_j2store&view=products&task=browse&Itemid='.$mitemid);
?>
<div id="j2store-search-<?php echo $module->id;?>" class="j2store-search-product  <?php echo $moduleclass_sfx;?>" >
	<form action="<?php echo $url;?>" method="" class="form-horizontal" name="j2storeserachForm" id="j2storeserachForm_<?php echo $module->id;?>" >

		<?php if ($params->get('show_label', 1) == 1): ?>
			<label for="mod_j2store_search"><?php echo JText::_('J2STORE_FILTER_SEARCH'); ?></label>
		<?php endif; ?>
		<input type="text" class="inputbox" name="search" id="mod_j2store_search-<?php echo $module->id; ;?>" value="<?php echo $search;?>"  placeholder="<?php echo JText::_('J2STORE_FILTER_SEARCH'); ?>"/>
		<a class="btn btn-primary" onclick="jQuery('#j2storeserachForm_<?php echo $module->id;?>').submit();">
			<i  class="icon-search"></i>
		</a>
		<?php foreach ($categoryList as $key=>$cat):?>
			<input type="hidden" name="catid[<?php echo $key;?>]" value="<?php echo $cat;?>" />
		<?php endforeach;?>


	</form>
</div>