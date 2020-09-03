<?php
if ( !empty($uid) && !empty($friend_suggestions) ):
?>
<div class="box2">
    <h3><a href="<?php echo $this->request->base?>/friends/ajax_suggestions" class="overlay" title="<?php echo __('People You May Know')?>"><?php echo __('People You May Know')?></a></h3>
    <div class="box_content">
        <ul class="list6">
        <?php foreach ($friend_suggestions as $friend): ?>
            <li><?php echo $this->Jsnsocial->getUserAvatar($friend['User'], 40)?>
                <div style="margin-left:50px">
                    <?php echo $this->Jsnsocial->getName($friend['User'])?><br />
                    <span class="date"><?php echo __n( '%s mutual friend', '%s mutual friends', $friend[0]['count'], $friend[0]['count'] )?></span><br />               
                    <a href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $friend['User']['id']?>" id="addFriend_<?php echo $friend['User']['id']?>" class="overlay" title="<?php printf( __('Send %s a friend request'), h($friend['User']['name'])  )?>"><?php echo __('Add as friend')?></a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
        <div class="view-all-link"><a href="<?php echo $this->request->base?>/friends/ajax_suggestions" class="overlay button button-tiny" title="<?php echo __('People You May Know')?>"><?php echo __('View all')?></a></div>
    </div>
</div>
<?php
endif;
?>