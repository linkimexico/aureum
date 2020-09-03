<strong><?php echo $topic['Topic']['title']?></strong>
<div class="comment_message" style="margin:5px 0">
    <?php echo $this->Jsnsocial->cleanHtml( $topic['Topic']['body'] )?>
    
    <?php if ( !empty( $pictures ) ): ?>
    <div style="margin:10px 0">
        <div class="date"><?php echo __('Attached Images')?></div>
        <ul class="list4 p_photos">
        <?php foreach ($pictures as $p): ?>     
            <li style="background-image:url(<?php echo $this->request->webroot?>uploads/attachments/t_<?php echo $p['Attachment']['filename']?>)"><a href="<?php echo $this->request->webroot?>uploads/attachments/<?php echo $p['Attachment']['filename']?>" class="attached-image"></a></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <?php if ( !empty( $files ) ): ?>
    <div style="margin:10px 0">
        <div class="date"><?php echo __('Attached Files')?></div>
        <ul class="list6 list6sm">
        <?php foreach ($files as $attachment): ?>     
            <li><i class="icon-paperclip icon-small"></i><a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>"><?php echo $attachment['Attachment']['original_filename']?></a> <span class="date">(<?php echo __n( '%s download', '%s downloads', $attachment['Attachment']['downloads'], $attachment['Attachment']['downloads'] )?>)</span></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<div class="date"><?php echo __('Posted by %s', $this->Jsnsocial->getName($topic['User']))?> <?php echo $this->Jsnsocial->getTime($topic['Topic']['created'], $jsnsocial_setting['date_format'], $utz)?></div><br />

<div class="bottom_options likes">
    <?php if ($uid == $topic['Topic']['user_id'] || !empty($cuser['Role']['is_admin']) || in_array($uid, $admins) ): ?>
	<span class="button-dropdown topButton" data-buttons="dropdown">
        <a href="#" class="button button-tiny"><?php echo __('Actions')?> <i class="icon-caret-down"></i></a>
        <ul>
            <?php if ($uid == $topic['Topic']['user_id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || in_array($uid, $admins) ): ?>
            <li><a href='javascript:void(0)' onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_group_create/<?php echo $topic['Topic']['id']?>')"><?php echo __('Edit Topic')?></a></li>
            <?php endif; ?>
            <?php if (!empty($cuser['Role']['is_admin']) || in_array($uid, $admins) ): ?>
                <?php if ( !$topic['Topic']['pinned'] ): ?>
                <li><a href="<?php echo $this->request->base?>/topics/do_pin/<?php echo $topic['Topic']['id']?>"><?php echo __('Pin Topic')?></a></li>
                <?php else: ?>
                <li><a href="<?php echo $this->request->base?>/topics/do_unpin/<?php echo $topic['Topic']['id']?>"><?php echo __('Unpin Topic')?></a></li>
                <?php endif; ?>
                
                <?php if ( !$topic['Topic']['locked'] ): ?>
                <li><a href="<?php echo $this->request->base?>/topics/do_lock/<?php echo $topic['Topic']['id']?>"><?php echo __('Lock Topic')?></a></li>
                <?php else: ?>
                <li><a href="<?php echo $this->request->base?>/topics/do_unlock/<?php echo $topic['Topic']['id']?>"><?php echo __('Unlock Topic')?></a></li>
                <?php endif; ?>     
            <?php endif; ?>
        </ul>   
    </span>   
    <?php endif; ?>  
    <?php echo $this->element('likes', array('item' => $topic['Topic'], 'type' => APP_TOPIC, 'hide_container' => true)); ?>
</div>

<h2><?php echo __('Replies (%s)', $topic['Topic']['comment_count'])?></h2>
<ul class="list6 comment_wrapper" id="comments">
<?php echo $this->element('comments');?>
</ul>

<?php 
if ( !isset( $is_member ) || $is_member  )
    if ( $topic['Topic']['locked'] )
        echo '<i class="icon icon-lock icon-small"></i> ' . __('This topic has been locked');
    else
	   echo $this->element( 'comment_form', array( 'target_id' => $topic['Topic']['id'], 'type' => APP_TOPIC ) ); 
else
	echo __('This a group topic. Only group members can leave comment');		
?>