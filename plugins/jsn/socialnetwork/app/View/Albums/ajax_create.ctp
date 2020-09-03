<?php
$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>

<form id="createForm">
<?php echo $this->Form->hidden('id', array('value' => $album['Album']['id'])); ?>
<ul class="list6 list6sm2" style="position:relative">
	<li><label><?php echo __('Album Title')?></label>
		<?php echo $this->Form->text('title', array('value' => $album['Album']['title'])); ?>
	</li>
	<li><label><?php echo __('Category')?></label>
		<?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $album['Album']['category_id'] ) ); ?>
	</li>
	<li><label><?php echo __('Description')?></label>
		<?php echo $this->Form->textarea('description', array('value' => $album['Album']['description'])); ?>
	</li>
	<li><label><?php echo __('Tags')?></label>
		<?php echo $this->Form->text('tags', array('value' => $tags_value)); ?> <a href="javascript:void(0)" class="tip" title="<?php echo __('Separated by commas')?>">(?)</a>
	</li>
	<li><label><?php echo __('Privacy')?></label>
		<?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'), 
														 PRIVACY_FRIENDS  => __('Friends Only'), 
														 PRIVACY_ME 	  => __('Me Only') 
												  ), 
												  array( 'value' => $album['Album']['privacy'], 
												  		 'empty' => false
										) ); 
		?>
	</li>
	<li><label>&nbsp;</label>
		<a href="javascript:void(0)" onclick="createItem('albums')" class="button button-action" id="createButton"><i class="icon-save"></i> <?php echo __('Save Album')?></a>
	</li>
</ul>
</form>
<div class="error-message" style="display:none;"></div>