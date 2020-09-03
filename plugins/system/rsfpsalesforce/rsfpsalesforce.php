<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRSFPSalesforce extends JPlugin
{
	public function rsfp_onFormSave($form)
	{
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$post['form_id'] = $post['formId'];
		
		$row = JTable::getInstance('RSForm_Salesforce', 'Table');
		if (!$row)
			return;
		if (!$row->bind($post))
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
		
		$db = JFactory::getDbo();
		$db->setQuery("SELECT form_id FROM #__rsform_salesforce WHERE form_id='".(int) $post['form_id']."'");
		if (!$db->loadResult())
		{
			$db->setQuery("INSERT INTO #__rsform_salesforce SET form_id='".(int) $post['form_id']."'");
			$db->execute();
		}
		
		$row->slsf_custom_fields = '';
		if (!empty($post['slsf_api_name']))
		{
			$row->slsf_custom_fields = array();
			for ($i=0; $i<count($post['slsf_api_name']); $i++)
			{
				$tmp = new stdClass();
				$tmp->api_name = $post['slsf_api_name'][$i];
				$tmp->value = $post['slsf_value'][$i];
				
				$row->slsf_custom_fields[] = $tmp;
			}
			$row->slsf_custom_fields = serialize($row->slsf_custom_fields);
		}
		
		if ($row->store())
		{
			return true;
		}
		else
		{
			JError::raiseWarning(500, $row->getError());
			return false;
		}
	}
	
	public function rsfp_bk_onFormCopy($args)
	{
		$formId = $args['formId'];
		$newFormId = $args['newFormId'];

		if ($row = JTable::getInstance('RSForm_Salesforce', 'Table') )
		{
			if ($row->load($formId)) {

				if (!$row->bind(array('form_id'=>$newFormId))) {
					JError::raiseWarning(500, $row->getError());
					return false;
				}

				$db 	= JFactory::getDbo();
				$query 	= $db->getQuery(true)
					->select($db->qn('form_id'))
					->from($db->qn('#__rsform_salesforce'))
					->where($db->qn('form_id').'='.$db->q($newFormId));
				if (!$db->setQuery($query)->loadResult()) {
					$query = $db->getQuery(true)
						->insert($db->qn('#__rsform_salesforce'))
						->set($db->qn('form_id').'='.$db->q($newFormId));
					$db->setQuery($query)->execute();
				}

				if ($row->store())
				{
					return true;
				}
				else
				{
					JError::raiseWarning(500, $row->getError());

					return false;
				}
			}
		}
	}
	
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpsalesforce');
		
		$row = JTable::getInstance('RSForm_Salesforce', 'Table');
		if (!$row)
			return;
		$row->load($formId);
		$row->slsf_custom_fields = !empty($row->slsf_custom_fields) ? unserialize($row->slsf_custom_fields) : array();
		
		$lists['published'] = RSFormProHelper::renderHTML('select.booleanlist','slsf_published','class="inputbox" onclick="enableSalesforce(this.value)"',$row->slsf_published);
		$lists['debug'] = RSFormProHelper::renderHTML('select.booleanlist','slsf_debug','class="inputbox" onclick="enableSalesforceDebug(this.value)"',$row->slsf_debug);
		
		echo '<div id="salesforcediv">';
			include JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/salesforce.php';
		echo '</div>';
	}
	
	public function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_rsfpsalesforce');
		
		echo '<li><a href="javascript: void(0);" id="salesforce"><span class="rsficon rsficon-cloud"></span><span class="inner-text">'.JText::_('RSFP_SALESFORCE_INTEGRATION').'</span></a></li>';
	}
	
	public function rsfp_f_onAfterFormProcess($args)
	{
		$db = JFactory::getDbo();
		
		$formId = (int) $args['formId'];
		$SubmissionId = (int) $args['SubmissionId'];
		
		$db->setQuery("SELECT * FROM #__rsform_salesforce WHERE `form_id`='".$formId."' AND `slsf_published`='1'");
		if ($row = $db->loadObject())
		{
			list($replace, $with) = RSFormProHelper::getReplacements($SubmissionId);
			$replace[] = '\n';
			$with[]	   = "\n";
			
			$data = array(
				'oid' 			=> $row->slsf_oid,
				'lead_source' 	=> $row->slsf_lead_source,
				'first_name' 	=> $row->slsf_first_name,
				'last_name' 	=> $row->slsf_last_name,
				'title' 		=> $row->slsf_title,
				'company' 		=> $row->slsf_company,
				'email' 		=> $row->slsf_email,
				'phone' 		=> $row->slsf_phone,
				'street' 		=> $row->slsf_street,
				'city' 			=> $row->slsf_city,
				'state' 		=> $row->slsf_state,
				'zip' 			=> $row->slsf_zip,
				'country' 		=> $row->slsf_country,
				'industry' 		=> $row->slsf_industry,
				'description' 	=> $row->slsf_description,
				'mobile' 		=> $row->slsf_mobile,
				'fax' 			=> $row->slsf_fax,
				'URL' 			=> $row->slsf_website,
				'salutation' 	=> $row->slsf_salutation,
				'revenue' 		=> $row->slsf_revenue,
				'employees' 	=> $row->slsf_employees,
				'emailOptOut' 	=> $row->slsf_emailoptout,
				'faxOptOut' 	=> $row->slsf_faxoptout,
				'doNotCall' 	=> $row->slsf_donotcall
			);
			
			if ($row->slsf_campaign_id)
			{
				$data['Campaign_ID'] = $row->slsf_campaign_id; 
			}
			
			$row->slsf_custom_fields = !empty($row->slsf_custom_fields) ? unserialize($row->slsf_custom_fields) : array();
			if (!empty($row->slsf_custom_fields))
			{
				foreach ($row->slsf_custom_fields as $field)
				{
					$data[$field->api_name] = $field->value;
				}
			}
			
			$data['debug'] = (int) $row->slsf_debug;
			if ($row->slsf_debug)
			{
				$data['debugEmail'] = $row->slsf_debugEmail;
			}
			
			$data = str_replace($replace, $with, $data);
			
			try
			{
				jimport('joomla.http.factory');
				$http 	 = JHttpFactory::getHttp();
				$url 	 = 'https://webto.salesforce.com/servlet/servlet.WebToLead';
				$request = $http->post($url, $data);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}
	
	public function rsfp_onFormDelete($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->delete('#__rsform_salesforce')
			  ->where($db->qn('form_id').'='.$db->q($formId));
		$db->setQuery($query)->execute();
	}
	
	public function rsfp_onFormBackup($form, $xml, $fields) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('*')
			  ->from($db->qn('#__rsform_salesforce'))
			  ->where($db->qn('form_id').'='.$db->q($form->FormId));
		$db->setQuery($query);
		if ($salesforce = $db->loadObject()) {
			// No need for a form_id
			unset($salesforce->form_id);
			
			$xml->add('salesforce');
			foreach ($salesforce as $property => $value) {
				$xml->add($property, $value);
			}
			$xml->add('/salesforce');
		}
	}
	
	public function rsfp_onFormRestore($form, $xml, $fields) {
		if (isset($xml->salesforce)) {
			$data = array(
				'form_id' => $form->FormId
			);
			
			foreach ($xml->salesforce->children() as $property => $value) {
				$data[$property] = (string) $value;
			}
			
			$row = JTable::getInstance('RSForm_Salesforce', 'Table');
			
			if (!$row->load($form->FormId)) {
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query	->insert('#__rsform_salesforce')
						->set(array(
								$db->qn('form_id') .'='. $db->q($form->FormId),
						));
				$db->setQuery($query)->execute();
			}
			
			$row->save($data);
		}
	}
	
	public function rsfp_bk_onFormRestoreTruncate() {
		JFactory::getDbo()->truncateTable('#__rsform_salesforce');
	}
}