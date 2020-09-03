/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

window.onloadCallbackCustomReCaptcha = function() {
	'use strict';
	
	var items = document.getElementsByClassName('g-recaptcha'), item, options;
	for (var i = 0, l = items.length; i < l; i++) {
		item = items[i];
		options = item.dataset ? item.dataset : { 
			'sitekey': item.getAttribute('data-sitekey'),
			'theme': item.getAttribute('data-theme'),
			'size': item.getAttribute('data-size'),
			'type': item.getAttribute('data-type'),
			'tabindex': item.getAttribute('data-tabindex'),
			'callback': item.getAttribute('data-callback'),
			'expired-callback': item.getAttribute('data-expired-callback'),
			'error-callback': item.getAttribute('data-error-callback')
		};
		grecaptcha.render(item, options);
	}
}