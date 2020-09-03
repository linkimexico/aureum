<?php
echo $this->Html->script(array('tinymce/tinymce.min'), array('inline' => false));
echo $this->element('admin/adminnav', array("cmenu" => "pages"));
?>

<script>
tinymce.init({
    selector: "textarea",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor"
    ],
    toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
    image_advtab: true,
    height: 500,
    relative_urls : false,
    remove_script_host : true,
    document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>'
});

jQuery(document).ready(function(){
	jQuery('#createButton').click(function(){
	    var checked = false;
        jQuery('#permission_list :checkbox').each(function(){
            if (jQuery(this).is(':checked'))
               checked = true;
        })
        
        if (!checked)
        {
           jsnsocialAlert('Please check at least one user role in the Permissions tab');
           return;
        }
        
	    jQuery('#page-body').val(tinyMCE.activeEditor.getContent());
        disableButton('createButton');
        jQuery.post("<?php echo $this->request->base?>/admin/pages/ajax_save", jQuery("#createForm").serialize(), function(data){
            enableButton('createButton');
            var json = jQuery.parseJSON(data);
            
            if ( json.result == 1 )
            	window.location = '<?php echo $this->request->base?>/admin/pages/create/' + json.page_id;
            else
                jsnsocialAlert(json.message);
        });
    });
    
    jQuery('#alias').on('blur', function(){
        jQuery('#alias').val( jQuery('#alias').val().replace(/[^a-zA-Z0-9-_]/g, '_').toLowerCase() );
    });
});
</script>

<style>
.list6 .mce-tinymce {
    margin-left: 0px;
}
</style>

<div id="center">
	<form id="createForm">
	<?php echo $this->Form->hidden('id', array('value' => $page['Page']['id'])); ?>
	<?php if (!empty($page['Page']['id'])): ?>
	<a href="<?php echo $this->request->base?>/pages/<?php echo $page['Page']['alias']?>" target="_blank" class="button topButton"><i class="icon-file-alt"></i> View Page</a>
	<?php endif; ?>
	<h1><?php if (empty($page['Page']['id'])) echo 'Create New Page'; else echo 'Edit Page';?></h1>	
	<ul class="list6">
		<li><label>Page Title</label><?php echo $this->Form->text('title', array('value' => $page['Page']['title'])); ?></li>
		<li><label>Page Alias</label><?php echo $this->Form->text('alias', array('value' => $page['Page']['alias'])); ?> (<a href="#" class="tip" title="The page url will be /pages/your-alias">?</a>)</li>
		<li><label style="float:none;margin-bottom:10px;">Page Content</label><?php echo $this->Form->textarea('content', array('value' => $page['Page']['content'], 'id' => 'page-body')); ?></li>
	</ul>
	
	<h2>Settings</h2>
	<ul class="list6">
		<li><label>Allow Comments</label><?php echo $this->Form->checkbox('comments', array('checked' => $params['comments'])); ?></li>
		<li><label>Show in Main Menu</label><?php echo $this->Form->checkbox('menu', array('checked' => $page['Page']['menu'])); ?></li>
		<li><label>Icon Class</label><?php echo $this->Form->text('icon_class', array('value' => $page['Page']['icon_class'])); ?> (<a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank" class="tip" title="Enter class of Font Awesome icon to use. Click to visit the site<br />You can use your own icons by overriding the css in your theme stylesheet">?</a>)</li>
	</ul>
	
	<h2>Permissions</h2>
	<?php echo $this->element('admin/permissions', array('permission' => $page['Page']['permission'])); ?>
	</form>
	<div style="margin-top:20px;text-align: center;">
		<button id="createButton" class="button button-action button-medium"><i class="icon-save"></i> Save Page</button>
	</div>	
</div>
