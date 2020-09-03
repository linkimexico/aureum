<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die('Restricted access');
?><?php

class plgAcymailingTagepuser extends JPlugin
{
	var $sendervalues =array();

	function plgAcymailingTagepuser(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'tagepuser');
			$this->params = new JParameter( $plugin->params );
		}
	}

	 function acymailing_getPluginType() {

	 	$onePlugin = new stdClass();
	 	$onePlugin->name = JText::_('Easy Profile');
	 	$onePlugin->function = 'acymailingtagep_show';
	 	$onePlugin->help = 'plugin-tagepuser';

	 	return $onePlugin;
	 }

	function onAcyDisplayFilters(&$type,$context="massactions"){

		if($this->params->get('displayfilter_'.$context,true) == false) return;
		if(!file_exists(ACYMAILING_ROOT .'components'.DS.'com_jsn'.DS.'jsn.php')) return;

		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__jsn_users');
		if(empty($fields)) return;

		$epfield = array();
		foreach($fields as $oneField => $fieldType){
			if($oneField!='privacy' && $oneField!='params')
				$epfield[] = JHTML::_('select.option',$oneField,$oneField);
		}
		$type['epfield'] = JText::_('Easy Profile');

		$operators = acymailing_get('type.operators');

		$return = '<div id="filter__num__epfield">'.JHTML::_('select.genericlist',   $epfield, "filter[__num__][epfield][map]", 'class="inputbox" size="1"', 'value', 'text');
		$return.= ' '.$operators->display("filter[__num__][epfield][operator]").' <input onchange="countresults(0)" class="inputbox" type="text" name="filter[__num__][epfield][value]" style="width:200px" value="" /></div>';

	 	return $return;
	 }

	function onAcyProcessFilter_epfield(&$query,$filter,$num){
	 	$query->leftjoin['epfield'] = '#__jsn_users AS epfield ON epfield.id = sub.userid';
	 	$query->where[] = $query->convertQuery('epfield',$filter['map'],$filter['operator'],$filter['value']);
	 }

	 function onAcyProcessFilterCount_epfield(&$query,$filter,$num){
	 	$this->onAcyProcessFilter_epfield($query,$filter,$num);
		return JText::sprintf('SELECTED_USERS',$query->count());
	 }

	 function acymailingtagep_show(){?>

		<script language="javascript" type="text/javascript">
			function applyTag(tagname){
				var string = '{eptag:'+tagname;
				for(var i=0; i < document.adminForm.typeinfo.length; i++){
					 if (document.adminForm.typeinfo[i].checked){ string += '|info:'+document.adminForm.typeinfo[i].value; }
				}
				string += '}';
				setTag(string);
				insertTag();
			}
		</script>
	<?php
		$typeinfo = array();
		$typeinfo[] = JHTML::_('select.option', "receiver",JText::_('RECEIVER_INFORMATION'));
		$typeinfo[] = JHTML::_('select.option', "sender",JText::_('SENDER_INFORMATIONS'));
		echo JHTML::_('acyselect.radiolist', $typeinfo, 'typeinfo' , '', 'value', 'text', 'receiver');

		$text = '<table class="adminlist table table-striped table-hover" cellpadding="1">';
		$db = JFactory::getDBO();
		$fields = acymailing_getColumns('#__jsn_users');

		$db->setQuery('SELECT title,type FROM #__jsn_fields');
		$fieldType = $db->loadObjectList('title');

		$k = 0;

		foreach($fields as $fieldname => $oneField){
			if($fieldname!='privacy' && $fieldname!='params'){
				$type = '';
				if(strpos(strtolower($oneField),'date') !== false) $type = '|type:date';
				if(!empty($fieldType[$fieldname]) AND $fieldType[$fieldname]->type == 'image') $type = '|type:image';
				$text .= '<tr style="cursor:pointer" class="row'.$k.'" onclick="applyTag(\''.$fieldname.$type.'\');" ><td class="acytdcheckbox"></td><td>'.$fieldname.'</td></tr>';
				$k = 1-$k;
			}
		}
		$text .= '</table>';

		echo $text;
	 }

	function acymailing_replaceusertags(&$email,&$user,$send = true){

		$match = '#(?:{|%7B)eptag:(.*)(?:}|%7D)#Ui';
		$variables = array('subject','body','altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match,$email->$var,$results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}

		if(!$found) return;

		$db = JFactory::getDBO();
		$pluginsHelper = acymailing_get('helper.acyplugins');
		$receivervalues = new stdClass();
		require_once(ACYMAILING_ROOT .'components'.DS.'com_jsn'.DS.'helpers'.DS.'helper.php');

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;

				$arguments = explode('|',$allresults[1][$i]);
				$field = $arguments[0];
				unset($arguments[0]);
				$mytag = new stdClass();
				$mytag->default = $this->params->get('default_'.$field,'');
				if(!empty($arguments)){
					foreach($arguments as $onearg){
						$args = explode(':',$onearg);
						if(isset($args[1])){
							$mytag->$args[0] = $args[1];
						}else{
							$mytag->$args[0] = 1;
						}
					}
				}
				$values = new stdClass();
				if(!empty($mytag->info) AND $mytag->info == 'sender'){
					if(empty($this->sendervalues[$email->mailid]) AND !empty($email->userid)){
						$this->sendervalues[$email->mailid] = JsnHelper::getUser($email->userid);
					}
					if(!empty($this->sendervalues[$email->mailid])) $values = $this->sendervalues[$email->mailid];
				}else{
					if(empty($receivervalues->id) AND !empty($user->userid)){
						$receivervalues = JsnHelper::getUser($user->userid);	
					}
					if(!empty($receivervalues->id)) $values = $receivervalues;
				}

				$replaceme = isset($values->$field) ? $values->getField($field) : $mytag->default;

				$tags[$oneTag] = $replaceme;
				$pluginsHelper->formatString($tags[$oneTag],$mytag);
			}
		}

		foreach($results as $var => $allresults){
			$email->$var = str_replace(array_keys($tags),$tags,$email->$var);
		}
	 }//endfct
}//endclass
