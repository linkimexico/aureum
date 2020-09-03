<?php
$link = unserialize($activity['Activity']['params']);
?>
<div class="comment_message">
    <a href="<?php echo $activity['Activity']['content']?>" target="_blank" rel="nofollow"><?php echo h($this->Text->truncate($activity['Activity']['content'], 80))?></a>
    <?php if ( !empty( $link['image'] ) ): ?>
    <img src="<?php echo $this->request->webroot?>uploads/links/<?php echo $link['image']?>" class="img_wrapper2" style="margin-right:10px;width:100px;padding:2px">
    <?php endif; ?>
    <div class="<?php if ( empty( $link['image'] ) ): ?>summary<?php endif; ?> date">
        <a href="<?php echo $activity['Activity']['content']?>" target="_blank" rel="nofollow"><strong><?php echo h($link['title'])?></strong></a><br />
        <?php
        if ( !empty( $link['description'] ) )
            echo h($this->Text->truncate($link['description'], 150));
        ?>
    </div>
</div>