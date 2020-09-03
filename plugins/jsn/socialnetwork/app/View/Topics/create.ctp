<?php
echo $this->Html->script(array('tinymce/tinymce.min', 'jquery.fineuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));
echo $this->element('misc/topic_create_script');

$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>

<style>
.attach_remove {display:none;}
#attachments_list li:hover .attach_remove {display:inline-block;}
</style>

<div class="box3">
	<form id="createForm">
	<?php
	echo $this->Form->hidden( 'attachments', array( 'value' => $attachments_list ) );
    
	if (!empty($topic['Topic']['id']))
		echo $this->Form->hidden('id', array('value' => $topic['Topic']['id']));
	?>
	<h1><?php if (empty($topic['Topic']['id'])) echo __('Create New Topic'); else echo __('Edit Topic');?></h1>	
	
	<ul class="list6 list6sm2">
		<li><label><?php echo __('Topic Title')?></label><?php echo $this->Form->text( 'title', array( 'value' => $topic['Topic']['title'] ) ); ?></li>
		<li><label><?php echo __('Category')?></label><?php echo $this->Form->select( 'category_id', $cats, array( 'value' => $topic['Topic']['category_id'] ) ); ?></li>
		<li><label><?php echo __('Topic')?></label><?php echo $this->Form->textarea( 'body', array( 'value' => $topic['Topic']['body'], 'id' => 'editor' ) ); ?></li>
		<li><label><?php echo __('Tags')?></label><?php echo $this->Form->text( 'tags', array( 'value' => $tags_value ) ); ?> <a href="javascript:void(0)" class="tip" title="<?php echo __('Separated by commas')?>">(?)</a></li>
		<?php if (!empty($attachments)): ?>
		<li><label><?php echo __('Attachments')?></label>
		    <ul class="list6 list6sm" id="attachments_list" style="overflow: hidden;">
		        <?php foreach ($attachments as $attachment): ?>
		        <li><i class="icon-paperclip icon-small"></i><a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>" target="_blank"><?php echo $attachment['Attachment']['original_filename']?></a>
		          	&nbsp;<a href="#" data-id="<?php echo $attachment['Attachment']['id']?>" class="attach_remove tip" title="<?php echo __('Delete')?>"><i class="icon-trash icon-small"></i></a>	            
		        </li>
		        <?php endforeach; ?>
		    </ul>
		</li>
		<?php endif; ?>
	</ul>
	</form>
	
	<div style="margin-left:100px">
		<div id="images-uploader" style="display:none;margin:10px 0;">
            <div id="attachments_upload"></div>
            <a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files')?></a>
        </div>
		<a href="javascript:void(0)" onclick="toggleUploader()"><?php echo __('Toggle Attachments Uploader')?></a>

		<div style="margin:20px 0">           
            <a href="#" class="button button-action" id="createButton"><i class="icon-save"></i> <?php echo __('Save')?></a>
            <?php if ( !empty( $topic['Topic']['id'] ) ): ?>
            <a href="<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>" class="button"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
            <?php endif; ?>
            <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty( $topic['Topic']['id'] ) && $cuser['Role']['is_admin'] ) ): ?>
            <a href="javascript:void(0)" onclick="jsnsocialConfirm( '<?php echo __('Are you sure you want to remove this topic?')?>', '<?php echo $this->request->base?>/topics/do_delete/<?php echo $topic['Topic']['id']?>' )" class="button button-caution"><i class="icon-trash"></i> <?php echo __('Delete')?></a>
            <?php endif; ?> 
        </div>
        <div class="error-message" id="errorMessage" style="display:none"></div>
	</div>
</div>