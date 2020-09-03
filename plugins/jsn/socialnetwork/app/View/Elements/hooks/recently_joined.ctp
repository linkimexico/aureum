<?php if ( !empty( $new_users ) ): ?> 
<div class="box2">
    <h3><?php echo __('Recently Joined')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/users_block', array( 'users' => $new_users ));?>
    </div>
</div>
<?php endif; ?>