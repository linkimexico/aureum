<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined( '_JEXEC' ) or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JFormHelper::loadFieldClass('dynamicsingleselect');

class JFormFieldLayoutSelect extends JFormFieldDynamicSingleSelect
{
	public $type = 'LayoutSelect';

	protected function getOptions() 
	{
		$options = array();
		
		$lang = JFactory::getLanguage();
				
		$path = '/plugins/captcha/customrecaptcha';
		
		$files_plugin = JFolder::files(JPATH_SITE.$path.'/tmpl', '^[^_]*\.php$');
		
		foreach ($files_plugin as $option) {
			
			$option_name = str_replace('.php', '', $option);
			$option_title = strtoupper($option_name);
			
			$image_path = JURI::root(true).$path.'/images/layouts/'.$option_name.'.png';
			if (!JFile::exists(JPATH_ROOT.$path.'/images/layouts/'.$option_name.'.png')) {
				$image_path = JURI::root(true).$path.'/images/layouts/unknown.png';
			}
			
			$options[] = array($option_name, $option_title, '', $image_path);
		}
		
		// Get the client id
		$clientId = $this->element['client_id'];		
		if (is_null($clientId) && $this->form instanceof JForm) {
			$clientId = $this->form->getValue('client_id');
		}		
		$clientId = (int) $clientId;		
		$client = JApplicationHelper::getClientInfo($clientId);
		
		// Get the template
		$template = (string) $this->element['template'];
		$template = preg_replace('#\W#', '', $template);
		
		// Get the style
		$template_style_id = '';
		if ($this->form instanceof JForm) {
			$template_style_id = $this->form->getValue('template_style_id');
			$template_style_id = preg_replace('#\W#', '', $template_style_id);
		}
		
		if ($client) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->select('element, name')
			->from('#__extensions as e')
			->where('e.client_id = ' . (int) $clientId)
			->where('e.type = ' . $db->quote('template'))
			->where('e.enabled = 1');
			
			if ($template) {
				$query->where('e.element = ' . $db->quote($template));
			}
			
			if ($template_style_id) {
				$query->join('LEFT', '#__template_styles as s on s.template=e.element')
				->where('s.id=' . (int) $template_style_id);
			}
			
			$db->setQuery($query);
			$templates = $db->loadObjectList('element');
			
			if ($templates) {
				foreach ($templates as $template) {
							
					$template_path = JPath::clean($client->path . '/templates/' . $template->element . '/html/plg_captcha_customrecaptcha');
					
					// Add the layout options from the template path.
					if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$'))) {
						foreach ($files as $i => $file) {
							// Remove layout that already exists in plugin
							if (in_array($file, $files_plugin)) {
								unset($files[$i]);
							}
						}
						
						foreach ($files as $option) {
							
							$option_name = str_replace('.php', '', $option);
							$option_title = strtoupper($option_name);
							
							$description = '('.$template->element.')';
							
							$image_path = JURI::root(true).$path.'/images/layouts/'.$option_name.'.png';
							if (!JFile::exists(JPATH_ROOT.$path.'/images/layouts/'.$option_name.'.png')) {
								$image_path = JURI::root(true).$path.'/images/layouts/unknown.png';
							}
							
							$options[] = array($option_name, $option_title, $description, $image_path);
						}
					}
				}
			}
		}

		return $options;
	}

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->width = 200;
			$this->height = 120;
		}

		return $return;
	}
}
?>