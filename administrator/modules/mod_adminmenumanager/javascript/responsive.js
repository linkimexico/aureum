// JavaScript Document

function amm_showhide(element){	
	var JNC_jQuery = jQuery.noConflict();	
	el_parent = JNC_jQuery(element).parent();	
	grandparent = JNC_jQuery(el_parent).parent();
	sibling = JNC_jQuery(grandparent).next();	
	current_state = sibling.css("display");		
	if(current_state=='none'){
		JNC_jQuery(sibling).css("display","block");			
		JNC_jQuery(element).removeClass("amm_open").addClass("amm_close");
	}else{
		JNC_jQuery(sibling).css("display","none");		
		JNC_jQuery(element).removeClass("amm_close").addClass("amm_open");
	}
}