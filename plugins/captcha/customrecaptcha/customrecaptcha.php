<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Custom Recaptcha Plugin
 */
class PlgCaptchaCustomRecaptcha extends JPlugin
{
	const RECAPTCHA_API_SERVER = "http://www.google.com/recaptcha/api";
	const RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api";
	const RECAPTCHA_VERIFY_SERVER = "api-verify.recaptcha.net";

	protected $autoloadLanguage = true;

	/**
	 * Reports the privacy related capabilities for this plugin to site administrators.
	 *
	 * @return  array
	 *
	 * @since   3.9.0
	 */
	public function onPrivacyCollectAdminCapabilities()
	{
	    $this->loadLanguage();

	    return array(
	        JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA') => array(
	            JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_PRIVACY_CAPABILITY_IP_ADDRESS'),
	        )
	    );
	}

	/**
	 * Initialise the captcha
	 *
	 * @param   string	$id	The id of the field
	 * @return  Boolean	true on success, false otherwise
	 * @throws  Exception
	 */
	public function onInit($id = 'recaptcha_widget')
	{
		$pubkey = trim($this->params->get('public_key', ''));

		if ($this->params->get('test_mode', 0)) {
			$pubkey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
		}

		if ($pubkey == '') {
			throw new Exception(JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_ERROR_NO_PUBLIC_KEY'));
			return false;
		}

		$doc = JFactory::getDocument();

		$minified = (JDEBUG) ? '' : '.min';

		$styles = '';

		$version = $this->params->get('version', 'v1'); // keep v1 as default for backward compatibility

		if ($version == 'v1') { // reCAPTCHA v1

			$doc->addScriptDeclaration('var RecaptchaOptions = { '.$this->_getCustomTranslations().' theme: \'custom\', custom_theme_widget: \''.$id.'\' };');

			// add styles

			$styles .= trim($this->params->get('styles', ''));

		} else if ($version == 'v2') { // reCAPTCHA v2

			$force_fallback_param = '';
			if ($this->params->get('force_fallback', 0)) {
				$force_fallback_param = 'fallback=true&';
			}

			// load callback first for browser compatibility
			if (version_compare(JVERSION, '3.2', 'lt')) {
			    $doc->addScript(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchav2'.$minified.'.js');
			} else {
			    $doc->addScriptVersion(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchav2'.$minified.'.js');
			}

			$doc->addScript('https://www.google.com/recaptcha/api.js?'.$force_fallback_param.'onload=onloadCallbackCustomReCaptcha&render=explicit&hl='.$this->_getLanguageTag(), 'text/javascript', true, true);

			// add styles

			$styles .= trim($this->params->get('styles_v2', ''));

			if ($this->params->get('size', 'normal') == 'normal' && $this->params->get('make_responsive_v2', 0)) {

				// great solution but breaks challenges, if any

// 				$doc->addStylesheet('https://fonts.googleapis.com/css?family=Roboto');

// 				$bg_color = '#f9f9f9';
// 				$font_color = '#9b9b9b';
// 				if ($this->params->get('theme', 'light') == 'dark') {
// 					$bg_color = '#222';
// 				}

// 				$border = ' border: 1px solid #d3d3d3; border-radius: 3px;';

// 				$wrapper_width = ' max-width:100%;';

// 				$adjust_width = 0;
// 				$logo_cover_width = 70 + $adjust_width;

// 				$styles .= '.recaptcha-wrap { position: relative; min-height: 70px; overflow: hidden; background-color: '.$bg_color.';'.$border.$wrapper_width.' } ';

// 				if ($doc->getDirection() == 'rtl') {
// 					$styles .= '.g-recaptcha { position: absolute; top: 0; right: 0; margin: -2px -2px -10px -2px; overflow: hidden; } ';
// 					$styles .= '.g-recaptcha:after { content:""; display: block; background-color:'.$bg_color.'; position: absolute; height: 70px; width: '.$logo_cover_width.'px; top: 2px; left: 0; } ';
// 					$styles .= '.recaptcha-wrap-logobloc { position: absolute; height: 70px; width: '.$logo_cover_width.'px; top: 0; left: 0; text-align: center; } ';
// 					$styles .= '.recaptcha-wrap-logo { position: absolute; height: 32px; width: 32px; top: 9px; left: '.(intval(($logo_cover_width - 4 - 32) / 2) + 4).'px; background-image: url(https://www.gstatic.com/recaptcha/api2/logo_48.png); background-size: 32px; background-repeat: no-repeat } ';
// 					$styles .= '.recaptcha-wrap-title { color: '.$font_color.'; cursor: default; font-family: "Roboto",helvetica,arial,sans-serif; font-size: 10px; font-weight: 400; line-height: 10px; margin-top: 5px; position: absolute; width: '.($logo_cover_width - 4).'px; left: 4px; top: 39px; } ';
// 					$styles .= '.recaptcha-wrap-links { font-family: "Roboto",helvetica,arial,sans-serif; font-size: 8px; font-weight: 400; line-height: 10px; position: absolute; width: '.($logo_cover_width - 4).'px; left: 4px; top: 55px; } ';
// 				} else {
// 					$styles .= '.g-recaptcha { position: absolute; top: 0; left: 0; margin: -2px -2px -10px -2px; overflow: hidden; } ';
// 					$styles .= '.g-recaptcha:after { content:""; display: block; background-color:'.$bg_color.'; position: absolute; height: 70px; width: '.$logo_cover_width.'px; top: 2px; right: 0; } ';
// 					$styles .= '.recaptcha-wrap-logobloc { position: absolute; height: 70px; width: '.$logo_cover_width.'px; top: 0; right: 0; text-align: center; } ';
// 					$styles .= '.recaptcha-wrap-logo { position: absolute; height: 32px; width: 32px; top: 9px; right: '.(intval(($logo_cover_width - 4 - 32) / 2) + 4).'px; background-image: url(https://www.gstatic.com/recaptcha/api2/logo_48.png); background-size: 32px; background-repeat: no-repeat } ';
// 					$styles .= '.recaptcha-wrap-title { color: '.$font_color.'; cursor: default; font-family: "Roboto",helvetica,arial,sans-serif; font-size: 10px; font-weight: 400; line-height: 10px; margin-top: 5px; position: absolute; width: '.($logo_cover_width - 4).'px; right: 4px; top: 39px; } ';
// 					$styles .= '.recaptcha-wrap-links { font-family: "Roboto",helvetica,arial,sans-serif; font-size: 8px; font-weight: 400; line-height: 10px; position: absolute; width: '.($logo_cover_width - 4).'px; right: 4px; top: 55px; } ';
// 				}

// 				$styles .= '.recaptcha-wrap-links a { color: '.$font_color.'; text-decoration: none; } ';
// 				$styles .= '.recaptcha-wrap-links a:hover { color: '.$font_color.'; text-decoration: underline; } ';

				// transform solution, when needed

// 				$styles .= ' @media screen and (max-height: 575px) { .g-recaptcha { -webkit-transform: scale(0.77); -webkit-transform-origin: 0 0; transform: scale(0.77); transform-origin: 0 0; } } ';
// 				$styles .= ' @media screen and (max-width: 640px) { .g-recaptcha { -webkit-transform: scale(0.77); -webkit-transform-origin: 0 0; transform: scale(0.77); transform-origin: 0 0; } } ';

				if (version_compare(JVERSION, '3.2', 'lt')) {
				    $doc->addScript(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchav2responsive'.$minified.'.js');
				} else {
				    $doc->addScriptVersion(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchav2responsive'.$minified.'.js');
				}
			}

		} else { // invisible reCAPTCHA

			// load callback first for browser compatibility
		    if (version_compare(JVERSION, '3.2', 'lt')) {
		        $doc->addScript(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchainvisible'.$minified.'.js');
		    } else {
		        $doc->addScriptVersion(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchainvisible'.$minified.'.js');
		    }

			$doc->addScript('https://www.google.com/recaptcha/api.js?onload=onloadCallbackCustomReCaptchaInvisible&render=explicit&hl='.$this->_getLanguageTag(), 'text/javascript', true, true);

			// add styles

			$styles .= trim($this->params->get('styles_invisible', ''));

			if ($this->params->get('badge', 'bottomright') == 'inline' && $this->params->get('make_responsive_invisible', 0)) {

				if (version_compare(JVERSION, '3.2', 'lt')) {
				    $doc->addScript(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchav2responsive'.$minified.'.js');
				} else {
				    $doc->addScriptVersion(JURI::root(true).'/media/syw_customrecaptcha/js/recaptchav2responsive'.$minified.'.js');
				}
			}

			// if badge position is bottom left or right - done in external script file with the DOM to avoid jQuery
			//JHtml::_('jquery.framework');
			//$doc->addScriptDeclaration('jQuery(document).ready(function($) { var bloc = $(".g-recaptcha").parent().parent(); bloc.css("margin", "0"); bloc.find(".control-label").hide(); });');
		}

		if (!empty($styles)) {
			$styles = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $styles); // minify the CSS code
			$doc->addStyleDeclaration($styles);
		}

		return true;
	}

	/**
	 * Gets the challenge HTML
	 *
	 * @param   string  $name   The name of the field. Not Used.
	 * @param   string  $id     The id of the field
	 * @param   string  $class  The class of the field. e.g. 'class="required"'; in Joomla 3.9+ 'required'
	 * @return  string  The HTML to be embedded in the form
	 */
	public function onDisplay($name = null, $id = 'recaptcha_widget', $class = '')
	{
		$pubkey = trim($this->params->get('public_key', ''));

		if ($this->params->get('test_mode', 0)) {
			$pubkey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
		}

		if ($pubkey == '') {
			return '';
		}

		$server = self::RECAPTCHA_API_SERVER;
		if (JFactory::getApplication()->isSSLConnection()) {
			$server = self::RECAPTCHA_API_SECURE_SERVER;
		}

		$html = '';

		$version = $this->params->get('version', 'v1'); // keep v1 as default for backward compatibility

		if ($version == 'v1') { // reCAPTCHA v1

			$layout = $this->params->get('layout', 'default').'.php';

			$classes = trim($this->params->get('classes', ''));
			if ($classes) {
				$classes .= ' ';
			}

			$classes .= trim(rtrim(str_replace('class="', '', $class), '"'));
			if ($classes) {
				$classes = ' '.$classes;
			}

			$variables = array('id' => $id, 'classes' => $classes, 'language_tag' => $this->_getLanguageTag());

			$html .= $this->_loadTemplate($layout, $variables);

			$html .= '<script type="text/javascript" src="'.$server.'/challenge?k='.$pubkey.'"></script>';
	 		$html .= '<noscript>';
	   		$html .= '<iframe src="'.$server.'/noscript?k='.$pubkey.'" height="300" width="500" frameborder="0"></iframe><br />';
	   		$html .= '<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>';
	   		$html .= '<input type="hidden" name="recaptcha_response_field" value="manual_challenge">';
	 		$html .= '</noscript>';

		} else if ($version == 'v2') { // reCAPTCHA v2

			$classes = trim($this->params->get('classes_v2', ''));
			if ($classes) {
				$classes = ' '.$classes;
			}

			// remove the class attribute, if it exists (necessary because behavior changed in Joomla 3.9)
			$class = trim(rtrim(str_replace('class="', '', $class), '"'));

			$html .= '<div class="g-recaptcha-wrapper'.$classes.'">';

			$html .= '<div id="'.$id
				.'" class="'.(($class == '') ? 'g-recaptcha' : ($class.' g-recaptcha'))
				.'" data-sitekey="'.$pubkey
				.'" data-theme="'.$this->params->get('theme', 'light')
				.'" data-size="'.$this->params->get('size', 'normal')
				.'" data-type="'.$this->params->get('type', 'image')
				.'" data-tabindex="'.$this->params->get('tabindex', '0')
				.'" data-callback="'.$this->params->get('callback', '')
				.'" data-expired-callback="'.$this->params->get('expired_callback', '')
				.'" data-error-callback="'.$this->params->get('error_callback', '')
				.'"></div>';

			// needs to be immediately after g-recaptcha
			$html .= '<noscript>';
			$html .= '<div>';
			$html .= '<div style="width: 302px; height: 422px; position: relative;">';
			$html .= '<div style="width: 302px; height: 422px; position: absolute;">';
			$html .= '<iframe src="https://www.google.com/recaptcha/api/fallback?k='.$pubkey.'" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div style="width: 300px; height: 60px; border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">';
			$html .= '<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;"></textarea>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</noscript>';

			// great solution but breaks challenges, if any

// 			if ($this->params->get('size', 'normal') == 'normal' && $this->params->get('make_responsive_v2', 0)) {

// 				$html .= '<div style="clear:both;"></div>';

// 				$html .= '<div class="recaptcha-wrap-logobloc">';
// 				$html .= '<div class="recaptcha-wrap-logo"></div>';
// 				$html .= '<div class="recaptcha-wrap-title">reCAPTCHA</div>';
// 				$html .= '<div class="recaptcha-wrap-links">';
// 				$html .= '<a href="https://www.google.com/intl/'.$this->_getLanguageTag().'/policies/privacy/" target="_blank">'.JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_PRIVACY').'</a><span aria-hidden="true" role="presentation"> - </span><a href="https://www.google.com/intl/'.$this->_getLanguageTag().'/policies/terms/" target="_blank">'.JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_TERMS').'</a>';
// 				$html .= '</div>';
// 				$html .= '</div>';
// 			}

			$html .= '</div>';

		} else { // invisible reCAPTCHA

			$legal_info = 'invisible';
			if ($this->params->get('add_legal', 0)) {
				$legal_info = 'visible';
			}

			$classes = trim($this->params->get('classes_invisible', ''));
			if ($classes) {
				$classes = ' '.$classes;
			}

			// remove the class attribute, if it exists (necessary because behavior changed in Joomla 3.9)
			$class = trim(rtrim(str_replace('class="', '', $class), '"'));

			$html .= '<div class="g-recaptcha-wrapper'.$classes.'">';

			$html .= '<div id="'.$id
                .'" class="'.(($class == '') ? 'g-recaptcha' : ($class . ' g-recaptcha'))
				.'" data-sitekey="'.$pubkey
				.'" data-badge="'.$this->params->get('badge', 'bottomright')
				.'" data-size="invisible'
				.'" data-tabindex="'.$this->params->get('tabindex_invisible', '0')
				.'" data-callback="'.$this->params->get('callback_invisible', 'submitCustomReCaptchaInvisible')
			    .'" data-expired-callback="'.$this->params->get('expired_callback_invisible', '')
			    .'" data-error-callback="'.$this->params->get('error_callback_invisible', '')
				.'" data-legal="'.$legal_info
				.'"></div>';

			if ($this->params->get('add_legal', 0)) {
				$html .= '<div class="legal">'.JText::sprintf('PLG_CAPTCHA_CUSTOMRECAPTCHA_INFO_LEGAL', 'https://www.google.com/intl/'.$this->_getLanguageTag().'/policies/').'</div>';
			}

			$html .= '</div>';
		}

		return $html;
	}

	/**
	  * Calls an HTTP POST function to verify if the user's guess was correct
	  *
	  * @param   string  $code  Answer provided by user
	  * @return  true if the answer is correct, false otherwise
	  */
	public function onCheckAnswer($code = null)
	{
		$input = JFactory::getApplication()->input;

		$privatekey = trim($this->params->get('private_key'));

		if ($this->params->get('test_mode', 0)) {
			$privatekey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
		}

		$version = $this->params->get('version', 'v1'); // keep v1 as default for backward compatibility

		$remoteip = $input->server->get('REMOTE_ADDR', '', 'string');

		if ($version == 'v1') { // reCAPTCHA v1

			$challenge = $input->get('recaptcha_challenge_field', '', 'string');
			$response = $code ? $code : $input->get('recaptcha_response_field', '', 'string');
			$spam = ($challenge === '' || $response === '');

		} else { // reCAPTCHA v2 and invisible

			// challenge not needed in 2.0 but needed for getResponse call
			$challenge = null;
			$response = $code ? $code : $input->get('g-recaptcha-response', '', 'string');
			$spam = ($response === '');
		}

		// Check for Private Key
		if (empty($privatekey)) {
			$this->_subject->setError(JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_ERROR_NO_PRIVATE_KEY'));
			return false;
		}

		// Check for IP
		if (empty($remoteip)) {
			$this->_subject->setError(JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_ERROR_NO_IP'));
			return false;
		}

		// Discard spam submissions
		if ($spam) {
			$this->_subject->setError(JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_ERROR_EMPTY_SOLUTION'));
			return false;
		}

		return $this->getResponse($privatekey, $remoteip, $response, $challenge);
	}

	/**
	 * Get the reCaptcha response.
	 *
	 * @param   string  $privatekey  The private key for authentication.
	 * @param   string  $remoteip    The remote IP of the visitor.
	 * @param   string  $response    The response received from Google.
	 * @param   string  $challenge   The challenge field from the reCaptcha. Only for 1.0.
	 * @return  bool True if response is good | False if response is bad
	 */
	private function getResponse($privatekey, $remoteip, $response, $challenge = null)
	{
		$version = $this->params->get('version', '1.0');

		if ($version == 'v1') { // reCAPTCHA v1

			$response = $this->_recaptcha_http_post(
				self::RECAPTCHA_VERIFY_SERVER, "/verify",
				//'www.google.com', '/recaptcha/api/verify',
				array(
					'privatekey' => $privatekey,
					'remoteip'   => $remoteip,
					'challenge'  => $challenge,
					'response'   => $response
				)
			);

			$answers = explode("\n", $response[1]);

			if (trim($answers[0]) !== 'true') {
				// @todo use exceptions here
				$this->_subject->setError(JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_ERROR_'.strtoupper(str_replace('-', '_', $answers[1]))));
				return false;
			}

		} else { // reCAPTCHA v2 and invisible

			require_once 'recaptchalib.php';

			$reCaptcha = new JReCaptcha($privatekey);
			$response = $reCaptcha->verifyResponse($remoteip, $response);

			if (!isset($response->success) || !$response->success) {
				// @todo use exceptions here
				if (is_array($response->errorCodes)) {
					foreach ($response->errorCodes as $error) {
						$this->_subject->setError($error);
					}
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * Encodes the given data into a query string format.
	 *
	 * @param   array  $data  Array of string elements to be encoded
	 * @return  string  Encoded request
	 */
	private function _recaptcha_qsencode($data)
	{
		$req = '';

		foreach ($data as $key => $value) {
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req = rtrim($req, '&');

		return $req;
	}

	/**
	 * Submits an HTTP POST to a reCAPTCHA server.
	 *
	 * @param   string  $host  Host name to POST to.
	 * @param   string  $path  Path on host to POST to.
	 * @param   array   $data  Data to be POSTed.
	 * @param   int     $port  Optional port number on host.
	 * @return  array   Response
	 */
	private function _recaptcha_http_post($host, $path, $data, $port = 80)
	{
		$req = $this->_recaptcha_qsencode($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if (($fs = @fsockopen($host, $port, $errno, $errstr, 10)) === false ) {
			die('Could not open socket');
		}

		fwrite($fs, $http_request);

		while (!feof($fs)) {
			// One TCP-IP packet
			$response .= fgets($fs, 1160);
		}

		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}

	/**
	 * Get the custom translation for widget v1
	 * The widget will be translated with all language files available by the plugin
	 * Note: audio is always in English
	 *
	 * @return  string
	 */
	private function _getCustomTranslations()
	{
		$language = JFactory::getLanguage();

		$tag = explode('-', $language->getTag());

		//$supported_languages = array('en', 'pt', 'fr', 'de', 'nl', 'ru', 'es', 'tr'); // available by Google

		$custom = array();

		// remaining translations that are created by the widget and that there is no handle on
// 			$custom[] = 'custom_translations : {';
// 			$custom[] = "\t".'instructions_audio : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_INSTRUCTIONS_AUDIO') . '",';
// 			$custom[] = "\t".'play_again : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_PLAY_AGAIN') . '",';
// 			$custom[] = "\t".'cant_hear_this : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_CANT_HEAR_THIS') . '",';
// 			$custom[] = "\t".'audio_challenge : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_AUDIO_CHALLENGE') . '",';
// 			$custom[] = "\t".'image_alt_text : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_IMAGE_ALT_TEXT') . '"';
// 			$custom[] = '},';

		// set language keys for all languages, supported or not
		// it allows for language overrides

		// if the language is not available or if the translation is set to be overridden, add a custom translation
// 			if (!in_array($tag, $available) || $this->params->get('override_language', 0)) {
		$custom[] = 'custom_translations : {';
		$custom[] = "\t".'instructions_visual : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_INSTRUCTIONS_VISUAL') . '",';
		$custom[] = "\t".'instructions_audio : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_INSTRUCTIONS_AUDIO') . '",';
		$custom[] = "\t".'play_again : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_PLAY_AGAIN') . '",';
		$custom[] = "\t".'cant_hear_this : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_CANT_HEAR_THIS') . '",';
		$custom[] = "\t".'visual_challenge : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_VISUAL_CHALLENGE') . '",';
		$custom[] = "\t".'audio_challenge : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_AUDIO_CHALLENGE') . '",';
		$custom[] = "\t".'refresh_btn : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_REFRESH_BTN') . '",';
		$custom[] = "\t".'help_btn : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_HELP_BTN') . '",';
		$custom[] = "\t".'incorrect_try_again : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_INCORRECT_TRY_AGAIN') . '",';
		$custom[] = "\t".'image_alt_text : "' . JText::_('PLG_CAPTCHA_CUSTOMRECAPTCHA_IMAGE_ALT_TEXT') . '"';
		$custom[] = '},';
// 			}
		$custom[] = "lang : '".$tag[0]."',";

		return implode("\n", $custom);
	}

	/**
	 * Get the language tag for the online policies (all versions) and the widget v2
	 *
	 * @return  string
	 */
	private function _getLanguageTag()
	{
		$language = JFactory::getLanguage();

		$supported_languages_fulltags = array('zh-HK', 'zh-CN', 'zh-TW', 'en-GB', 'fr-CA', 'de-AT', 'de-CH', 'pt-BR', 'pt-PT'); // available by Google

		if (in_array($language->getTag(), $supported_languages_fulltags)) {
			return $language->getTag();
		}

		// TODO check Google codes periodically to see if new ones are listed
		$supported_languages_halftags = array('ar', 'af', 'am', 'hy', 'az', 'eu', 'bn', 'bg', 'ca', 'hr', 'cs', 'da', 'nl', 'en', 'et', 'fil', 'fi', 'fr', 'gl', 'ka', 'de', 'el', 'gu', 'iw', 'hi', 'hu', 'is', 'id', 'it', 'ja', 'kn', 'ko', 'lo', 'lv', 'lt', 'ms', 'ml', 'mr', 'mn', 'no', 'fa', 'pl', 'pt', 'ro', 'ru', 'sr', 'si', 'sk', 'sl', 'es', 'sw', 'sv', 'ta', 'te', 'th', 'tr', 'uk', 'ur', 'vi', 'zu');

		$tag = explode('-', $language->getTag());

		if (in_array($tag[0], $supported_languages_halftags)) {
			return $tag[0];
		}

		return 'en-GB'; // TODO give users the choice of the default language
	}

	/**
	 * load the template for widget v1
	 *
	 * @return  string
	 */
	private function _loadTemplate($file = null, $variables = array())
	{
		$template = JFactory::getApplication()->getTemplate();
		$overridePath = JPATH_THEMES.'/'.$template.'/html/plg_captcha_customrecaptcha';

		if (is_file($overridePath.'/'.$file)) {
			$file = $overridePath.'/'.$file;
		} else {
			$file = __DIR__.'/tmpl/'.$file;
		}

		unset($template);
		unset($overridePath);

		if (!empty($variables)) {
			foreach ($variables as $name => $value) {
				$$name = $value;
			}
		}

		unset($variables);
		unset($name);
		unset($value);
		if (isset($this->this)) {
			unset($this->this);
		}

		@ob_start();
		include $file;
		$html = ob_get_contents();
		@ob_end_clean();

		return $html;
	}

}
