<?php
echo $this->Html->css(array('pickadate', 'fineuploader'), null, array('inline' => false));
echo $this->Html->script(array('pickadate/picker', 'pickadate/picker.date', 'pickadate/picker.time', 'pickadate/legacy', 'jquery.fineuploader','pickadate/translations/'.Configure::read('Config.language')), array('inline' => false));
?>

<script>
jQuery(document).ready(function(){
    jQuery('#select-0').fineUploader({
        request: {
            <?php if (!empty($event['Event']['id'])): ?>
            endpoint: "<?php echo $this->request->base?>/upload/event/<?php echo $event['Event']['id']?>"
            <?php else: ?>
            endpoint: "<?php echo $this->request->base?>/upload/event"
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
    
    jQuery(".datepicker").pickadate({
		firstDay: <?php if(JText::_('COM_JSN_STARTMONDAY')=='1') echo 'true' ;else echo 'false'; ?>,
		format: 'yyyy-mm-dd',
        onClose: function() {
            if ( jQuery('#to').val() != '' && (jQuery('#from').val() > jQuery('#to').val()) )
            {
                jsnsocialAlert('<?php echo addslashes(__('To date must be greater than From date'))?>');
                jQuery('#to').val('');
            }
        }
    });
    
    jQuery(".timepicker").pickatime({
        format: '<?php echo ($jsnsocial_setting['time_format'] == '24h') ? 'H:i' : 'h:i A'?>'
    });
});

</script>

<form id="createForm">
<?php
if (!empty($event['Event']['id']))
	echo $this->Form->hidden('id', array('value' => $event['Event']['id']));

echo $this->Form->hidden('photo', array('value' => $event['Event']['photo']));
?>	

<div class="box3">	
	
	<h1><?php if (empty($event['Event']['id'])) echo __('Add New Event'); else echo __('Edit Event');?></h1>

	<ul class="list6 list6sm2">
		<li><label><?php echo __('Event Title')?></label>
			<?php echo $this->Form->text('title', array('value' => $event['Event']['title'])); ?>
		</li>
		<li><label><?php echo __('Category')?></label>
            <?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $event['Event']['category_id'] ) ); ?>
        </li>
		<li><label><?php echo __('Location')?></label>
			<?php echo $this->Form->text('location', array('value' => $event['Event']['location'])); ?>
			<a href="javascript:void(0)" class="tip" title="<?php echo __('e.g. Aluminum Hall, Carleton University')?>">(?)</a>
		</li>
		<li><label><?php echo __('Address')?></label>
			<?php echo $this->Form->text('address', array('value' => $event['Event']['address'])); ?>
			<a href="javascript:void(0)" class="tip" title="<?php echo __('Enter the full address (including city, state, country) of the location.<br />This will render a Google map on your event page (optional)')?>">(?)</a>
		</li>
		<li><label><?php echo __('From')?></label>
			<?php 
			echo $this->Form->text('from', array('class' => 'datepicker', 'value' => $event['Event']['from']));
			echo $this->Form->text('from_time', array('value' => $event['Event']['from_time'], 'class' => 'timepicker')); 
			?>
		</li>
		<li><label><?php echo __('To')?></label>
			<?php 
			echo $this->Form->text('to', array('class' => 'datepicker', 'value' => $event['Event']['to']));				
			echo $this->Form->text('to_time', array('value' => $event['Event']['to_time'], 'class' => 'timepicker')); 
			?> 
		</li>

		<li><label><?php echo __('Information')?></label>
			<?php echo $this->Form->textarea('description', array('style' => 'width:430px;height:100px', 'value' => $event['Event']['description'])); ?>
		</li>

		<li><label><?php echo __('Event Type')?></label>
			<?php 
			echo $this->Form->select('type', array( PRIVACY_PUBLIC  => __('Public'), 
													PRIVACY_PRIVATE => __('Private')
												), 
											 array( 'value' => $event['Event']['type'], 'empty' => false ) 
									); 
			?>
			<a href="javascript:void(0)" class="tip" title="<?php echo __('Public: anyone can view and RSVP<br />Private: only invited guests can view and RSVP')?>">(?)</a>
		</li>		
		<li><label><?php echo __('Photo')?></label>
			<img src="<?php echo $this->Jsnsocial->getItemPicture($event['Event'], 'events'); ?>" id="item-avatar" class="img_wrapper">
			<div id="select-0" style="margin: 10px 0 0 100px;"></div>
		</li>		
		<li><label>&nbsp;</label>     
            <a href="javascript:void(0)" onclick="createItem('events')" class="button button-action" id="createButton"><i class="icon-save"></i> <?php echo __('Save')?></a>    
            <?php if ( !empty( $event['Event']['id'] ) ): ?>
            <a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>" class="button"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
            <?php endif; ?>
            <?php if ( ($event['Event']['user_id'] == $uid ) || ( !empty( $event['Event']['id'] ) && !empty($cuser['Role']['is_admin']) ) ): ?>
            <a href="javascript:void(0)" onclick="jsnsocialConfirm( '<?php echo __('Are you sure you want to remove this event?')?>', '<?php echo $this->request->base?>/events/do_delete/<?php echo $event['Event']['id']?>' )" class="button button-caution"><i class="icon-trash"></i> <?php echo __('Delete')?></a>
			<?php endif; ?>	
        </li>
	</ul>
	<div class="error-message" style="display:none;"></div>
</div>
</form>