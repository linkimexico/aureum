<?php
echo $this->Html->script(array('jquery.fineuploader'));
echo $this->Html->css(array( 'fineuploader' ));
?> 

<script>
jQuery(document).ready(function(){
    jQuery('#select-0').fineUploader({
        request: {
            <?php if (!empty($group['Group']['id'])): ?>
            endpoint: "<?php echo $this->request->base?>/upload/group/<?php echo $group['Group']['id']?>"
            <?php else: ?>
            endpoint: "<?php echo $this->request->base?>/upload/group"
            <?php endif; ?>
        },
        text: {
            uploadButton: '<?php echo __('Upload a Picture')?>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            sizeLimit: 10 * 1024 * 1024
        },
        multiple: false
    }).on('complete', function(event, id, fileName, response) {
        jQuery('#photo').val(response.filename);
        jQuery('#item-avatar').attr('src', response.avatar);
    });
});
</script>

<div class="box3">
	<form id="createForm">
	<?php
	if (!empty($group['Group']['id']))
		echo $this->Form->hidden('id', array('value' => $group['Group']['id']));
	
	echo $this->Form->hidden('photo', array('value' => $group['Group']['photo']));
	?>	
	
	<h1><?php if (empty($group['Group']['id'])) echo __('Add New Group'); else echo __('Edit Group');?></h1>	

	<ul class="list6 list6sm2">
		<li><label><?php echo __('Group Name')?></label>
			<?php echo $this->Form->text('name', array('value' => $group['Group']['name'])); ?>
		</li>
		<li><label><?php echo __('Category')?></label>
			<?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $group['Group']['category_id'] ) ); ?>
		</li>
		<li><label><?php echo __('Description')?></label>
			<?php echo $this->Form->textarea('description', array('style' => 'width:430px;height:100px', 'value' => $group['Group']['description'])); ?>
		</li>
		<li><label><?php echo __('Group Type')?></label>
			<?php 
			echo $this->Form->select('type', array( PRIVACY_PUBLIC     => __('Public'), 
													PRIVACY_PRIVATE    => __('Private'),
													PRIVACY_RESTRICTED => __('Restricted')
												), 
											 array( 'value' => $group['Group']['type'], 'empty' => false ) 
									); 
			?>
			<a href="javascript:void(0)" class="tip" title="<?php echo __("Public: anyone can view and join<br />Private: only members can view group's details<br />Restricted: anyone can view but join request has to be accepted by group admins")?>">(?)</a>
		</li>
		<li><label><?php echo __('Photo')?></label>
			<img src="<?php echo $this->Jsnsocial->getItemPicture($group['Group'], 'groups'); ?>" id="item-avatar" class="img_wrapper">
			<div id="select-0" style="margin: 10px 0 0 100px;"></div>
		</li>
		<li><label>&nbsp;</label>		
		    <a href="javascript:void(0)" onclick="createItem('groups')" class="button button-action" id="createButton"><i class="icon-save"></i> <?php echo __('Save')?></a>	
		    <?php if ( !empty( $group['Group']['id'] ) ): ?>
			<a href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>" class="button"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
			<?php endif; ?>
			<?php if ( in_array('group_delete', $uacos) && ( ($group['Group']['user_id'] == $uid ) || ( !empty($cuser['Role']['is_admin']) ) ) ): ?>
            <a href="javascript:void(0)" onclick="jsnsocialConfirm( '<?php echo __('Are you sure you want to remove this group?<br />All group contents will also be deleted!')?>', '<?php echo $this->request->base?>/groups/do_delete/<?php echo $group['Group']['id']?>' )" class="button button-caution"><i class="icon-trash"></i> <?php echo __('Delete')?></a>
			<?php endif; ?>
		</li>
	</ul>
	<div class="error-message" style="display:none;"></div>
	</form>
</div>