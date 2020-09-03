<div id="jsnsocialfooter">
    <?php echo html_entity_decode( $jsnsocial_setting['footer_code'] )?><br />
    <?php if ($jsnsocial_setting['show_credit']): ?>
    <span class="date"><?php echo __('Powered by')?> <a href="http://www.easy-profile.com" target="_blank">Easy Profile - Social Network <?php echo $jsnsocial_setting['version']?></a></span>
    <?php endif; ?>
</div>