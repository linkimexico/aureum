<?php
if ( empty( $hide_container ) ):
?>
<div class="box2">
    <h3><a href="<?php echo $this->request->base?>/likes/ajax_show/<?php echo $type?>/<?php echo $item['id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><span id="like_count2"><?php echo $item['like_count']?></span> <?php echo __('Like This')?></a></h3>
    <div class="box_content">
        <div class="likes">
	    <?php echo $this->element( 'blocks/users_block', array( 'users' => $likes ) ); ?>
<?php
endif;
?>
	    <a href="javascript:void(0)" onclick="likeIt('<?php echo $type?>', <?php echo $item['id']?>, 1)" class="button button-tiny <?php if ( !empty($uid) && !empty( $like['Like']['thumb_up'] ) ): ?>active<?php endif; ?>">
	        <i class="icon-thumbs-up"></i>
	        <span id="like_count"><?php echo $item['like_count']?></span>
	    </a>
	    <a href="javascript:void(0)" onclick="likeIt('<?php echo $type?>', <?php echo $item['id']?>, 0)" class="button button-tiny <?php if ( !empty($uid) && isset( $like['Like']['thumb_up'] ) && $like['Like']['thumb_up'] == 0 ): ?>active<?php endif; ?>">
	        <i class="icon-thumbs-down"></i>
	        <span id="dislike_count"><?php echo $item['dislike_count']?></span>
	    </a>
<?php
if ( empty( $hide_container ) ):
?>
	   </div>
    </div>
</div>
<?php
endif;
?>