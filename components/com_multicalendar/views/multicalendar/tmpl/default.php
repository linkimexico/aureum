<?php
/**
* @Copyright Copyright (C) 2010 CodePeople, www.codepeople.net
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*
* This file is part of Multi Calendar for Joomla <www.joomlacalendars.com>.
*
* Multi Calendar for Joomla is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Multi Calendar for Joomla  is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Multi Calendar for Joomla.  If not, see <http://www.gnu.org/licenses/>.
*
**/

// no direct access

defined('_JEXEC') or die('Restricted access');


// Create shortcuts to some parameters.
//$params  = $this->item->params;
require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );
$mainframe = JFactory::getApplication();
$id = $this->params->get('the_calendar_id');
$container = "cdcmv".$id;
$language = $mainframe->getCfg('language');
$style = $this->params->get('cssStyle');
$views = $this->params->get('views');
$buttons = $this->params->get('buttons');
$edition = $this->params->get('edition');
$sample = $this->params->get('sample');
$otherparamsvalue = $this->params->get('otherparams');
$palette = $this->params->get('palette');
$viewdefault = $this->params->get('viewdefault');
$numberOfMonths = $this->params->get('numberOfMonths');
$start_weekday = $this->params->get("start_weekday");
$matches = array();
$msg = print_scripts($id,$container,$language,$style,$views,$buttons,$edition,$sample,$otherparamsvalue,$palette,$viewdefault,$numberOfMonths,$start_weekday,false,$matches);
?>
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>  
<?php echo $msg;?>
<script>var pfHeaderImgUrl = '';var pfHeaderTagline = '';var pfdisableClickToDel = 0;var pfHideImages = 0;var pfImageDisplayStyle = 'right';var pfDisablePDF = 1;var pfDisableEmail = 1;var pfDisablePrint = 0;var pfCustomCSS = '';var pfBtVersion='2';(function(){var js,pf;pf=document.createElement('script');pf.type='text/javascript';pf.src='//cdn.printfriendly.com/printfriendly.js';document.getElementsByTagName('body')[0].appendChild(pf)})();</script><a href="https://www.printfriendly.com" style="color:#6D9F00;text-decoration:none;" class="printfriendly" onclick="window.print();return false;" title="Printer Friendly and PDF"><img style="border:none;-webkit-box-shadow:none;box-shadow:none;" src="//cdn.printfriendly.com/buttons/print-button-gray.png" alt="Print Friendly and PDF"/></a>
<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php echo print_html($container);  ?>
</div>
<?php 
defined('JPATH_BASE') or die;
// Create a shortcut for params.
//$params = $displayData->params;
$app = JFactory::getApplication();
$params = $app->getParams();
//echo $params;
//$canEdit = $displayData['params']->get('access-edit');
//$canEdit = $displayData->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
$phocaPDF = false;
//if (JPluginHelper::isEnabled('phocapdf', 'content')) {
	include_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdf.php');
$phocaPDF = PhocaPDFHelper::getPhocaPDFContentIcon($params['views'], $params);
//$phocaPDF = PhocaPDFHelper::getPhocaPDFContentIcon($params['item'], $params['params']);
//	$phocaPDF = PhocaPDFHelper::getPhocaPDFContentIcon('pdf', '');
	//echo $phocaPDF;
//} 
?>
		<script type="text/javascript">
			

var $=jQuery.noConflict();
			
				$( document ).ready(function() {
				//alert("dd");
					$("#Rat").hide();
					
					$(function() {
    $(".Rat").click(function(){        
      $('#Rat').modal('show');
    });
});
					
 				$("#container_160").hide();
				$("#container_161").hide();
				$("#container_162").hide();
				$( '.Choques').click(function(e) {
					$("#container_160").toggle('slow');
				});
					$( '.Oficiales').click(function(e) {
					$("#container_161").toggle('slow');
				});
 			$( '.Afinidades').click(function(e) {
					$("#container_162").toggle('slow');
				});
				 var thisSelect = $('#txtdatetimeshowcdcmv'+<?php echo $id?>).text();
				// var thisSelect = $('#txtdatetimeshowcal'+<?php echo $id?>).text();
				 var mes_actual='';
				  mes_actual = thisSelect.split(" "); 
				 var mes_act='';
				 $( "#multicalendar" ).removeAttr('class');
				 mes_act=mes_actual[0];
				 $( "#multicalendar" ).addClass( mes_act );
				 
                   var url="http://linki.mx/aureum/mes.php";
				   $(".mesactual").html("");
				   $.getJSON(url,{nombre:thisSelect },function(meses){
					   $.each(meses, function(i,meses){
						  var newRow ='';
							$(".m3s").remove();
							var newRow="<div class='m3s'><img src='http://linki.mx/aureum/images/simbolos/"+meses.nombre+meses.elemento+".svg' alt='"+meses.nombre+"'>"
							//+"  <span>"+meses.elemento
							//+"</span> 
	+"</div>";
							$(newRow).appendTo(".mesactual");
						     var texto="<div class='cambio'>Inicio mes "+meses.nombre+" "+meses.elemento+"</div>";
						   $( ".cambio" ).replaceWith( texto);
				 		});	
					});	
				 						
				$( '.fprevbtn').click(function(e) {
	
  				   var thisSelect = $('#txtdatetimeshowcdcmv'+<?php echo $id?>).text();
                   var url="http://linki.mx/aureum/mes.php";
				 var mes_actual='';
				  mes_actual = thisSelect.split(" "); 
				 var mes_act='';
 				$( "#multicalendar" ).removeAttr('class');
				 mes_act=mes_actual[0];
				 $( "#multicalendar" ).addClass( mes_act );
				 
				   $(".mesactual").html("");
				   $.getJSON(url,{nombre:thisSelect },function(meses){
					   $.each(meses, function(i,meses){
						  var newRow ='';
							$(".m3s").remove();
							var newRow="<div class='m3s'><img src='http://linki.mx/aureum/images/simbolos/"+meses.nombre+meses.elemento+".svg' alt='"+meses.nombre+"'>"
							//+"  <span>"+meses.elemento
							//+"</span> 
	+"</div>";
							$(newRow).appendTo(".mesactual");
						     var texto="<div class='cambio'>Inicio mes "+meses.nombre+" "+meses.elemento+"</div>";
						   $( ".cambio" ).replaceWith( texto);
				 		});	
					});	
					
					
				});
				
				$( '.ui-state-default').click(function(e) {
	
  				   var thisSelect = $('#txtdatetimeshowcdcmv'+<?php echo $id?>).text();
                   var url="http://linki.mx/aureum/mes.php";
				   var mes_actual='';
				  mes_actual = thisSelect.split(" "); 
  				$( "#multicalendar" ).removeAttr('class');
				 var mes_act='';
				 mes_act=mes_actual[0];
				 $( "#multicalendar" ).addClass( mes_act );
				 
				   $(".mesactual").html("");
				   $.getJSON(url,{nombre:thisSelect },function(meses){
					   $.each(meses, function(i,meses){
						  var newRow ='';
							$(".m3s").remove();
							var newRow="<div class='m3s'><img src='http://linki.mx/aureum/images/simbolos/"+meses.nombre+meses.elemento+".svg' alt='"+meses.nombre+"'>"
							//+"  <span>"+meses.elemento
							//+"</span> 
	+"</div>";
							$(newRow).appendTo(".mesactual");
						     var texto="<div class='cambio'>Inicio mes "+meses.nombre+" "+meses.elemento+"</div>";
						   $( ".cambio" ).replaceWith( texto);
				 		});	
					});	
					
					
				});
});
$( "td.Rat" ).hover(
  function() {
    $( this ).append( $( "<span> ***</span>" ) );
  }, function() {
    $( this ).find( "span" ).last().remove();
  }
);

</script>
<div class="Rat" id="Rat">
									
					Amiga del Buey
Trina con Dragón y Mono
Choca con Caballo
Daño para Oveja

				</div>
