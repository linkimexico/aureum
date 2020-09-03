<?php if ( !empty( $featured_users ) ): ?> 
<div class="box2">
    <h3><?php echo __('Featured Members')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/users_block', array( 'users' => $featured_users ) );?>
    </div>
</div>
<?php endif; ?>