<div id="photo_wrapper">
    
    <div id="tag-wrapper">
        <img src="<?php echo $this->request->webroot?><?php echo $photo['Photo']['path']?>" onload="showPhotoWrapper()" id="photo_src">
        <div id="tag-target"></div>
        <div id="tag-input">
            <?php echo __("Enter person's name")?>
            <input type="text" id="tag-name">
            <?php echo __('Or select a friend')?>
            <div id="friends_list" class="tag_friends_list"></div>
            <a href="#" id="tag-submit" class="button button-action"><?php echo __('Submit')?></a>
            <a href="#" id="tag-cancel" class="button"><?php echo __('Cancel')?></a>
        </div>
        <?php 
        foreach ( $photo_tags as $tag ): 
        ?>
        <div style="<?php echo $tag['PhotoTag']['style']?>" class="hotspot" id="hotspot-0-<?php echo $tag['PhotoTag']['id']?>"><span>
            <?php
            if ( $tag['PhotoTag']['user_id'] )
                echo $this->Jsnsocial->getName( $tag['User'], false );
            else
                echo h($tag['PhotoTag']['value']);
            ?>
        </span></div>
        <?php
        endforeach;
        ?>        
    </div>
    
    <div id="lb_description">
        <?php if ( $photo['Photo']['type'] == APP_GROUP ): ?>
        <a href="<?php echo $this->request->base?>/groups/view/<?php echo $photo['Photo']['target_id']?>/<?php echo seoUrl($photo['Group']['name'])?>"><?php echo __('Photos of %s', $photo['Group']['name'])?></a>
        <?php else: ?>
        <a href="<?php echo $this->request->base?>/albums/view/<?php echo $photo['Photo']['target_id']?>/<?php echo seoUrl($photo['Album']['title'])?>"><?php echo h($photo['Album']['title'])?></a>
        <?php endif; ?> 
        <ul>            
            <?php if ( $can_tag ): ?>
            <li id="tagPhoto"><a href="javascript:void(0)" onclick="tagPhoto()"><i class="icon-tag"></i> <?php echo __('Tag Photo')?></a></li>
            <?php endif; ?>
            <?php if ( !empty( $photo['Photo']['original'] ) ): ?>
            <li><a href="<?php echo $this->request->webroot?><?php echo $photo['Photo']['original']?>" target="_blank"><i class="icon-download-alt"></i> <?php echo __('Download Hi-res')?></a></li>
            <?php endif; ?>
            <?php if ( !empty($uid) ): ?>
            <li><a href="javascript:void(0)" id="photo_like_count" onclick="likePhoto(<?php echo $photo['Photo']['id']?>, 1)" class="<?php if ( !empty( $uid ) && !empty( $like['Like']['thumb_up'] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i> <span id="photo_like_count2"><?php echo $photo['Photo']['like_count']?></span></a></li>
            <li><a href="javascript:void(0)" id="photo_dislike_count" onclick="likePhoto(<?php echo $photo['Photo']['id']?>, 0)" class="<?php if ( !empty( $uid ) && isset( $like['Like']['thumb_up'] ) && $like['Like']['thumb_up'] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i> <span id="photo_dislike_count2"><?php echo $photo['Photo']['dislike_count']?></span></a></li>          
            <?php endif; ?>
        </ul>
    </div>
    
    <?php if ( ( $photo['Photo']['type'] == APP_GROUP ) ):?>
    <a href="<?php echo $this->request->base?>/groups/view/<?php echo $photo['Photo']['target_id']?>" id="photo_close_icon" class="lb_icon"><i class="icon-remove icon-2x topButton"></i></a>
    <?php elseif ( ( $photo['Photo']['type'] == APP_ALBUM ) ): ?>
    <a href="<?php echo $this->request->base?>/albums/view/<?php echo $photo['Photo']['target_id']?>/<?php echo seoUrl($photo['Album']['title'])?>" id="photo_close_icon" class="lb_icon"><i class="icon-remove icon-2x topButton"></i></a>
    <?php endif; ?>
    
    <?php if (!empty($neighbors['next']['Photo']['id'])): ?>
    <a href="javascript:void(0)" onclick="showPhoto(<?php echo $neighbors['next']['Photo']['id']?>)" id="photo_left_arrow" class="lb_icon"><i class="icon-chevron-left icon-4x"></i></a>
    <?php endif; ?>
    
    <?php if (!empty($neighbors['prev']['Photo']['id'])): ?>
    <a href="javascript:void(0)" onclick="showPhoto(<?php echo $neighbors['prev']['Photo']['id']?>)" id="photo_right_arrow" class="lb_icon"><i class="icon-chevron-right icon-4x"></i></a>
    <?php endif; ?>    
  
</div>

<div class="photo_comments">
    <div class="photo_right">
        <?php if ( !empty($photo['Photo']['like_count']) ): ?>
        <a href="<?php echo $this->request->base?>/likes/ajax_show/photo/<?php echo $photo['Photo']['id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><?php echo __n( '%s person likes this', '%s people like this', $photo['Photo']['like_count'], $photo['Photo']['like_count'] )?></a>
        <?php endif; ?>
          
        <div class="comment_message" style="margin:4px 0">
            <?php echo $this->Jsnsocial->formatText( $photo['Photo']['caption'] )?>
        </div>
        <div id="tags" style="margin:5px 0;">
            <span class="date"><?php echo __('In this photo')?>: </span>
            <?php 
            $count = 0;
            foreach ( $photo_tags as $tag ): 
            ?>
            <span onmouseout="hideTag('0-<?php echo $tag['PhotoTag']['id']?>')" onmouseover="showTag('0-<?php echo $tag['PhotoTag']['id']?>')" id="hotspot-item-0-<?php echo $tag['PhotoTag']['id']?>">
                <?php
                if ( $tag['PhotoTag']['user_id'] )
                    echo $this->Jsnsocial->getName( $tag['User'], false );
                else
                    echo h($tag['PhotoTag']['value']);
                
                if ( $uid == $tag['PhotoTag']['tagger_id'] || $uid == $tag['PhotoTag']['user_id'] ):
                ?><a onclick="removeTag('0-<?php echo $tag['PhotoTag']['id']?>', <?php echo $tag['PhotoTag']['id']?>)" href="javascript:void(0)"><i class="icon-remove cross-icon-sm"></i></a>
                <?php
                endif;
                ?>
            </span>
            <?php
                $count++; 
            endforeach; 
            ?>
        </div>
        <span class="date"><?php echo __('Posted by %s', $this->Jsnsocial->getName($photo['User'], false))?> <?php echo $this->Jsnsocial->getTime( $photo['Photo']['created'], $jsnsocial_setting['date_format'], $utz )?></span>
        
        <div class="box4">
            <ul class="list6 list6sm">                
                <?php if ( ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins ) && in_array( $uid, $admins ) ) ): ?>
                <li><a href="javascript:void(0)" onclick="deletePhoto()"><?php echo __('Delete Photo')?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $this->request->base?>/reports/ajax_create/photo/<?php echo $photo['Photo']['id']?>" class="overlay" title="<?php echo __('Report')?>"><?php echo __('Report Photo')?></a></li>
                <li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
            </ul>   
        </div>
    </div>

	<div class="photo_left">
        <h2><?php echo __('Comments')?> (<span id="comment_count"><?php echo $comment_count?></span>)</h2>
        <ul class="list6 comment_wrapper" id="comments">
        <?php echo $this->element('comments');?>
        </ul>
        
        <?php 
        if ( !isset( $is_member ) || $is_member  )
            echo $this->element( 'comment_form', array( 'target_id' => $photo['Photo']['id'], 'type' => APP_PHOTO, 'class' => 'commentForm2' ) ); 
        else
            echo __('This a group photo. Only group members can leave comment');    
        ?>
    </div>
</div>