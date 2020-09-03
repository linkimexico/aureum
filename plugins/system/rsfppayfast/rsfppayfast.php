<?php
/**
 * @package        RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class plgSystemRSFPPayfast
 */
class plgSystemRSFPPayfast extends JPlugin
{
	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;
	/**
	 * @var int
	 */
	protected $componentId = 512;
	/**
	 * @var string
	 */
	protected $componentValue = 'payfast';
	/**
	 * @var array
	 */
	protected $newComponents = array();

	/**
	 * plgSystemRSFPPayfast constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct(&$subject, $config)
	{
		$jversion = new JVersion();
		if ($jversion->isCompatible('2.5') && !$jversion->isCompatible('3.0'))
		{
			$this->loadLanguage();
		}

		parent::__construct($subject, $config);
		$this->newComponents = array(512);
	}

	/**
	 * @throws Exception
	 */
	public function rsfp_bk_onAfterShowComponents()
	{

		$formId = JFactory::getApplication()->input->getInt('formId');

		$link = "displayTemplate('" . $this->componentId . "');";
		if ($components = RSFormProHelper::componentExists($formId, $this->componentId))
		{
			$link = "displayTemplate('" . $this->componentId . "', '" . $components[0] . "');";
		}
		?>
		<li>
			<a href="javascript: void(0);" onclick="<?php echo $link; ?> return false;" id="rsfpc<?php echo $this->componentId; ?>"><span class="rsficon rsficon-list-alt"></span><span class="inner-text"><?php echo JText::_('RSFP_PAYFAST_COMPONENT'); ?></span></a>
		</li>
		<?php
	}

	/**
	 * @param $items
	 * @param $formId
	 */
	public function rsfp_getPayment(&$items, $formId)
	{
		if ($components = RSFormProHelper::componentExists($formId, $this->componentId))
		{
			$data = RSFormProHelper::getComponentProperties($components[0]);

			$item        = new stdClass();
			$item->value = $this->componentValue;
			$item->text  = $data['LABEL'];

			// add to array
			$items[] = $item;
		}
	}

	/**
	 * @throws Exception
	 */
	public function rsfp_f_onSwitchTasks()
	{
		$input        = JFactory::getApplication()->input;
		$formId       = $input->getInt('formId', 0);
		$submissionId = $input->getInt('SubmissionId', '');
		$application  = JFactory::getApplication();

		if ($input->getString('plugin_task', '') == 'payfast.notify')
		{
			$this->payFastITN($formId);
		}

		if ($input->getString('plugin_task', '') == 'payfast.return')
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/PayFast/PayFast.php';

			$message['type'] = 'error';

			if ((int) RSFPPayFast::checkOrderStatus($submissionId))
			{
				$message['type'] = 'Notice';
			}

			$message['message'] = JText::_('PLG_SYSTEM_RSFPPAYFAST_TRANSACTION_SUCCESFUL');

			if ( ! (int) RSFPPayFast::checkOrderStatus($submissionId))
			{
				$message['message'] = JText::_('PLG_SYSTEM_RSFPPAYFAST_TRANSACTION_FAILED');
			}
			// Redirect
			$application->enqueueMessage($message['message'], $message['type']);
		}

	}

	/**
	 * @param $formId
	 */
	protected function payFastITN($formId)
	{
		header('HTTP/1.0 200 OK');
		flush();

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/PayFast/PayFast.php';
		$confirmed = false;

		$status = $this->runChecks($_POST);

		if ($status)
		{
			RSFPPayFast::changeOrderStatus($_POST['m_payment_id']);
		}

	}

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	protected function runChecks($data)
	{
		$status = array(
			'signature'      => false,
			'source_ip'      => false,
			'amount'         => false,
			'data'           => false,
			'payment_status' => false,
		);

		$signature = RSFPPayFast::checkSignature($data);
		if ($signature['status'])
		{
			$status['signature'] = true;
		}

		if (RSFPPayFast::checkDomain($_SERVER['REMOTE_ADDR']))
		{
			$status['source_ip'] = true;
		};

		if (RSFPPayFast::checkAmount($data['amount_gross'], $data['m_payment_id']))
		{
			$status['amount'] = true;
		};

		$url = 'https://sandbox.payfast.co.za/eng/query/validate';
		if (!(bool) RSFormProHelper::getConfig('payfast.test'))
		{
			$url = 'https://www.payfast.co.za/eng/query/validate';
		}

		if (!RSFPPayFast::checkDataFromRemote($data['data'], $url))
		{
			$status['data'] = true;
		}

		if ($data['payment_status'] === 'COMPLETE')
		{
			$status['payment_status'] = true;
		}
		$result = false;

		foreach ($status as $key => $value)
		{
			if ($value === true)
			{
				$result = true;
				break;
			}
		}

		return $result;

	}

	/**
	 * @param $payValue
	 * @param $formId
	 * @param $SubmissionId
	 * @param $price
	 * @param $products
	 * @param $code
	 *
	 * @throws Exception
	 */
	public function rsfp_doPayment($payValue, $formId, $SubmissionId, $price, $products, $code)
	{
		// execute only for our plugin
		if ($payValue != $this->componentValue)
		{
			return;
		}

		if ($price > 0)
		{
			$app = JFactory::getApplication();
			try
			{
				/**
				 * Get the calculated price (with tax)
				 */
				if ($tax = RSFormProHelper::getConfig('payfast.tax.value'))
				{
					$price = $this->calcTax($price, RSFormProHelper::getConfig('payfast.tax.value'), RSFormProHelper::getConfig('payfast.tax.type'));
				}

				$price = number_format(sprintf('%.2f', $price), 2, '.', '');

				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/PayFast/PayFast.php';

				/**
				 * Build the description based on products.
				 * In case it's an array, we convert it as string
				 */
				$description = $products;
				if (is_array($description))
				{
					$description = substr(implode(', ', $products), 0, 100);
				}

				$vars = array(
					'return_url'   => JRoute::_(JURI::root() . 'index.php?option=com_rsform&formId=' . $formId . '&task=plugin&plugin_task=payfast.return&$SubmissionId=' . $SubmissionId),
					'notify_url'   => JRoute::_(JURI::root() . 'index.php?option=com_rsform&formId=' . $formId . '&task=plugin&plugin_task=payfast.notify&$SubmissionId=' . $SubmissionId),
					'amount'       => $price,
					'item_name'    => $description,
				);
				
				$PayFast = RSFPPayFast::getInstance();
				
				// If any options have already been set, use this to override the ones used here
				$PayFast->args = array_merge($vars, $PayFast->args);
				
				$PayFast->submissionId = $SubmissionId;
				
				$PayFast->redirect();
			} catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'warning');
			}

		}
	}

	/**
	 * @param array $args
	 */
	public function rsfp_bk_onAfterCreateComponentPreview($args = array())
	{
		if ($args['ComponentTypeName'] == 'payfast')
		{
			$args['out'] = '<td>&nbsp;</td>';
			$args['out'] .= '<td class="payfast"><span style="font-size:24px;margin-right:5px" class="rsficon rsficon-list-alt"></span> ' . $args['data']['LABEL'] . '</td>';
		}
	}

	/**
	 * @param $tabs
	 */
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs)
	{
		JFactory::getLanguage()->load('plg_system_rsfppayfast');

		$tabs->addTitle(JText::_('RSFP_PAYFAST_LABEL'), 'form-payfast');
		$tabs->addContent($this->payfastConfigurationScreen());
	}

	/**
	 * @return string
	 */
	public function payfastConfigurationScreen()
	{
		ob_start();

		?>
		<div id="page-payfast" class="com-rsform-css-fix">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key">
						<label for="payfastmerchantid"><?php echo JText::_('RSFP_PAYFAST_LOGINID'); ?></label></td>
					<td>
						<input id="payfastmerchantid" type="text" name="rsformConfig[payfast.merchantid]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('payfast.merchantid')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key">
						<label for="payfasttransactionkey"><?php echo JText::_('RSFP_PAYFAST_TRANSACTIONKEY'); ?></label>
					</td>
					<td>
						<input id="authorizetransactionkey" type="text" name="rsformConfig[payfast.merchantkey]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('payfast.merchantkey')); ?>" size="100">
					</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key">
						<label><?php echo JText::_('RSFP_PAYFAST_TEST'); ?></label></td>
					<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[payfast.test]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('payfast.test'))); ?></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key">
						<label><?php echo JText::_('RSFP_PAYFAST_TAX_TYPE'); ?></label>
					</td>
					<td><?php echo JHTML::_('select.booleanlist', 'rsformConfig[payfast.tax.type]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('payfast.tax.type')), JText::_('RSFP_PAYFAST_TAX_TYPE_FIXED'), JText::_('RSFP_PAYFAST_TAX_TYPE_PERCENT')); ?></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key">
						<label for="payfasttax"><?php echo JText::_('RSFP_PAYFAST_TAX_VALUE'); ?></label>
					</td>
					<td>
						<input id="payfasttax" type="text" name="rsformConfig[payfast.tax.value]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('payfast.tax.value')); ?>" size="4" maxlength="5">
					</td>
				</tr>
			</table>
		</div>
		<?php

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * @param $price
	 * @param $amount
	 * @param $type
	 *
	 * @return mixed
	 */
	public function calcTax($price, $amount, $type)
	{
		switch ($type)
		{
			case false:
				$price = $price + (($price * $amount) / 100);
				break;

			case true:
				$price = $price + $amount;
				break;
		}

		return $price;
	}

	/**
	 * @param $formId
	 */
	public function rsfp_onFormDelete($formId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__rsform_payfast')
			->where($db->qn('form_id') . '=' . $db->q($formId));
		$db->setQuery($query)->execute();
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormBackup($form, $xml, $fields)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->qn('#__rsform_payfast'))
			->where($db->qn('form_id') . '=' . $db->q($form->FormId));
		$db->setQuery($query);
		if ($result = $db->loadObject())
		{
			// No need for a form_id
			unset($result->form_id);

			$xml->add('payfast');
			foreach ($result as $property => $value)
			{
				$xml->add($property, $value);
			}
			$xml->add('/payfast');
		}
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormRestore($form, $xml, $fields)
	{
		if (isset($xml->payfast))
		{
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->payfast->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}
			$row = JTable::getInstance('RSForm_PayFast', 'Table');

			if (!$row->load($form->FormId))
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->insert('#__rsform_payfast')
					->set(array(
						$db->qn('form_id') . '=' . $db->q($form->FormId),
					));
				$db->setQuery($query)->execute();
			}

			$row->save($data);
		}
	}

	/**
	 *
	 */
	public function rsfp_bk_onFormRestoreTruncate()
	{
		JFactory::getDbo()->truncateTable('#__rsform_payfast');
	}
}