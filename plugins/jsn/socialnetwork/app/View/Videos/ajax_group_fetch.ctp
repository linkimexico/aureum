<script>
function deleteVideo()
{
	jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post( '<?php echo $this->request->base?>/videos/ajax_delete/<?php echo $video['Video']['id']?>', function(data){ 
				loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_browse/group/<?php echo $this->request->data['group_id']?>');
				if ( jQuery("#videos_count").html() != '0' )
					jQuery("#videos_count").html( parseInt(jQuery("#videos_count").html()) - 1 );
			});	
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this video?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}
</script>

<h2 style="margin-top: 0px;"><?php echo __('Video Details')?></h2>
<div class="error-message" style="display:none"></div>

<?php if ( !empty( $video['Video']['id'] ) ): ?>
<form id="createForm">
<?php endif; ?>

<ul class="list6 list6sm2">
	<?php echo $this->Form->hidden('id', array('value' => $video['Video']['id'])); ?>
	<?php echo $this->Form->hidden('source_id', array('value' => $video['Video']['source_id'])); ?>
	<?php echo $this->Form->hidden('thumb_url', array('value' => $video['Video']['thumb'])); ?>
	<?php echo $this->Form->hidden('privacy', array('value' => PRIVACY_EVERYONE)); ?>
	
	<li><label><?php echo __('Video Title')?></label>
		<?php echo $this->Form->text('title', array('value' => $video['Video']['title'])); ?>
	</li>
	<li><label><?php echo __('Description')?></label>
		<?php echo $this->Form->textarea('description', array('value' => $video['Video']['description'])); ?>
	</li>
	<li><label>&nbsp;</label>
		<a href="javascript:void(0)" class="button button-action" onclick="ajaxCreateItem('videos')"><i class="icon-save"></i> <?php echo __('Save')?></a>
		<?php if ( !empty($video['Video']['id']) ): ?>
		<a href="javascript:void(0)" class="button" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>')"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
		<a href="javascript:void(0)" class="button button-caution" onclick="deleteVideo()"><i class="icon-trash"></i> <?php echo __('Delete')?></a>
		<?php else: ?>
		<a href="javascript:void(0)" class="button" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_browse/group/<?php echo $this->request->data['group_id']?>')"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
		<?php endif; ?>
	</li>
</ul>

<?php if ( !empty( $video['Video']['id'] ) ): ?>
</form>
<?php endif; ?>