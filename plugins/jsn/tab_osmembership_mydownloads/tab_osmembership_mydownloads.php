<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Osmembership_mydownloads extends JPlugin
{
	
	public function renderTabs($data, $jsnconfig)
	{
		$userID=JFactory::getUser()->id;
		$profileID=$data->id;
		/* Only Owner can see this tab */
		if($userID!=$profileID) return;

		$lang = JFactory::getLanguage();
		$lang->load('com_osmembership');
		$plugin=array(JText::_($this->params->get('tabtitle','My Downloads')));

		/* Include Core */
		include JPATH_ADMINISTRATOR . '/components/com_osmembership/config.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		ob_start();
		$this->drawDocuments();
		
		$script='';
		$css='<style></style>';
		$plugin[]='<div id="osmembership-mydownload-wrapper">'.ob_get_clean().'</div>'.$script.$css;

		return $plugin;
		
	}
	private function drawDocuments()
	{
		$db            = JFactory::getDbo();
		$query         = $db->getQuery(true);
		$activePlanIds = OSMembershipHelper::getActiveMembershipPlans();
		$query->select('*')
				->from('#__osmembership_documents')
				->order('ordering')
				->where('plan_id IN (' . implode(',', $activePlanIds) . ')')
				->order('plan_id')
				->order('ordering');
		$db->setQuery($query);
		$documents = $db->loadObjectList();

		if (empty($documents))
		{
			return;
		}

		$Itemid = JFactory::getApplication()->input->getInt('Itemid');
		$path = JPATH_ROOT . '/media/com_osmembership/documents/';
		?>
		<table class="adminlist table table-striped" id="adminForm">
			<thead>
			<tr>
				<th class="title"><?php echo JText::_('OSM_TITLE'); ?></th>
				<th class="title"><?php echo JText::_('OSM_DOCUMENT'); ?></th>
				<th class="center"><?php echo JText::_('OSM_SIZE'); ?></th>
				<th class="center"><?php echo JText::_('OSM_DOWNLOAD'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				for ($i = 0; $i < count($documents); $i++)
				{
					$document = $documents[$i];
					$downloadLink = JRoute::_('index.php?option=com_osmembership&task=download_document&id=' . $document->id . '&Itemid=' . $Itemid);
					?>
					<tr>
						<td><a href="<?php echo $downloadLink ?>"><?php echo $document->title; ?></a></td>
						<td><?php echo $document->attachment;?></td>
						<td class="center"><?php echo static::getFormattedFilezize($path . $document->attachment);?></td>
						<td class="center">
							<a href="<?php echo $downloadLink; ?>"><i class="icon-download"></i></a>
						</td>
					</tr>
					<?php
				}
			?>
			</tbody>
		</table>
		<?php
	}
	private static function getFormattedFilezize($file, $precision = 2)
	{
		$bytes = filesize($file);
		$size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
}

?>