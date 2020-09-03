<strong><?php echo h($video['Video']['title'])?></strong><br /><br />
	
<?php
$w = ( $this->request->is('mobile') ) ? 300 : 590;
$h = ( $this->request->is('mobile') ) ? 225 : 465;
    
switch ( $video['Video']['source'] )
{
	case 'youtube':
		echo '<iframe width="' . $w . '" height="' . $h . '" src="http://www.youtube.com/embed/' . $video['Video']['source_id'] . '?wmode=opaque" frameborder="0" allowfullscreen></iframe>';
		break;
		
	case 'vimeo':
		echo '<iframe src="http://player.vimeo.com/video/' . $video['Video']['source_id'] . '" width="' . $w . '" height="' . $h . '" frameborder="0"></iframe>';
		break;
}
?>

<div style="margin:10px 0">
	<?php echo $this->Jsnsocial->formatText( $video['Video']['description'], false, true )?>
</div>
<span class="date"><?php echo __('Posted by %s', $this->Jsnsocial->getName($video['User']))?> <?php echo $this->Jsnsocial->getTime($video['Video']['created'], $jsnsocial_setting['date_format'], $utz)?></span><br />
<div class="likes bottom_options">
	<?php echo $this->element('likes', array('item' => $video['Video'], 'type' => APP_VIDEO, 'hide_container' => true)); ?>    
	<?php if ($uid == $video['Video']['user_id'] || ( !empty($cuser['Role']['is_admin']) ) || in_array($uid, $admins) ): ?>
	<a href='javascript:void(0)' onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_group_create/<?php echo $video['Video']['id']?>')" class="button button-tiny"><i class="icon-pencil"></i> <?php echo __('Edit')?></a>
	<?php endif; ?>
</div>

<h2><?php echo __('Comments (%s)', $comment_count)?></h2>
<ul class="list6 comment_wrapper" id="comments">
<?php echo $this->element('comments');?>
</ul>

<?php 
if ( !isset( $is_member ) || $is_member  )
	echo $this->element( 'comment_form', array( 'target_id' => $video['Video']['id'], 'type' => APP_VIDEO ) ); 
else
	echo __('This a group video. Only group members can leave comment');
?>