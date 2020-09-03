<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Easydiscuss extends JPlugin
{
	
	public function renderTabs($data, $config)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_easydiscuss');
		$plugin=array(JText::_($this->params->get('tabtitle','Forum')));

		// Include main engine
		require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');
		

		require_once(DISCUSS_ROOT . '/views/profile/view.html.php');

		$app = JFactory::getApplication();
		$input = $app->input;
		$input->set('hideToolbar',1);

		$view=new EasyDiscussViewProfile();

		ob_start();
		$view->display();
		
		$script='<script>jQuery(document).ready(function(){
			jQuery("#profileTabs a").click(function(){
				if(jQuery(".jsn_profile_fields").width()>450) jQuery("#ed").removeClass("w320 w480");
			});
		});
		</script>';
		
		$css='<style>
			.ed-navbar,.ed-user-profile{display:none;}
		</style>';
		
		$plugin[]=$script.$css.'<div id="discuss-wrapper" class="discuss-view-profile">'.ob_get_clean().'<input type="hidden" class="easydiscuss-token" value="' . DiscussHelper::getToken() . '" />'.'</div>';
		
		
		return $plugin;
		
	}
}

?>