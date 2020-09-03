<script>
jQuery(document).ready(function(){
	jQuery('#createButton').click(function(){
        checked = false;
        jQuery('#permission_list :checkbox').each(function(){
            if (jQuery(this).is(':checked'))
               checked = true;
        })
        
        if (!checked)
        {
           jQuery(".error-message").show();
           jQuery('.error-message').html('Please check at least one user role in the Permissions tab');
           return;
        }

		disableButton('createButton');
		jQuery.post("<?php echo $this->request->base?>/admin/categories/ajax_save", jQuery("#createForm").serialize(), function(data){
			enableButton('createButton');
			var json = jQuery.parseJSON(data);
            
            if ( json.result == 1 )
                location.reload();
            else
            {
                jQuery(".error-message").show();
                jQuery(".error-message").html(json.message);
            }   
		});
        
		return false;
	});
	
	initTabs('category_view');
});

function toggleField()
{
    jQuery('.opt_field').toggle();
}
</script>

<form id="createForm">
<?php echo $this->Form->hidden('id', array('value' => $category['Category']['id'])); ?>

<div id="category_view">
    <div class="tabs-wrapper">
        <ul class="tabs">
            <li id="category_settings" class="active">Settings</li>
            <li id="category_permissions">Permissions</li>  
        </ul>
    </div>
    
    <div id="category_settings_content" class="tab" style="display:block">  
        <ul class="list6 list6sm2">
        	<li><label>Name</label>
        		<?php echo $this->Form->text('name', array('value' => $category['Category']['name'])); ?>
        	</li>
        	<li><label>Type</label>
        		<?php echo $this->Form->select('type', array( APP_ALBUM => 'Album', 
        		                                              APP_EVENT => 'Event',
        													  APP_GROUP => 'Group', 
        													  APP_TOPIC => 'Topic',
        													  APP_VIDEO => 'Video'
        													  ), 
        											   array('value' => $category['Category']['type'])
        									); 
        		?>
        	</li>
        	<li><label>Header (<a href="javascript:void(0)" class="tip" title="Category header is top level category<br />which does not allow items to be posted">?</a>)</label> 
                <?php echo $this->Form->checkbox('header', array( 'checked' => $category['Category']['header'], 'onclick' => 'toggleField()', 'id' => 'cat_header' )); ?>
            </li>
            <li class="opt_field" <?php if ($category['Category']['header']):?>style="display:none"<?php endif; ?>><label>Parent Category</label>
                <?php echo $this->Form->select('parent_id', $headers, array('value' => $category['Category']['parent_id'], 'empty' => false)); ?>
            </li>
        	<li class="opt_field" <?php if ($category['Category']['header']):?>style="display:none"<?php endif; ?>><label>Description</label>
        		<?php echo $this->Form->textarea('description', array('value' => $category['Category']['description'])); ?>
        	</li>
        	<li><label>Active</label>
        		<?php echo $this->Form->checkbox('active', array( 'checked' => $category['Category']['active'] ) );	?>
        	</li>
        </ul>
    </div>
    
    <div id="category_permissions_content" class="tab">        
        <h2 style="margin-top: 0px">Post Permission</h2>
        <?php echo $this->element('admin/permissions', array('permission' => $category['Category']['create_permission'])); ?>
    </div>
</form>

<div class="regSubmit">
    <a href="#" id="createButton" class="button button-action"><i class="icon-save"></i> Save Category</a>
</div>
    
<div class="error-message" style="display:none;margin-top:10px;"></div>