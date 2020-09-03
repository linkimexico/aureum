// JavaScript Document

jQuery(
	function($){		
		height = $('.navbar-fixed-top').height();		
		if(height>35){
			//$('.header').css("display","none");
			margin = height-30;
			$('.header').css("margin-top",margin+"px");
			$('.subhead').css("margin-top",margin+"px");
		}
		
		$(".adminmenum_menu li").hover(
			function(){
				$('#menu li').removeClass("open");
			}  
		);
	}
);