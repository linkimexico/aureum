<?php echo $this->element('misc/topic_create_script'); ?>

<script>
function deleteTopic()
{
	jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post( '<?php echo $this->request->base?>/topics/ajax_delete/<?php echo $topic['Topic']['id']?>', function(data){ 
                loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_browse/group/<?php echo $this->request->data['group_id']?>');
                
                if ( jQuery("#topics_count").html() != '0' )
                    jQuery("#topics_count").html( parseInt(jQuery("#topics_count").html()) - 1 );
            });     
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: '<?php echo addslashes(__('Are you sure you want to remove this topic?'))?>',
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}
</script>

<style>
.list6 .mce-tinymce { margin-left: 0; }
.attach_remove {display:none;}
#attachments_list li:hover .attach_remove {display:inline-block;}
</style>

<form id="createForm">
<?php
echo $this->Form->hidden( 'attachments', array( 'value' => $attachments_list ) );
echo $this->Form->hidden( 'tags' );

if (!empty($topic['Topic']['id']))
	echo $this->Form->hidden('id', array('value' => $topic['Topic']['id']));

echo $this->Form->hidden('group_id', array('value' => $this->request->data['group_id']));
echo $this->Form->hidden('category_id', array('value' => 0));
?>	

<ul class="list6 list6sm2">
	<li><?php echo __('Topic Title')?></li>
	<li><?php echo $this->Form->text( 'title', array( 'value' => $topic['Topic']['title'] ) ); ?></li>
	<li><?php echo __('Topic')?></li>
	<li><?php echo $this->Form->textarea( 'body', array( 'value' => $topic['Topic']['body'], 'id' => 'editor' ) ); ?></li>
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

<div id="images-uploader" style="display:none;margin:10px 0;">
    <div id="attachments_upload"></div>
    <a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files')?></a>
</div>
<a href="javascript:void(0)" onclick="toggleUploader()"><?php echo __('Toggle Attachments Uploader')?></a>

<div style="margin:20px 0">           
    <a href="javascript:void(0)" class="button button-action" id="ajaxCreateButton" onclick="ajaxCreateItem('topics')"><i class="icon-save"></i> <?php echo __('Save')?></a>
    
    <?php if ( !empty( $topic['Topic']['id'] ) ): ?>
    <a href="javascript:void(0)" class="button" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
    
    <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) || ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
    <a href="javascript:void(0)" onclick="deleteTopic()" class="button button-caution"><i class="icon-trash"></i> <?php echo __('Delete')?></a>
    <?php endif; ?> 
    
    <?php else: ?>
    <a href="javascript:void(0)" class="button" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_browse/group/<?php echo $this->request->data['group_id']?>')"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
    <?php endif; ?>     
</div>
<div class="error-message" id="errorMessage" style="display:none"></div>