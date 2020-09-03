<?php
$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>

<?php if ( !empty( $video['Video']['id'] ) ): ?>
<form id="createForm">
<?php endif; ?>

<ul class="list6 list6sm2">
	<?php echo $this->Form->hidden('id', array('value' => $video['Video']['id'])); ?>
	<?php echo $this->Form->hidden('source_id', array('value' => $video['Video']['source_id'])); ?>
	<?php echo $this->Form->hidden('thumb_url', array('value' => $video['Video']['thumb'])); ?>
	
	<li><label><?php echo __('Video Title')?></label>
		<?php echo $this->Form->text('title', array('value' => $video['Video']['title'])); ?>
	</li>
	<li><label><?php echo __('Category')?></label>
		<?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $video['Video']['category_id'] ) ); ?>
	</li>
	<li><label><?php echo __('Description')?></label>
		<?php echo $this->Form->textarea('description', array('value' => $video['Video']['description'])); ?>
	</li>
	<li><label><?php echo __('Tags')?></label>
		<?php echo $this->Form->text('tags', array('value' => $tags_value)); ?> <a href="javascript:void(0)" class="tip" title="<?php echo __('Separated by commas')?>">(?)</a>
	</li>
	<li><label><?php echo __('Privacy')?></label>
		<?php 
		echo $this->Form->select( 'privacy', 
								  array( PRIVACY_EVERYONE => __('Everyone'), 
								  		 PRIVACY_FRIENDS  => __('Friends Only'), 
										 PRIVACY_ME 	  => __('Me Only')
										), 
								  array( 'value' => $video['Video']['privacy'],
								  		 'empty' => false
										)
								); 
		?>
	</li>
	<li><label>&nbsp;</label>
	    <a href="javascript:void(0)" class="button button-action" onclick="createItem('videos')" id="createButton"><i class="icon-save"></i> <?php echo __('Save Video')?></a>
	    <?php if ( !empty( $video['Video']['id'] ) ): ?>
        <a href="javascript:void(0)" onclick="if (confirm( '<?php echo addslashes(__('Are you sure you want to remove this video?'))?>')) window.location='<?php echo $this->request->base?>/videos/do_delete/<?php echo $video['Video']['id']?>';" class="button button-caution"><i class="icon-trash"></i> <?php echo __('Delete Video')?></a>
        <?php endif; ?> 
	</li>
</ul>

<?php if ( !empty( $video['Video']['id'] ) ): ?>
</form>
<?php endif; ?>

<div class="error-message" style="display:none;margin-top:10px;"></div>