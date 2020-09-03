<?php
defined('_JEXEC') or die;

jimport('joomla.plugins.plugin');

class plgUserMultiviewcalCreator extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if(!$success) {
			return false; // if the user wasn't stored we don't resync
		}

		if(!$isnew) {
			return false; // if the user isn't new we don't sync
		}

		// ensure the user id is really an int
		$user_id = (int)$user['id'];

		if (empty($user_id)) {
			die('invalid userid');
			return false; // if the user id appears invalid then bail out just in case
		}

		$db 	=& JFactory::getDBO();
  
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_multicalendar/tables');
		$calendar =& JTable::getInstance('multicalendar', 'Table');
		$calendar->title = $this->params->get('name', 0)." - ".$user["username"];
		$calendar->owner = $user_id;
		
		$calendar->permissions = "groups1=0;users1=".( ( (int)$this->params->get('permissions1')==1 )?$user_id:"0" ).";groups2=0;users2=".( ( (int)$this->params->get('permissions2')==1 )?$user_id:"0" ).";groups3=0;users3=".( ( (int)$this->params->get('permissions3')==1 )?$user_id:"0" ).";";
		$calendar->published = 1;
        if ($calendar->check()) {
			$result = $calendar->store();
		}
		if (!(isset($result)) || !$result) {
			JError::raiseError(42, JText::sprintf('Failed to create calendar', $calendar->getError()));
		}
	
		
		$sql = "select id from `#__dc_mv_calendars` where owner=".$user_id;
		$db->setQuery( $sql );
		$rows = $db->loadObjectList();
	    foreach ($rows as $item)
    {
		$calid = $item->id;
	}
		
		
		$sql = "select * from `efemerides` ";
		$db->setQuery( $sql );
		$rows = $db->loadObjectList();
	    foreach ($rows as $item)
    	{
       		//$event = $rows[$i];
			$dato = explode("-", $item->fecha); 
			$dia= $dato[2];
			$chañ='';
			$chms='';
			$shar='';
			$shac='';
			$shaa='';
			$mercurio='';
			$venus='';
			$luna='';
			$sharm='';
			$shacm='';
			$shaam='';
			$cambio='';
			if (trim($item->ChAñ)!='') $chañ='X Año';
			if (trim($item->ChMs)!='') $chms='X Mes';
			if (trim($item->ShaR)!='') $shar='Sh A R';
			if (trim($item->ShaC)!='') $shac='Sh A C';
			if (trim($item->ShaA)!='') $shaa='Sh A A';	
			if (trim($item->ShaRm)!='') $sharm='Sh M R';
			if (trim($item->ShaCm)!='') $shacm='Sh M C';
			if (trim($item->ShaAm)!='') $shaam='Sh M A';	
			
			$efe=$dia." ".$item->a." ".$item->b." ".$item->OF." ".$chañ." ".$chms."".$shar." ".$shac
." ".$shaa." ".$item->Lunas." ".$item->Retrogrados." Mes: ".$item->c." ".$item->d;

			if (trim($item->Retrogrados)=='Mercurio') $mercurio='http://linki.mx/aureum/images/simbolos/Merc01P.png';
			if (trim($item->v_retro)=='Venus') $venus='http://linki.mx/aureum/images/simbolos/Ven01P.png';
			if (trim($item->Lunas)=='Luna Llena')  $luna='http://linki.mx/aureum/images/simbolos/LunaLlena.png';
			if (trim($item->Lunas)=='Luna Menguante') $luna='http://linki.mx/aureum/images/simbolos/LunaMenguante.png';
			if (trim($item->Lunas)=='Luna ueva') $luna='http://linki.mx/aureum/images/simbolos/LunaNueva.png';
			if (trim($item->Lunas)=='Luna Creciente') $luna='http://linki.mx/aureum/images/simbolos/LunaCreciente.png';
			if ($item->cambio=='x') $cambio="<div class='cambio'></div><div class='texto_mes'>mes ".$item->c.$item->d."</div><div class='oficial'>Of: ".trim($item->OF)."</div>";
			else
				$cambio="<div class='nada'><div class='oficial'>Of: ".trim($item->OF)."</div></div>";
			
			if (trim($item->b)=='Ag+') $elemento='Ag';
			else if  (trim($item->b)=='Ma+') $elemento='Ma';
			else if  (trim($item->b)=='Me+') $elemento='Me';
			else if  (trim($item->b)=='Ti+') $elemento='Ti';
			else if  (trim($item->b)=='Fu+') $elemento='Fu';
			else $elemento=trim($item->b);
			
			if (trim($item->d)=='Ag+') $elementom='Ag';
			else if  (trim($item->d)=='Ma+') $elementom='Ma';
			else if  (trim($item->d)=='Me+') $elementom='Me';
			else if  (trim($item->d)=='Ti+') $elementom='Ti';
			else if  (trim($item->d)=='Fu+') $elementom='Fu';
			else $elementom=trim($item->d);
			
			$info=$cambio;
			$info.="<table class='efemerides'>
			<tr><td><table>
			  <tbody>
				<tr>
				  <td width='25%' class='choque'><table>
			  <tbody>
				<tr>
				  <td height='20' class='veinte5 anio'>".$chañ."</td>
				</tr>
				<tr>
				  <td class='veinte5 mesc'>".$chms."</td>
				</tr>
			  </tbody>
			</table>
			</td>
				  <td class=' simbolo_dia ".trim($item->a)."' width='50%'><img src='http://linki.mx/aureum/images/simbolos/".trim($item->a).trim($item->b).".svg' class='".$elemento."' /></td>
				  <td width='25%' class='shas'><table >
			  <tbody>
				<tr>
				 <td class='veinte5' style='text-align:right'>".$shar." ".$shac." ".$shaa." </td>
				  
				</tr>
				<tr>
				 <td class='veinte5' style='text-align:right'>".$sharm." ".$shacm." ".$shaam." </td>
				</tr>
			  </tbody>
			</table>
			</td>
				</tr>
			  </tbody>
			</table>
</td></tr>
  <tr height='20'>
    <td><table>
				 
				  <tr>";
				  if (trim($luna)!='')
					$info.="<td width='20%' class='luna'><img src='".$luna."' alt='".$item->Lunas."' /></td>";
					else
					$info.="<td height='30' width='20%'>&nbsp;</td>";

					
					$info.="
					<td width='20%'>&nbsp;</td>
					<td width='20%'>&nbsp;</td>";
					 if (trim($venus)!='')
			$info.="<td class='veinte5 venus' width='20%'><img src='".$venus."' alt='venus retrógrado' /></td>";
					else
					$info.="<td width='20%' >&nbsp;</td>";
					if (trim($mercurio)!='')
					$info.="<td width='20%' class='mercurio'><img src='".$mercurio."' alt='mercurio retrógrado' /></td>";
					else
					$info.="<td width='20%'>&nbsp;</td>";
				
					
				  $info.="</tr>
				</table></td>
  </tr>
 
</table>

			
			
			";
//$info=htmlentities(addslashes($info));
$info=addslashes($info);
			$query ="insert into #__dc_mv_events(calid,starttime,endtime,title,isalldayevent,owner,published,color) values('".$calid."','".$item->fecha."','".$item->fecha."','".$info."','1','".$user_id."','1','#fff') "; 
            $db->setQuery( $query );
			$efe="";


            if (!$db->query())
		    {
		        JError::raiseError(500, $db->getErrorMsg() );
		    }
            echo $db->insertid();	
			
		}
		

		
	}
	
	

}
