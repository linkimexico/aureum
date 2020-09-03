<script>
jQuery(document).ready(function(){
   jQuery('#select_photos').change(function(){
      if ( jQuery(this).val() == 'move' )
         jQuery('#album_id').show();
      else
         jQuery('#album_id').hide();
   });
});
</script>

<form action="<?php echo $this->request->base?>/albums/edit/<?php echo $album['Album']['id']?>" method="post">
<?php echo $this->Form->hidden('id', array('value' => $album['Album']['id'])); ?>
<div class="box3">	
    <a href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>" class="button button-action topButton"><?php echo __('View Album')?></a>
	<h1><?php echo h($album['Album']['title'])?></h1>	
	
	<?php
	if (count($photos) == 0):
		echo __('No photos found');
	else:
	?>
	<ul class="list1 photos_edit">
		<?php foreach ($photos as $photo): ?>
		<li>
		    <div style="background-image: url(<?php echo $this->request->webroot?><?php echo $photo['Photo']['thumb']?>)"></div>
		    <?php echo $this->Form->textarea('caption_' . $photo['Photo']['id'], array('value' => $photo['Photo']['caption'], 'placeholder' => __('Caption'), 'class' => 'no-grow')) ?><br />
		    <input type="radio" name="cover" value="<?php echo $photo['Photo']['thumb']?>" <?php if ($photo['Photo']['thumb'] == $album['Album']['cover']) echo 'checked'; ?>> <?php echo __('Album cover')?>
		    <input type="checkbox" name="select_<?php echo $photo['Photo']['id']?>" value="1" style="position: absolute;top: 10px;right: 10px;">
		</li>
		<?php endforeach; ?>
	</ul>
	<?php
	endif;
	?>
	
	<div style="float:right">
        <?php echo $this->Form->select('select_photos', array('move' => __('Move to'), 'delete' => __('Delete') ), array( 'empty' => __('With selected...') ) ); ?>
        <?php echo $this->Form->select('album_id', $albums, array( 'style' => 'display:none' ) ); ?>
    </div>
	
	<div align="center" style="margin-top: 30px">
		<input type="submit" value="<?php echo __('Save Changes')?>" class="button button-action">
		<?php if ( empty( $album['Album']['type'] ) ): ?>
		<input type="button" value="<?php echo __('Delete Album')?>" class="button button-caution" onclick="jsnsocialConfirm( '<?php echo __('Are you sure you want to delete this album?<br />All photos will also be deleted!')?>', '<?php echo $this->request->base?>/albums/do_delete/<?php echo $album['Album']['id']?>' )">
		<?php endif; ?>
	</div>
</div>
</form>