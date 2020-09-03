<?php
/**
 * @package        RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

require_once dirname(__FILE__) . '/PayFastDefines.php';

/**
 * Class RSFPPayFast
 */
class RSFPPayFast
{
	/**
	 * @var
	 */
	private $merchantId;
	/**
	 * @var
	 */
	private $merchantKey;
	/**
	 * @var
	 */
	private $signature;
	/**
	 * @var
	 */
	public $args = array();
	/**
	 * @var
	 */
	public $submissionId;

	/**
	 * @var
	 */
	public $url = 'https://sandbox.payfast.co.za/eng/process?';

	/**
	 * RSFPPayFast constructor.
	 *
	 */
	public function __construct()
	{
		if (!(bool) RSFormProHelper::getConfig('payfast.test'))
		{
			$this->url = 'https://www.payfast.co.za/eng/process?';
		}
	}
	
	public function redirect()
	{
		// Add signature
		$this->processSignature();
		
		JFactory::getApplication()->redirect($this->url . http_build_query($this->args));
	}

	public function processSignature()
	{
		$data =& $this->args;
		if (!isset($data['merchant_id']))
		{
			$data['merchant_id'] = trim(RSFormProHelper::getConfig('payfast.merchantid'));
		}
		
		if (!isset($data['merchant_key']))
		{
			$data['merchant_key'] = trim(RSFormProHelper::getConfig('payfast.merchantkey'));
		}
		
		$signature       = array();
		$signatureString = '';

		foreach ($data as $key => $val)
		{
			$signature[$key] = stripslashes(trim($val));
		}

		foreach ($signature as $key => $val)
		{
			if (!empty($val))
			{
				$signatureString .= $key . '=' . urlencode($val) . '&';
			}
		}

		$signatureString = substr($signatureString, 0, -1);
		$passphrase      = trim(RSFormProHelper::getConfig('payfast.passphrase'));

		if (!empty($passphrase))
		{
			$signatureString .= '&passphrase=' . urlencode($passphrase);
		}

		$this->signature = md5($signatureString);
		
		$data['m_payment_id'] = $this->signature;
		
		try
		{
			$formId = JFactory::getApplication()->input->getInt('formId', 0);

			$values = array(
				'signature'     => $this->signature,
				'form_id'       => $formId,
				'submission_id' => $this->submissionId
			);

			$row = JTable::getInstance('RSForm_PayFast', 'Table');

			if (!$row)
			{
				throw new Exception ('Could not get an instance of the PayFast Table');
			}

			if (!$row->bind($values))
			{
				throw new Exception ('Could not bind data from the PayFast Table');
			}

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('submission_id'))
				->from($db->qn('#__rsform_payfast'))
				->where($db->qn('submission_id') . '=' . $db->q((int) $this->submissionId));
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$query->clear();
				$query->insert('#__rsform_payfast')
					->set($db->qn('submission_id') . '=' . $db->q((int) $this->submissionId));
				$db->setQuery($query);
				$db->execute();
			}

			if (!$row->store())
			{
				throw new Exception ('Could not save information to database');
			}

		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
	
	public static function getInstance()
	{
		static $inst;
		if (!$inst) {
			$inst = new RSFPPayFast;
		}

		return $inst;
	}

	public static function checkOrderStatus($submissionId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('FieldValue'))
			->from($db->qn('#__rsform_submission_values'))
			->where($db->qn('FieldName') . '=' . $db->q('_STATUS'))
			->where($db->qn('SubmissionId') . ' = ' . $db->q($submissionId));
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * @param $signature
	 *
	 * @return bool
	 */
	public static function changeOrderStatus($signature)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('submission_id'))
			->from($db->qn('#__rsform_payfast'))
			->where($db->qn('signature') . '=' . $db->q($signature));
		$db->setQuery($query);

		if ($result = $db->loadResult())
		{
			$query->clear();
			$query->update($db->qn('#__rsform_submission_values'))
				->set($db->qn('FieldValue') . ' = ' . $db->q(1))
				->where($db->qn('FieldName') . ' = ' . $db->q('_STATUS'))
				->where($db->qn('SubmissionId') . ' = ' . $db->q($result));
			$db->setQuery($query);
			$db->execute();

			return true;
		}

		return false;
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	public static function checkSignature($data)
	{
		$signatureString = '';

		foreach ($data as $key => $val)
		{
			$data[$key] = stripslashes($val);
		}

		$passphrase = trim(RSFormProHelper::getConfig('payfast.passphrase'));
		if (!empty($passphrase))
		{
			$data['passphrase'] = $passphrase;
		}

		foreach ($data as $key => $val)
		{
			if ($key != 'signature')
			{
				$signatureString .= $key . '=' . urlencode($val) . '&';
			}
		}

		$signatureString = substr($signatureString, 0, -1);

		$signature = md5($signatureString);

		if ($signature === $data['signature'])
		{
			return array(
				'data'      => $signatureString,
				'signature' => $signature,
				'status'    => true
			);
		}

		return array('status' => false);
	}

	/**
	 * @param $remoteAddr
	 *
	 * @return bool
	 */
	public static function checkDomain($remoteAddr)
	{
		$validHosts = array(
			'www.payfast.co.za',
			'sandbox.payfast.co.za',
			'w1w.payfast.co.za',
			'w2w.payfast.co.za',
		);

		$validIps = array();

		foreach ($validHosts as $hostname)
		{
			$ips = gethostbynamel($hostname);

			if ($ips !== false)
			{
				$validIps = array_merge($validIps, $ips);
			}
		}

		$validIps = array_unique($validIps);

		if (!in_array($remoteAddr, $validIps))
		{
			return false;
		}

		return true;
	}

	/**
	 * @param $amount
	 * @param $signature
	 *
	 * @return bool
	 */
	public static function checkAmount($amount, $signature)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('v.FieldValue'))
			->from($db->qn('#__rsform_submission_values', 'v'))
			->join('LEFT', $db->qn('#__rsform_payfast', 'p') . ' ON (' . $db->qn('p.submission_id') . ' = ' . $db->qn('v.SubmissionId') . ')')
			->where($db->qn('p.signature') . '=' . $db->q($signature))
			->where($db->qn('v.FieldName') . '=' . $db->q('rsfp_Total'));
		$db->setQuery($query);
		$result = $db->loadResult();
		if (abs(floatval($result) - floatval($amount)) > 0.01)
		{
			return false;
		}

		return true;
	}

	/**
	 * @param $data
	 * @param $url
	 *
	 * @return string
	 */
	public static function checkDataFromRemote($data, $url)
	{
		$http = JHttpFactory::getHttp();

		$http->setOption('userAgent', PF_USER_AGENT);

		$response = $http->post($url, $data, array(), 5);

		if ($response->body === 'VALID')
		{
			return true;
		}

		return false;
	}

}