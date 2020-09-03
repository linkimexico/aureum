<?php if ( !isset( $this->request->params['admin'] ) && empty($no_right_column) ): ?>
<div id="right">	
	<?php echo $this->element('hooks', array('position' => 'global_sidebar') ); ?>
	
	<?php echo html_entity_decode( $jsnsocial_setting['sidebar_code'] )?>
	
	<br />
	<?php
    if ( $this->request->is('mobile') ):
    ?>
    <a href="javascript:void(0)" onclick="viewMobileSite()"><?php echo __('Mobile Site')?></a><br />
    <?php endif; ?>
    
	<?php if ( $jsnsocial_setting['select_language'] ): ?>
    <?php echo __('Language')?>: <a href="<?php echo $this->request->base?>/home/ajax_lang" class="overlay" title="<?php echo __('Language')?>"><?php echo (!empty($site_langs[Configure::read('Config.language')])) ? $site_langs[Configure::read('Config.language')] : __('Change')?></a><br />
    <?php endif; ?>
    
    <?php if ( $jsnsocial_setting['select_theme'] ): ?>
    <?php echo __('Theme')?>: <a href="<?php echo $this->request->base?>/home/ajax_theme" class="overlay" title="<?php echo __('Theme')?>"><?php echo (!empty($site_themes[$this->theme])) ? $site_themes[$this->theme] : __('Change')?></a>
    <?php endif; ?>
</div>
<?php endif; ?>