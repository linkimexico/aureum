<?php
defined('_JEXEC') or die;
F0FModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j2store/models');
$document = JFactory::getDocument();
$url = trim(JUri::root(),'/').'/modules/mod_j2store_discountorder/js/mod_j2store_discountorder.js';
$document->addScript($url);
?>
<div class="j2store-estimator-discount-block-<?php echo $module->id; ?> <?php echo $moduleclass_sfx;?>">
    <?php if(J2Store::isPro()): ?>
        <?php if($params->get('enable_coupon', 0)):?>
            <div class="coupon">
                <form action="<?php echo JRoute::_('index.php'); ?>" id="coupon_form_<?php echo $module->id; ?>" method="post" enctype="multipart/form-data">
                    <?php
                    $coupon = F0FModel::getTmpInstance ( 'Coupons', 'J2StoreModel' )->get_coupon();
                    ?>
                    <input type="text" name="coupon" value="<?php echo $coupon; ?>" />
                    <input type="button" id="coupon_button" value="<?php echo JText::_('J2STORE_APPLY_COUPON')?>" class="button btn btn-primary" onclick="applycoupon('coupon_form_<?php echo $module->id; ?>', '<?php echo addslashes(JText::_("J2STORE_APPLYING_COUPON"));?>')" />
                    <input type="hidden" name="option" value="com_j2store" />
                    <input type="hidden" name="view" value="carts" />
                    <input type="hidden" name="task" value="applyCoupon" />
                </form>
            </div>
        <?php endif; ?>

    <?php endif;?>
    <?php if(J2Store::isPro()): ?>
        <?php if($params->get('enable_voucher', 0)):?>
            <div class="voucher">
                <form action="<?php echo JRoute::_('index.php'); ?>" id="voucher_form_<?php echo $module->id; ?>" method="post" enctype="multipart/form-data">
                    <?php
                    $voucher = F0FModel::getTmpInstance ( 'Vouchers', 'J2StoreModel' )->get_voucher();
                    ?>
                    <input type="text" name="voucher" value="<?php echo $voucher; ?>" />
                    <input type="button" id="voucher_button" value="<?php echo JText::_('J2STORE_APPLY_VOUCHER')?>" class="button btn btn-primary" onclick="applyvoucher('voucher_form_<?php echo $module->id; ?>','<?php echo addslashes(JText::_("J2STORE_APPLYING_VOUCHER"));?>')" />
                    <input type="hidden" name="option" value="com_j2store" />
                    <input type="hidden" name="view" value="carts" />
                    <input type="hidden" name="task" value="applyVoucher" />
                </form>
            </div>
        <?php endif; ?>
    <?php endif;?>
</div>


