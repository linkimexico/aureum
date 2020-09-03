<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class PlgJsnAjaxuserlist extends JPlugin
{
	public $random;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->random=time();
	}

	public function renderAfterList($data, $config)
	{
		$app = JFactory::getApplication();
		$Itemid=$app->input->get('Itemid',null);

		$menuItems = $this->params->get('menuitems',array());

		if(in_array($Itemid,$menuItems)) return;

		$mapAjaxFunctions='';

		/*$modules=JModuleHelper::getModuleList();

		$mapAjaxFunctions='';
		foreach($modules as $module)
		{
			if($module->module=='mod_jsnmap' && $module->id==110)
			{
				//$from=array('id="jsnmap'.$module->id.'-canvas"','initialize_jsnmap'.$module->id);
				//$to=array('id="jsnmap'.$module->id.'-hidden"','initialize_jsnmap'.$module->id.'_ajax');
				//echo str_replace($from,$to,$moduleOutput=JModuleHelper::renderModule($module));
				//$mapAjaxFunctions.='initialize_jsnmap'.$module->id.$this->random.'_ajax();'."\n";
				//
			}
		}*/

		?>
		<script>
		function ajaxUserListPagination()
			{
				jQuery('#jsn_listresult .pagination a').click(function(e){
					jQuery('#jsn_listresult').css('opacity','0.4');
					var request = jQuery.ajax({
				        type:"POST",
				        url:jQuery(this).attr('href'),
				        data:'format=raw'
				    });
					request.done(function( result ) {
						jQuery( "#jsn_listresult" ).html( jQuery(result).find('#jsn_listresult').html() );
						ajaxUserListPagination();
						<?php echo $mapAjaxFunctions ?>
						jQuery('#jsn_listresult').css('opacity','1');
					});
					e.preventDefault();
				});
			}
		function ajaxUserSearch(form)
		{
			if(window.location.href.indexOf(form.attr("action")) >= 0){
				var request = jQuery.ajax({
			        type:"POST",
			        url:form.attr("action"),
			        data:form.serialize()+'&format=raw'
			    });
				request.done(function( result ) {
					jQuery( "#jsn_listresult" ).html( jQuery(result).find('#jsn_listresult').html() );
					ajaxUserListPagination();
					<?php echo $mapAjaxFunctions ?>
					jQuery('#jsn_listresult').css('opacity','1');
				});
			}
		}
		jQuery(document).ready(function($)
		{
			ajaxUserListPagination();
			var searchTimeout;
			var dataForm;
			$('.jsn_search_module input,.jsn_search input,.jsn_search_module select,.jsn_search select').bind('change keyup blur',function(e){
				var form = jQuery(this).closest('form');
				if(window.location.href.indexOf(form.attr("action")) >= 0){
					if(dataForm!=form.serialize()){
						if(e.type=='keyup' && $(this).is('[autocomplete]') && e.keyCode != 13) jQuery('#jsn_listresult').css('opacity','1');
						else 
						{
							$('#jsn_listresult').css('opacity','0.4');
							dataForm=form.serialize();
							clearTimeout(searchTimeout);
							searchTimeout = setTimeout(function(){ajaxUserSearch(form)},500);
						}
					}
				}
			});
		});
		</script>
		<?php
		
	}
	public function renderAfterResultList($data, $config)
	{
		$app = JFactory::getApplication();
		if($app->input->get('format',null)!='raw') return;
		$Itemid=$app->input->get('Itemid',null);

		$menuItems = $this->params->get('menuitems',array());

		if(in_array($Itemid,$menuItems)) return;

		$modules=PlgJsnAjaxuserlist::getModuleList();
		foreach($modules as $module)
		{
			if($module->module=='mod_jsnmap')
			{
				$params = (array) json_decode($module->params);
				if(isset($params['syncUserlist']) && $params['syncUserlist']==1)
				{
					$from=array('id="jsnmap'.$module->id.'-canvas"','initialize_jsnmap'.$module->id);
					$to=array('id="jsnmap'.$module->id.'-hidden"','initialize_jsnmap'.$module->id.$this->random.'_ajax');
					$output = str_replace($from,$to,$moduleOutput=JModuleHelper::renderModule($module));
					//echo(strlen($output));
					if($output)
					{
						echo $output;
						echo '<style>#jsnmap'.$module->id.'-hidden{display:none !important}</style>';
						echo '<script>if(jQuery("#jsnmap'.$module->id.'-canvas").length) initialize_jsnmap'.$module->id.$this->random.'_ajax();</script>';
					}
					else
					{
						echo '<style>#jsnmap'.$module->id.'-canvas .gm-style{display:none !important}#jsnmap'.$module->id.'-canvas:before{content:"'.JText::_('COM_JSN_NORESULT').'";position:relative;top:50%;font-size:13px;margin-top:-7px;width:100%;text-align:center;line-height:13px;display:block;}</style>';
					}
				}

			}
		}
	}

	public static function getModuleList()
	{
		$app = JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid')
			->from('#__modules AS m')
			->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id')
			->where('m.published = 1')
			->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
			->where('e.enabled = 1');

		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')')
			->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')')
			->where('m.access IN (' . $groups . ')')
			->where('m.client_id = ' . $clientId)
			->where('(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)');

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
		}

		$query->order('m.position, m.ordering');

		// Set the query
		$db->setQuery($query);

		try
		{
			$modules = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), JLog::WARNING, 'jerror');

			return array();
		}

		return $modules;
	}

}

?>