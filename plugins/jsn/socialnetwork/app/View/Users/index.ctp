<script>
jQuery(document).ready(function()
{
	jQuery(".multi").multiSelect({
        selectAll: false,
        noneSelected: '',
        oneOrMoreSelected: '<?php echo __('% selected')?>'
    });
	jQuery("#filters").keypress(function(e){
    	if ( e.which == 13 ) e.preventDefault();
	});
	jQuery("#searchPeople").click(function(){
		jQuery('#everyone a').spin('tiny');
		jQuery('#browse .current').removeClass('current');
		jQuery('#everyone').addClass('current');

		jQuery.post('<?php echo $this->request->base?>/users/ajax_browse/search', jQuery("#filters").serialize(), function(data){
			jQuery('#everyone a').spin(false);
			jQuery('#list-content').html(data);
			registerOverlay();
		});
	});
	
	<?php if ( !empty( $values ) || !empty($online_filter) ): ?>
	jQuery('#searchPeople').trigger('click');
	<?php endif; ?> 

});

function moreUserSearchResults( url )
{
    jQuery('#list-content .view-more a').spin('small');
    jQuery('#list-content .view-more a').css('color', 'transparent');
    jQuery.post('<?php echo $this->request->base?>' + url, jQuery("#filters").serialize(), function(data){
        jQuery('#list-content .view-more a').spin(false);
        jQuery('#list-content .view-more').remove();
        jQuery("#list-content").append(data);     
        registerOverlay();
    }); 
}
</script>

<div id="leftnav">	
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li class="current" id="everyone"><a data-url="<?php echo $this->request->base?>/users/ajax_browse/all" href="<?php echo $this->request->base?>/users"><?php echo __('Everyone')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/users/ajax_browse/friends" href="#"><?php echo __('My Friends')?></a></li>
		</ul>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'users_sidebar') ); ?>
	
	<?php
	$ids=SocialNetwork::$search_fields;
	if(is_array($ids) && count($ids)>0) :
	?>
	<div class="box2">	    
    	<h3><?php echo __('Search')?></h3>
    	<div class="box_content">
    		<form id="filters">
    		<ul class="list6">
				
				
				<?php
				if(in_array('formatname', $ids)) :
				?>
					<li>
						<label><?php echo JText::_('COM_JSN_NAME'); ?></label>
						<input type="text" placeholder="<?php echo JText::_('COM_JSN_SEARCHFOR').' '.JText::_('COM_JSN_NAME'); ?>..." name="name" value="<?php echo JRequest::getVar('name',''); ?>"/>	
					</li>
				<?php 
				endif;

				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select('a.*')->from('#__jsn_fields AS a')->where('a.level = 2')->where('a.search = 1')->where('a.published = 1')->order($db->escape('a.lft') . ' ASC');
				$db->setQuery( $query );
				$fields = $db->loadObjectList('id');

				foreach (glob(JPATH_ADMINISTRATOR . '/components/com_jsn/helpers/fields/*.php') as $filename) {
					require_once $filename;
				}

				foreach($ids as $id)
				{
					if($id!='formatname' && isset($fields[$id])){
						$registry = new JRegistry;
						$registry->loadString($fields[$id]->params);
						$fields[$id]->params = $registry;

						$class='Jsn'.ucfirst($fields[$id]->type).'FieldHelper';
						
						if($fields[$id]->params->get('titlesearch','')!='') $fields[$id]->title=$fields[$id]->params->get('titlesearch','');
						?>
							<li>
								<label><?php echo JText::_($fields[$id]->title); ?></label>
								
						<?php
						if(class_exists($class)) echo $class::getSearchInput($fields[$id]);
						?>
								<div style="clear:both"></div>
							</li>
						<?php
					}
				}
				?>

				<?php
				if(in_array('status', $ids)) :
				?>
					<li>
						<label>OnLine</label>
						<input type="checkbox" name="status" value="1" <?php if(JRequest::getVar('status','')!='' || !empty($online_filter)) echo "checked=checked"; ?> />		
					</li>
				<?php 
				endif;
				?>
				<input type="hidden" name="search" value="1"/>
				
    			<li style="margin-top:20px;border:none;"><input type="button" value="<?php echo __('Search')?>" id="searchPeople" class="button button-action"></li>
    		</ul>
    		</form>
    	</div>
	</div>	
	<?php endif; ?>
</div>

<div id="center">	
    <?php echo $this->element('hooks', array('position' => 'users_top') ); ?>
    
	<h1><?php echo __('People')?></h1>
	<ul class="list1 users_list" id="list-content">
		<?php 
		if ( !empty( $values ) || !empty($online_filter) )
			echo __('Loading...');
		else
			echo $this->element( 'lists/users_list', array( 'more_url' => '/users/ajax_browse/all/page:2' ) );
		?>
	</ul>
</div>