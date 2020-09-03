/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

document.addEventListener("DOMContentLoaded", function() {
	
	var all_recaptchas = document.getElementsByClassName('g-recaptcha');	
	for (var i = 0, l = all_recaptchas.length; i < l; i++) {		
		recaptcha = all_recaptchas[i];					
		var direction = (document.dir != undefined)? document.dir : document.getElementsByTagName("html")[0].getAttribute("dir");	
		if (direction == "rtl") {
			recaptcha.style.transformOrigin = "top right";
			recaptcha.style.msTransformOrigin = "top right";
		} else {
			recaptcha.style.transformOrigin = "0 0";
			recaptcha.style.msTransformOrigin = "0 0";
		}		
		scaleCaptcha(recaptcha);
	}
	
	function scaleCaptcha(elt) {				
		var the_form = closestRecaptchaForm(elt);
		if (the_form !== null) {
			var parent = the_form.querySelector(".controls"); // gets the first .controls element of the form containg the widget
			if (parent !== null) {			
				var widget_width = 304; // should get width of child instead in case the widget changes in size
				if (elt.getAttribute("data-size") === "invisible") { // invisible captcha
					widget_width = 266; // 256 + extra shadow
					elt.style.margin = "0 3px";
				}				
				var scale = 1;						
				if (parent.clientWidth < widget_width) {
					scale = parent.clientWidth / widget_width;
				}				
				if (scale > 0) {
					elt.style.transform = "scale(" + scale + ")"; 
					elt.style.msTransform = "scale(" + scale + ")"; 
				}
			}
		}
	}	
	
	var resizeRecaptchasCheck;
	
	function scaleRecaptchas() {
		clearInterval(resizeRecaptchasCheck);
		resizeRecaptchasCheck = setInterval(function() { // to limit strain on browser
            clearInterval(resizeRecaptchasCheck);            
            var all_recaptchas = document.getElementsByClassName('g-recaptcha');
            for (var i = 0, l = all_recaptchas.length; i < l; i++) { 
            	scaleCaptcha(all_recaptchas[i]);
            }			
        }, 50);
	}
	
	window.addEventListener("resize", scaleRecaptchas);
	
	function closestRecaptchaForm(elt) {
	    while (elt.tagName != "FORM") { // tagName is always uppercased
	        elt = elt.parentNode;
	        if (!elt) {
	            return null;
	        }
	    }
	    return elt;
	}
});
