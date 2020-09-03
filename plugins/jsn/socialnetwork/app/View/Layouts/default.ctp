<?php
JFactory::getDocument()->setTitle(JFactory::getDocument()->getTitle().' - '.$title_for_layout);
if(!empty($desc_for_layout)) JFactory::getDocument()->setDescription($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $desc_for_layout)), 200));
?>

<!DOCTYPE html>
<html>
<head>
    <meta property="og:image" content="<?php echo FULL_BASE_URL ?><?php if(empty($og)) : echo $this->request->webroot; ?>img/og-image.png<?php else : echo($og); endif; ?>" />
	<script>
        var baseUrl = '<?php echo $this->request->base?>';
        var root = '<?php echo $this->request->webroot?>';
    </script>
	<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->

    <?php
        //echo $this->Html->meta('icon');
        echo $this->Html->css( array( 'all.css', 'main.css' ) );
        echo $this->Html->script( array( 'scripts.js', 'global.js' ) );

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>
</head>
<body class="jsn_social">
<jsnmenu>
<div class="jsn_social">
	<div class="jsn_social_header">
	    <div class="jsn_social_wrapper">
        
	        <?php echo $this->element('userbox'); ?>
	        <?php echo $this->element('main_menu'); ?>
	    </div>  
	</div>
	<?php if(JRequest::getVar('view','profile')=='profile') : ?>
		<?php echo $this->element('cover'); ?>
	<?php endif; ?>
</div>
</jsnmenu>
<jsncontent>
<div class="jsn_social">
	<div class="jsn_social_body">      
		<div class="jsn_social_wrapper">   
		    <?php html_entity_decode( $jsnsocial_setting['header_code'] )?>
		    <?php echo $this->element('hooks', array('position' => 'global_top') ); ?>
		    <div class="jsn_social_content">      
		        <?php //echo $this->element('right_column'); ?>           
		        <?php echo $this->element('left_column'); ?>
		    </div>
		    <?php echo $this->element('hooks', array('position' => 'global_bottom') ); ?>
		    <?php echo $this->element('footer'); ?> 
		</div> 
	</div>
</div>
<?php echo $this->element('sql_dump'); ?>
<?php echo html_entity_decode( $jsnsocial_setting['analytics_code'] )?>

<style>
.qq-hide{display:none}
</style>
<script type="text/template" id="qq-template">
  <div class="qq-uploader-selector qq-uploader">
    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
      <span>Drop files here to upload</span>
    </div>
    <div class="qq-upload-button-selector qq-upload-button button">
      <div>Upload a file</div>
    </div>
    <span class="qq-drop-processing-selector qq-drop-processing">
      <span>Processing dropped files...</span>
      <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
    </span>
    <ul class="qq-upload-list-selector qq-upload-list">
      <li>
        <div class="qq-progress-bar-container-selector">
          <div class="qq-progress-bar-selector qq-progress-bar"></div>
        </div>
        <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
        <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
        <span class="qq-upload-file-selector qq-upload-file"></span>
 
        <span class="qq-upload-size-selector qq-upload-size"></span>
        <a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Cancel</a>
        <a class="qq-upload-retry-selector qq-upload-retry" href="#">Retry</a>
        
        <span class="qq-upload-status-text-selector qq-upload-status-text"></span>
      </li>
    </ul>
</script>
<script>
jQuery(document).ready(function(){
	jQuery('#select-2 .qq-upload-button div').html('<div>&nbsp;<i class="icon icon-picture"></i> <?php /*echo __('Upload a Photo')*/?></div>');
	jQuery('#select-0 .qq-upload-button div').html('<i class="icon icon-picture"></i> <?php echo __('Upload a Picture')?>');
	jQuery('#photos_upload .qq-upload-button div').html('<i class="icon icon-picture"></i> <?php echo __('Select Files')?>');
	jQuery('#attachments_upload .qq-upload-button div').html('<i class="icon icon-paperclip"></i> <?php echo __('Select Files')?>');
});
jQuery(document).ajaxStop(function(){
	jQuery('#select-1 .qq-upload-button div').html('<i class="icon icon-picture"></i> <?php echo __('Upload New Picture')?>');
	jQuery('#photos_upload .qq-upload-button div').html('<i class="icon icon-picture"></i> <?php echo __('Select Files')?>');
});
</script>
</jsncontent>
</body>
</html>