/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

window.submitCustomReCaptchaInvisible = function(token) {
	'use strict';
	
	window.sentForm.submit();
	delete window.sentForm;
}

window.onloadCallbackCustomReCaptchaInvisible = function() {
	'use strict';

	var items = document.getElementsByClassName('g-recaptcha'), item, options, element;
	for (var i = 0, l = items.length; i < l; i++) {
		item = items[i];
		options = item.dataset ? item.dataset : {
			'sitekey': item.getAttribute('data-sitekey'),
			'badge' : item.getAttribute('data-badge'),
			'size': item.getAttribute('data-size'),
			'tabindex': item.getAttribute('data-tabindex'),
			'callback' : item.getAttribute('data-callback'),
			'expired-callback': item.getAttribute('data-expired-callback'),
			'error-callback': item.getAttribute('data-error-callback')
		};
		var widgetId = grecaptcha.render(item, options);
		if (widgetId !== '') {
			grecaptcha.reset(widgetId);
			element = item; 
			
			if (item.getAttribute('data-legal') == 'invisible' && (item.getAttribute('data-badge') == 'bottomright' || item.getAttribute('data-badge') == 'bottomleft')) {
				var bloc = element.parentNode.parentNode.parentNode;
				if (bloc.getAttribute('class').indexOf('control-group') != -1) {
					bloc.style.margin = '0';
					if (bloc.hasChildNodes()) {				
						bloc.children[0].style.display = 'none';
					}
				}
			}
			
			do {
				element = element.parentNode;
			} while (element.nodeName != 'FORM');
			element.addEventListener('submit', function(event) {
				event.preventDefault();
				grecaptcha.execute(widgetId);
				window.sentForm = this;
			});
		}
	}
}
