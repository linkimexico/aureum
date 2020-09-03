<?php
if ( !( empty($uid) && $jsnsocial_setting['force_login'] ) ):
?>
<div class="box2">
    <h3><a href="<?php echo $this->request->base?>/users/index/online:1"><?php echo __("Who's Online")?> (<?php echo $online['total']?>)</a></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/users_block', array('users' => $online['members'])); ?>
        <?php 
            printf( __('There are currently %s and %s online'), __n( '%s member', '%s members', count($online['userids']), count($online['userids']) ), __n( '%s guest', '%s guests', $online['guests'], $online['guests'] ) );    
        ?>
    </div>
</div>
<?php
endif;
?>