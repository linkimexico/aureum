var popUpWin=0;
function amm_pop_centered(url, width, height){	
	var winl = (screen.width - width) / 2;
	var wint = (screen.height - height) / 2;
	winprops = 'height='+height+', width='+width+', top='+wint+', left='+winl+', scrollbars=0, resizable';
	popUpWin = open(url, 'popUpWin', winprops);	
	popUpWin.window.focus();	
}