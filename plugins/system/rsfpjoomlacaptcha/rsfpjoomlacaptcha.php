<?php
/**
* @package RSform!Pro
* @copyright (C) 2007-2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class plgSystemRSFPJoomlaCaptcha extends JPlugin
{
	protected $autoloadLanguage = true;
	
	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
		
		$jversion = new JVersion();
		if ($jversion->isCompatible('2.5') && !$jversion->isCompatible('3.0')) {
			$this->loadLanguage();
		}
	}
	
	// get the available captchas
	
	public function rsfp_bk_getAvailableCaptchas() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->qn('element', 'value'))
			  ->select($db->qn('name', 'text'))
			  ->from($db->qn('#__extensions'))
			  ->where($db->qn('folder').'='.$db->q('captcha'))
			  ->where($db->qn('enabled').'='.$db->q(1))
			  ->order($db->qn('ordering').' ASC');
		
		$db->setQuery($query);
		$plugins = $db->loadObjectList();
		
		$lang = JFactory::getLanguage();
		
		$availablePlugins = array('joomla_default|joomla_default_captcha');
		
		if (!empty($plugins)) {
			foreach ($plugins as $plugin) {
			
				$extension = 'plg_captcha_' . $plugin->value;
				$source = JPATH_PLUGINS . '/captcha/' . $plugin->value;
				$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true) ||	$lang->load($extension . '.sys', $source, null, false, true);
				
				$availablePlugins[] = $plugin->value.'|'.JText::_($plugin->text);
			}
		}
		
		return implode("\n", $availablePlugins);
	}
	
	// Show field in Form Components
	public function rsfp_bk_onAfterShowComponents() {
		$formId 	= JFactory::getApplication()->input->getInt('formId');
		$exists 	= RSFormProHelper::componentExists($formId, 2525);
		$link		= $exists ? "displayTemplate('2525', '{$exists[0]}')" : "displayTemplate('2525')";
		
		?>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_JOOMLA_BUILT_IN_CAPTCHA_LABEL'); ?></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link;?>;return false;" id="captcha"><span class="rsficon rsficon-spinner9"></span><span class="inner-text"><?php echo JText::_('RSFP_JOOMLA_CAPTCHA_LABEL'); ?></span></a></li>
		<?php
	}
	
	// Show backend preview of field
	public function rsfp_bk_onAfterCreateComponentPreview($args = array()) {
		if ($args['ComponentTypeName'] == 'joomlacaptcha') {
			$args['out']  = '<td>'.$args['data']['CAPTION'].'</td>';
			$args['out'] .= '<td>'.JText::_('RSFP_JOOMLA_BUILT_IN_CAPTCHA_LABEL').'</td>';
		}
	}
	
	public function rsfp_bk_onAfterCreateFrontComponentBody($args) {
		$typeId 		= $args['r']['ComponentTypeId'];
		$formId			= $args['formId'];
		$componentId	= $args['componentId'];
		
		if ($typeId == 2525) {
			$data = $args['data'];
			$captcha = $data['CAPTCHA'];

			if ($captcha == 'joomla_default') {
				$jconfig = JFactory::getConfig();
				$captcha = $jconfig->get('captcha');
			}
			
			if (!empty($captcha)) {
				// check if additional attributes exists
				$attributes = trim($data['ADDITIONALATTRIBUTES']);
				$attributes = $this->parseAttributes($attributes);
				
				// if the class attribute does not exist then we have to add it
				if (!isset($attributes['class'])) {
					$attributes['class'] = '';
				}
				
				$newAttributes = array();
				foreach($attributes as $attr => $value) {
					$newAttributes[] = $this->escape($attr).'='.'"'.$this->escape($value).'"';
				}
				$newAttributes = implode(' ', $newAttributes);
			
				try {

					$captcha = JCaptcha::getInstance('recaptcha', array('namespace'=>'captcha-'.$componentId));
					if (!is_null($captcha))
					{
						$args['out'] .= $captcha->display('form[' . $data['NAME'] . ']', 'captcha-' . $componentId, $newAttributes);
					}
				} catch (Exception $e) {
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}
	}
	
	public function rsfp_f_onBeforeFormValidation($args) {
		$formId 	= $args['formId'];
		$invalid 	=& $args['invalid'];
		$post		=& $args['post'];
		$form       = RSFormProHelper::getForm($formId);
		if (!empty($form->RemoveCaptchaLogged)) {
			$logged     = JFactory::getUser()->id;
		} else {
			$logged = false;
		}
		
		if (($componentId = RSFormProHelper::componentExists($formId, 2525)) && !$logged) {
			$componentData = RSFormProHelper::getComponentProperties($componentId[0]);
			$captcha = $componentData['CAPTCHA'];
			$codeField = $componentData['NAME'];
			
			if ($captcha == 'joomla_default') {
				$jconfig = JFactory::getConfig();
				$captcha = $jconfig->get('captcha');
			}
			
			if (!empty($captcha)) {
				$input = JFactory::getApplication()->input;
				$task	  = strtolower($input->get('task'));
				$option	  = strtolower($input->get('option'));
				
				// if the ajax validation is on then the answer check must be skipped
				if ($option == 'com_rsform' && $task == 'ajaxvalidate') {
					return true;
				}

				$captcha = JCaptcha::getInstance('recaptcha', array('namespace'=>'captcha-'.$componentId[0]));
				if (!is_null($captcha) && !$captcha->checkAnswer(isset($post[$codeField]) ? $post[$codeField] : null)){
					$invalid[] = $componentId[0];
					return false;
				}
			}
		}
	}
	
	protected function parseAttributes($string) {
		$attributes = array();
		if (!empty($string)) {
			$attr = array();

			// Let's grab all the key/value pairs using a regular expression
			preg_match_all('/([\w:-]+)[\s]?(=[\s]?"([^"]*)")?/i', $string, $attr);

			if (is_array($attr))
			{
				$numPairs = count($attr[1]);
				for ($i = 0; $i < $numPairs; $i++)
				{
					$attributes[$attr[1][$i]] = $attr[3][$i];
				}
			}
		}

		return $attributes;
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_QUOTES, 'utf-8');
	}
}