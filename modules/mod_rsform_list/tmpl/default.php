<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($moduleclass_sfx) { ?>
<div class="<?php echo $moduleclass_sfx; ?>">
<?php } ?>
	<?php echo $html; ?>
<?php if ($moduleclass_sfx) { ?>
</div>
<?php } ?>