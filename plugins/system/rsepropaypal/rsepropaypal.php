<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemRsepropaypal extends JPlugin
{
	//set the value of the payment option
	var $rsprooption = 'paypal';
	
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}
	
	public function onAfterInitialise() {		
		$app	= JFactory::getApplication();
		$jinput	= $app->input;
		
		if($app->getName() != 'site') 
			return;

		$paypal = $jinput->getInt('paypalrsepro');
		if (!empty($paypal))
			$this->rsepro_processForm(array());
	}
	
	/*
	*	Is RSEvents!Pro installed
	*/
	
	protected function canRun() {
		$helper = JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
		if (file_exists($helper)) {
			require_once $helper;
			JFactory::getLanguage()->load('plg_system_rsepropaypal',JPATH_ADMINISTRATOR);
			
			return true;
		}
		
		return false;
	}
	
	/*
	*	Add the current payment option to the Payments List
	*/

	public function rsepro_addOptions() {
		if ($this->canRun())
			return JHTML::_('select.option', $this->rsprooption, JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_NAME'));
		else return JHTML::_('select.option', '', '');
	}
	
	/*
	*	Add optional fields for the payment plugin. Example: Credit Card Number, etc.
	*	Please use the syntax <form method="post" action="index.php?option=com_rseventspro&task=process" name="paymentForm">
	*	The action provided in the form will actually run the rsepro_processForm() of your payment plugin.
	*/
	
	public function rsepro_showForm($vars) {
		
		if (JFactory::getApplication()->isClient('administrator') || !$this->canRun() || !JPluginHelper::isEnabled('system', 'rsepropaypal')) {
			return;
		}
		
		if (isset($vars['method']) && $vars['method'] == $this->rsprooption) {
			jimport('joomla.mail.helper');
			JFactory::getLanguage()->load('com_rseventspro',JPATH_SITE);
			
			// Load variables
			$db			= JFactory::getDbo();
			$query		= $db->getQuery(true);
			$details	= $vars['details'];
			$tickets	= $vars['tickets'];
			$currency	= $vars['currency'];
			$cart		= $vars['cart'];
			$total		= $vars['total'];
			$info		= $vars['info'];
			$discount	= 0;
			
			// Do we have a valid payment request ?
			if (empty($details) && empty($tickets)) {
				return;
			}
			
			// PayPal variables
			$paypal_email		= $this->params->get('paypal_email','');
			$paypal_return		= $this->params->get('return_url','');
			$paypal_cancel		= $this->params->get('cancel_url','');
			$paypal_lang		= $this->params->get('paypal_lang','US');
			
			if (!$cart) {
				// Do we let users sell their own tickets ?
				if (rseventsproHelper::getConfig('payment_paypal','int')) {
					$query->clear()
						->select($db->qn('paypal_email'))
						->from($db->qn('#__rseventspro_events'))
						->where($db->qn('id').' = '.(int) $details->ide);
					
					$db->setQuery($query);
					if ($user_paypal = $db->loadResult()) {
						$paypal_email = $user_paypal;
					}
				}
			}
			
			// Do we have a valid business email address ?
			if (!JMailHelper::isEmailAddress($paypal_email)) {
				echo JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_NO_VALID_EMAIL');
				return;
			}
			
			// Is the return URL a valid one ?
			if (substr($paypal_return,0,4) != 'http') {
				$paypal_return = '';
			}
			
			// Get PayPal URL
			$paypal_url = $this->params->get('paypal_mode') ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			
			if ($details->early_fee)		$discount += $details->early_fee;
			if (!empty($details->discount))	$discount += $details->discount;
			
			$html = '';
			$html .= '<fieldset>'."\n";
			$html .= '<legend>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_TICKETS_INFO').'</legend>'."\n";
			$html .= '<table cellspacing="10" cellpadding="0" class="table table-bordered rs_table">'."\n";
			$html .= '<tr>'."\n";
			$html .= '<td>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_TICKETS').'</td>'."\n";
			$html .= '<td>'."\n";
			$html .= implode('<br />', $info);
			$html .= '</td>'."\n";
			$html .= '</tr>'."\n";
			
			if (!empty($details->discount)) {
				$html .= '<tr>'."\n";
				$html .= '<td>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_TICKETS_DISCOUNT').'</td>'."\n";
				$html .= '<td>'.rseventsproHelper::currency($details->discount).'</td>'."\n";
				$html .= '</tr>'."\n";
			}
			
			if ($details->early_fee) {
				$html .= '<tr>'."\n";
				$html .= '<td>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_EARLY_FEE').'</td>'."\n";
				$html .= '<td>'."\n";
				$html .= rseventsproHelper::currency($details->early_fee);
				$html .= '</td>'."\n";
				$html .= '</tr>'."\n";
			}
			
			if ($details->late_fee) {
				$html .= '<tr>'."\n";
				$html .= '<td>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_LATE_FEE').'</td>'."\n";
				$html .= '<td>'."\n";
				$html .= rseventsproHelper::currency($details->late_fee);
				$html .= '</td>'."\n";
				$html .= '</tr>'."\n";
			}
			
			if (!empty($details->tax)) {
				$html .= '<tr>'."\n";
				$html .= '<td>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_TICKETS_TAX').'</td>'."\n";
				$html .= '<td>'.rseventsproHelper::currency($details->tax).'</td>'."\n";
				$html .= '</tr>'."\n";
			}
			
			$html .= '<tr>'."\n";
			$html .= '<td>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_TICKETS_TOTAL').'</td>'."\n";
			$html .= '<td>'.rseventsproHelper::currency($total).'</td>'."\n";
			$html .= '</tr>'."\n";
			
			$html .= '</table>'."\n";
			$html .= '</fieldset>'."\n";
			
			$html .= '<p style="margin: 10px;font-weight: bold;">'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_REDIRECTING').'</p>'."\n";
			
			$html .= '<form method="post" action="'.$paypal_url.'" id="paypalForm">'."\n";
			$html .= '<input type="hidden" name="business" value="'.$this->escape($paypal_email).'" />'."\n";
			$html .= '<input type="hidden" name="item_name" value="'.$this->escape(JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_ITEM_NAME')).'" />'."\n";
			$html .= '<input type="hidden" name="currency_code" value="'.$this->escape($currency).'" />'."\n";
			$html .= '<input type="hidden" name="cmd" value="_cart" />'."\n";
			$html .= '<input type="hidden" name="bn" value="RSJoomla_SP" />'."\n";
			$html .= '<input type="hidden" name="charset" value="utf-8" />'."\n";
			$html .= '<input type="hidden" name="notify_url" value="'.JURI::root().'index.php?paypalrsepro=1" />'."\n";
			$html .= '<input type="hidden" name="custom" value="'.$this->escape($details->verification).'" />'."\n";
			$html .= '<input type="hidden" name="lc" value="'.$this->escape($paypal_lang).'" />'."\n";
			$html .= '<input type="hidden" name="no_shipping" value="1">'."\n";
			$html .= '<input type="hidden" name="upload" value="1" />'."\n";
			
			$ticket_number = 1;
			
			if ($cart) {
				if ($info) {
					foreach ($info as $i => $ticket) {
						$ticket = str_replace('1 x ', '', $ticket);
						list($position, $price) = explode('-',$i);
						$html .= '<input type="hidden" name="item_name_'.$ticket_number.'" value="'.$this->escape(strip_tags($ticket)).'" />'."\n";
						$html .= '<input type="hidden" name="amount_'.$ticket_number.'" value="'.$this->convertprice($price).'" />'."\n";
						$html .= '<input type="hidden" name="quantity_'.$ticket_number.'" value="1" />'."\n";
						
						$ticket_number++;
					}
				}
			} else {
				$query->clear()
					->select($db->qn('name'))
					->from($db->qn('#__rseventspro_events'))
					->where($db->qn('id').' = '.(int) $details->ide);
				
				$db->setQuery($query);
				$event = $db->loadResult();
				
				foreach ($tickets as $ticket) {
					$html .= '<input type="hidden" name="item_name_'.$ticket_number.'" value="'.$this->escape($ticket->name.' '.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_FROM').' '.$event).'" />'."\n";
					$html .= '<input type="hidden" name="amount_'.$ticket_number.'" value="'.$this->convertprice($ticket->price).'" />'."\n";
					$html .= '<input type="hidden" name="quantity_'.$ticket_number.'" value="'.$this->escape($ticket->quantity).'" />'."\n";
					
					$ticket_number++;
				}
			}
			
			if ($details->late_fee) {
				$html .= '<input type="hidden" name="item_name_'.$ticket_number.'" value="'.$this->escape(JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_LATE_FEE')).'" />'."\n";
				$html .= '<input type="hidden" name="amount_'.$ticket_number.'" value="'.$this->convertprice($details->late_fee).'" />'."\n";
				$html .= '<input type="hidden" name="quantity_'.$ticket_number.'" value="1" />'."\n";
			}
			
			if (!empty($paypal_return))	$html .= '<input type="hidden" name="return" value="'.$this->escape($paypal_return).'" />'."\n";
			if (!empty($paypal_cancel))	$html .= '<input type="hidden" name="cancel_return" value="'.$this->escape($paypal_cancel).'" />'."\n";
			if (!empty($details->tax))	$html .= '<input type="hidden" name="tax_cart" value="'.$this->convertprice($details->tax).'" />'."\n";
			if (!empty($discount))		$html .= '<input type="hidden" name="discount_amount_cart" value="'.$this->convertprice($discount).'" />'."\n";
			
			$html .= '</form>'."\n";
			
			$html .= '<script type="text/javascript">'."\n";
			$html .= 'function paypalFormSubmit() { document.getElementById(\'paypalForm\').submit() }'."\n";
			$html .= 'try { window.addEventListener ? window.addEventListener("load",paypalFormSubmit,false) : window.attachEvent("onload",paypalFormSubmit); }'."\n";
			$html .= 'catch (err) { paypalFormSubmit(); }'."\n";
			$html .= '</script>'."\n";
			
			echo $html;			
		}
	}
	
	/*
	*	Process the form
	*/
	
	public function rsepro_processForm($vars) {
		// Can we run this plugin ?
		if (!$this->canRun()) {
			return;
		}
		
		// Get variables
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$log	= array();
		$params = array();
		$post	= $_POST;
		$custom = $post['custom'];
		$status	= $post['payment_status'];
		$amount	= $post['mc_gross'];

		// Exit if the custom variable is empty
		if (empty($custom)) {
			return;
		}
		
		// Get subscriber details
		$query->select($db->qn('id'))->select($db->qn('state'))->select($db->qn('gateway'))
			->select($db->qn('discount'))->select($db->qn('early_fee'))
			->select($db->qn('late_fee'))->select($db->qn('tax'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('verification').' = '.$db->q($custom));
		
		$db->setQuery($query);
		$subscriber = $db->loadObject();
		
		// The subscriber was not found
		if (empty($subscriber)) {
			return;
		}
		
		// Do not check for "Complete" subscription or if the gateway is not paypal
		if ($subscriber->state == 1 || $subscriber->gateway != $this->rsprooption) 
			return;
		
		// Get the PayPal check URL
		$url = $this->params->get('paypal_mode') ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$req = $this->_buildPostData();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));
		$res = curl_exec($ch);
		$errstr = curl_error($ch);
		curl_close($ch);
		
		$log[] = "Receiving a new transaction from Paypal.";
		
		if ($this->params->get('paypal_mode',0) == 0)
			$log[] = "Demo mode is on.";
		
		if ($res) {
			if (strcmp ($res, "VERIFIED") == 0) {
				$log[] = "PayPal reported a valid transaction.";
				$log[] = "Payment status is ".(!empty($status) ? $status : 'empty').".";
				
				// Make sure that the payment status is Completed
				if ($status == 'Completed' || ($this->params->get('paypal_mode') == 0)) {
					// Transaction params
					if ($post) {
						foreach($post as $key => $value) {
							$params[] = $db->escape($key.'='.$value);
						}
					}
					
					$params = is_array($params) ? implode("\n",$params) : '';
					
					$total = $this->getTotal($subscriber);
					$total = $this->convertprice($total);
					
					// If everything is OK , the mark the subscription as Complete
					if ((float) $total <= (float) $amount) {
						$query->clear()
							->update($db->qn('#__rseventspro_users'))
							->set($db->qn('state').' = 1')
							->set($db->qn('params').' = '.$db->q($params))
							->where($db->qn('id').' = '.(int) $subscriber->id);
						
						$db->setQuery($query);
						$db->execute();
						
						$log[] = "Successfully added the payment to the database.";
						
						//send the activation email
						require_once JPATH_SITE.'/components/com_rseventspro/helpers/emails.php';
						
						rseventsproHelper::confirm($subscriber->id);
						rseventsproHelper::savelog($log,$subscriber->id);
					} else {
						$currency = rseventsproHelper::getConfig('payment_currency');
						$log[] = "Expected an amount of $total $currency. PayPal reports this payment is $amount $currency. Stopping.";
						rseventsproHelper::savelog($log,$subscriber->id);
					}
				}
				else {
					// log for manual investigation
					$log[] = 'Paypal reported a transaction with the status of : '.$status;
					rseventsproHelper::savelog($log,$subscriber->id);
					return;
				}
			} elseif (strcmp($res, "INVALID") == 0) {
				// Invalid transaction
				$log[] = "Could not verify transaction authencity. PayPal said it's invalid.";
				$log[] = "String sent to PayPal is $req";
				rseventsproHelper::savelog($log,$subscriber->id);
			}
		} else {
			$log[] = "Could not connect to $url in order to verify this transaction. Error reported is: $errstr";
			rseventsproHelper::savelog($log,$subscriber->id);
		}
	}
	
	public function rsepro_tax($vars) {
		if (!$this->canRun()) 
			return;
		
		if (isset($vars['method']) && $vars['method'] == $this->rsprooption) {
			$total		= isset($vars['total']) ? $vars['total'] : 0;
			$tax_value	= $this->params->get('tax_value',0);
			$tax_type	= $this->params->get('tax_type',0);
			
			return rseventsproHelper::setTax($total,$tax_type,$tax_value);
		}
	}
	
	public function rsepro_info($vars) {
		if (!$this->canRun()) 
			return;
		
		if (isset($vars['method']) && $vars['method'] == $this->rsprooption) {
			$params	= array();
			$data	= $vars['data'];
			
			if (!empty($data)) {
				if (!is_array($data)) {
					$data	= explode("\n",$data);
					if (!empty($data)) {
						foreach ($data as $line) {
							$linearray = explode('=',$line);
							
							if (!empty($linearray))
								$params[trim($linearray[0])] = trim($linearray[1]);
						}
					}
				} else {
					$params = $data;
				}
				
				echo '<table width="100%" border="0" class="table table-striped adminform rs_table">';
				echo '<thead><tr><th colspan="2">'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_PAYMENT_DETAILS').'</th></tr></thead>';
				echo '<tr>';
				echo '<td width="25%" align="right"><b>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_TRANSACTION_ID').'</b></td>';
				echo '<td>'.$params['txn_id'].'</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td width="25%" align="right"><b>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_PAYER_NAME').'</b></td>';
				echo '<td>'.$params['first_name'].' '.$params['last_name'].'</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td width="25%" align="right"><b>'.JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_PAYER_EMAIL').'</b></td>';
				echo '<td>'.$params['payer_email'].'</td>';
				echo '</tr>';
				echo '</table>';
			}
		}
	}
	
	public function rsepro_name($vars) {
		if (!$this->canRun()) 
			return;
		
		if (isset($vars['gateway']) && $vars['gateway'] == $this->rsprooption) {
			return JText::_('COM_RSEVENTSPRO_PLG_PLUGIN_PAYPAL_NAME');
		}
	}
	
	protected function getTotal($subscriber) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$total	= 0;
		
		// Check if this subscription is from CART
		try {
			$query->select($db->qn('id'))->select($db->qn('total'))
				->from($db->qn('#__rseventspro_cart'))
				->where($db->qn('ids').' = '.$db->q($subscriber->id));
			$db->setQuery($query);
			if ($cart = $db->loadObject()) {
				return $cart->total;
			}			
		} catch (Exception $e) {
			
		}
		
		// We have a standard subscription
		$query->clear()
			->select($db->qn('t.price'))->select($db->qn('ut.quantity'))
			->from($db->qn('#__rseventspro_user_tickets','ut'))
			->join('left', $db->qn('#__rseventspro_tickets','t').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
			->where($db->qn('ut.ids').' = '.(int) $subscriber->id);
		
		$db->setQuery($query);
		$tickets = $db->loadObjectList();
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {				
				if ($ticket->price > 0)
					$total += ($ticket->quantity * $ticket->price);
			}
		}
		
		// check if the amount is correct
		if (!empty($subscriber->discount)) 
			$total = $total - $subscriber->discount; 
		
		if (!empty($subscriber->early_fee))
			$total = $total - $subscriber->early_fee;
		
		if (!empty($subscriber->late_fee))
			$total = $total + $subscriber->late_fee;
		
		if (!empty($subscriber->tax))
			$total = $total + $subscriber->tax;
		
		return $total;
	}
	
	protected function _buildPostData() {
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
			
		//reading raw POST data from input stream. reading post data from $_POST may cause serialization issues since POST data may contain arrays
		$raw_post_data = file_get_contents('php://input');
		if ($raw_post_data) {
			$raw_post_array = explode('&', $raw_post_data);
			$myPost = array();
			foreach ($raw_post_array as $keyval) {
				$keyval = explode ('=', $keyval);
				if (count($keyval) == 2) {
					$myPost[$keyval[0]] = urldecode($keyval[1]);
				}
			}
			
			$get_magic_quotes_exists 	= function_exists('get_magic_quotes_gpc');
			$get_magic_quotes_gpc 		= get_magic_quotes_gpc();
			
			foreach ($myPost as $key => $value) {
				if ($key == 'limit' || $key == 'limitstart' || $key == 'option') continue;
				
				if ($get_magic_quotes_exists && $get_magic_quotes_gpc) {
					$value = urlencode(stripslashes($value)); 
				} else {
					$value = urlencode($value);
				}
				$req .= "&$key=$value";
			}
		} else {
			// read the post from PayPal system
			$post = $_POST;
			foreach ($post as $key => $value) {
				if ($key == 'limit' || $key == 'limitstart' || $key == 'option') continue;
				
				$value = urlencode($value);
				$req .= "&$key=$value";
			}
		}
		
		return $req;
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'UTF-8');
	}
	
	protected function convertprice($price) {
		return number_format($price, 2, '.', '');
	}
	
	protected function stripHTML($info) {
		foreach ($info as $i => $ticket) {
			$info[$i] = strip_tags($ticket);
		}
		
		return $info;
	}
}