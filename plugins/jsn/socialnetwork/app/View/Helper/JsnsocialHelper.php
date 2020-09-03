<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnsocialHelper extends JsnAppHelper {
    
    public $helpers = array('Time', 'Text');

    public function getUserPicture($pic = '', $thumb = true)
    {
        if ($pic)
            return (strpos($pic,'http')===false ? $this->request->webroot : '') . 'uploads/avatars/'.$pic;
        static $defaultavatar=null;
		if($defaultavatar) 
			return $defaultavatar;
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('params')->from('#__jsn_fields')->where($db->quoteName('alias').'='.$db->quote('avatar'));
		
		$db->setQuery($query);
		$result=$db->loadResult();
		$registry = new JRegistry;
		$registry->loadString($result);
		$defaultavatar = $registry->get('image_defaultvalue','');
		if(empty($defaultavatar))
			$defaultavatar = 'components/com_jsn/assets/img/default.jpg';
		return $defaultavatar;
    }

    public function getUserAvatar($userdata, $width = '45', $class = '')
    {
        if (!empty($userdata))
        {
            /*if ( !empty( $userdata['username'] ) )
                $url = $this->request->base . '/-' . $userdata['username'];
            else
                $url = $this->request->base . '/users/view/'.$userdata['id'];*/
			$profileMenu=JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_jsn&view=profile',true);
			if(isset($profileMenu->id)) $Itemid=$profileMenu->id;
			else $Itemid='';
			$url = JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$Itemid.'&id='.$userdata['id'],false);
            
            if ( !empty($userdata['avatar_clean']) )
                return '<a href="' . $url . '"><i class="'.($userdata['online'] ? 'ep_online' : 'ep_offline').'"></i><img src="' . (strpos($userdata['avatar'],'http')===false ? $this->request->webroot : '') . 'uploads/avatars/'.$userdata['avatar'].'" class="img_wrapper2 '.$class.'" style="width:' . $width . 'px;height:' . $width . 'px" title="'.str_replace(array('<','>'), '', $userdata['name']).'"></a>';
            else
                return '<a href="' . $url . '"><i class="'.($userdata['online'] ? 'ep_online' : 'ep_offline').'"></i><img avatar="'.str_replace(array('<','>'), '', $userdata['name']).'" src="' . (strpos($userdata['avatar'],'http')===false ? $this->request->webroot : '') . 'uploads/avatars/'.$userdata['avatar'].'" class="img_wrapper2 '.$class.'" style="width:' . $width . 'px;height:' . $width . 'px" title="'.str_replace(array('<','>'), '', $userdata['name']).'"></a>';
        }
        else
            return '<img src="' . $this->request->webroot . 'img/no-avatar-sm.jpg" class="img_wrapper2 '.$class.'" style="width:'.$width.'px;height:' . $width . 'px">';
    }

    public function getName($userdata, $bold = true)
    {
        if (!empty($userdata))
        {
            $name = h($this->Text->truncate($userdata['name'], 30));
            
            /*if ( !empty( $userdata['username'] ) )
                $url = $this->request->base . '/-' . $userdata['username'].'/';
            else
                $url = $this->request->base . '/users/view/'.$userdata['id'];*/
			
			$profileMenu=JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_jsn&view=profile',true);
			if(isset($profileMenu->id)) $Itemid=$profileMenu->id;
			else $Itemid='';
			$url = JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$Itemid.'&id='.$userdata['id'],false);
            
            if ($bold)
                return '<a href="' . $url . '"><b>' . $name . '</b></a>';
            else
                return '<a href="' . $url . '">' . $name . '</a>';
        }
    }
    
    public function getProfileUrl($userdata)
    {
        if (!empty($userdata))
        {
            /*if ( !empty( $userdata['username'] ) )
                $url = $this->request->base . '/-' . $userdata['username'].'/';
            else
                $url = $this->request->base . '/users/view/'.$userdata['id'];*/
			$profileMenu=JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_jsn&view=profile',true);
			if(isset($profileMenu->id)) $Itemid=$profileMenu->id;
			else $Itemid='';
			$url = JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$Itemid.'&id='.$userdata['id'],false);
            
            return $url;
        }
    }

    public function getItemPicture($obj, $type, $thumb = false)
    {
        $prefix = '';
        if ($thumb) $prefix = 't_';
        
        if (!empty($obj['photo']))
            return $this->request->webroot . 'uploads/' . $type . '/' . $prefix . $obj['photo'];
        else
            return $this->request->webroot . 'img/' . $prefix . 'no-image-' . $type . '.jpg';
    }

    public function getAlbumCover($cover = '')
    {
        if ($cover && file_exists(WWW_ROOT . $cover))
            return $this->request->webroot . $cover;
        else
            return $this->request->webroot . 'img/no-image.jpg';
    }

    public function getTime( $time, $format = 'F j \a\t g:ia', $timezone = 'UTC' )
    {
        return $this->Time->timeAgoInWords( $time, array( 'end' => '+4 day', 'format' => $format, 'timezone' => $timezone ) );
    }
    
    public function formatText( $text, $truncate = false, $parse_smilies = true )
    {
        $text = preg_replace('/(\r?\n){2,}/', "\n\n", $text);
            
        if ( !$truncate )
            $text = nl2br( str_replace('&amp;', '&', h( $text ) ) );
        else
            $text = nl2br( str_replace('&amp;', '&', h( $this->Text->truncate( $text, $truncate ) ) ) );
        
        if ( $parse_smilies )
            $text = $this->parseSmilies( $text );
        
        $text = $this->Text->autoLink( $text, array( 'target' => '_blank', 'rel' => 'nofollow', 'escape' => false ) );
        
        return $text;
    }
    
    public function cleanHtml( $text )
    {
        $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', strip_tags( $text, '<ul><ol><li><img><a><p><br><b><i><u><strong><em><sub><sup><div><blockquote>' ) );
        $text = $this->parseSmilies( $text );

        $text = preg_replace_callback('/<a[^>]+/', function($matches) {
            $link = $matches[0];
            $site_link = JURI::base();

            if (strpos($link, 'rel') === false) {
                $link = preg_replace("%(href=\S(?!$site_link))%i", 'rel="nofollow" $1', $link);
            } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
                $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
            }

            if (strpos($link, 'target') === false) {
                $link = preg_replace("%(href=\S(?!$site_link))%i", 'target="_blank" $1', $link);
            } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
                $link = preg_replace('/target=\S(?!nofollow)\S*/i', 'target="_blank"', $link);
            }
            return $link;
        }, $text);
        
        return $text;
    }

	public function cleanHtmlPartial( $text )
    {
        $text = strip_tags( $text, '<ul><ol><li><img><a><p><br><b><i><u><strong><em><sub><sup><div><blockquote>' ) ;
        $text = $this->parseSmilies( $text );

        $text = preg_replace_callback('/<a[^>]+/', function($matches) {
            $link = $matches[0];
            $site_link = JURI::base();

            if (strpos($link, 'rel') === false) {
                $link = preg_replace("%(href=\S(?!$site_link))%i", 'rel="nofollow" $1', $link);
            } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
                $link = preg_replace('/rel=\S(?!nofollow)\S*/i', 'rel="nofollow"', $link);
            }

            if (strpos($link, 'target') === false) {
                $link = preg_replace("%(href=\S(?!$site_link))%i", 'target="_blank" $1', $link);
            } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
                $link = preg_replace('/target=\S(?!nofollow)\S*/i', 'target="_blank"', $link);
            }
            return $link;
        }, $text);
        
        return $text;
    }

    public function parseSmilies( $text )
    {
        $text = str_replace( array( ':)', ':(', ':D', 'B)', ';)', ':P', ':s', 'o_o' ), 
                             array( '<img src="' . $this->request->webroot . 'img/smilies/smile.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/sad.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/lol.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/cool.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/wink.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/razz.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/confuse.png" align="absmiddle">',
                                    '<img src="' . $this->request->webroot . 'img/smilies/eek.png" align="absmiddle">' ), 
                             $text );
        return $text;
    }
    
    public function getTimeZones()
    {
        $timezones = array(
            'Pacific/Kwajalein' => '(GMT-12:00) Kwajalein',
            'Pacific/Midway' => '(GMT-11:00) Midway Island',
            'Pacific/Samoa' => '(GMT-11:00) Samoa',
            'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
            'America/Anchorage' => '(GMT-09:00) Alaska',
            'America/Los_Angeles' => '(GMT-08:00) Pacific Time',
            'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
            'America/Denver' => '(GMT-07:00) Mountain Time',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua',
            'America/Mazatlan' => '(GMT-07:00) Mazatlan',
            'America/Phoenix' => '(GMT-07:00) Arizona',
            'America/Regina' => '(GMT-06:00) Saskatchewan',
            'America/Tegucigalpa' => '(GMT-06:00) Central America',
            'America/Chicago' => '(GMT-06:00) Central Time',
            'America/Mexico_City' => '(GMT-06:00) Mexico City',
            'America/Monterrey' => '(GMT-06:00) Monterrey',
            'America/New_York' => '(GMT-05:00) Eastern Time',
            'America/Bogota' => '(GMT-05:00) Bogota',
            'America/Lima' => '(GMT-05:00) Lima',
            'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
            'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'America/Halifax' => '(GMT-04:00) Atlantic Time',
            'America/Manaus' => '(GMT-04:00) Manaus',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'America/St_Johns' => '(GMT-03:30) Newfoundland',
            'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
            'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
            'America/Godthab' => '(GMT-03:00) Greenland',
            'America/Montevideo' => '(GMT-03:00) Montevideo',
            'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Europe/Dublin' => '(GMT) Dublin',
            'Europe/Lisbon' => '(GMT) Lisbon',
            'Europe/London' => '(GMT) London',
            'Africa/Monrovia' => '(GMT) Monrovia',
            'Atlantic/Reykjavik' => '(GMT) Reykjavik',
            'Africa/Casablanca' => '(GMT) Casablanca',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade',
            'Europe/Bratislava' => '(GMT+01:00) Bratislava',
            'Europe/Budapest' => '(GMT+01:00) Budapest',
            'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
            'Europe/Prague' => '(GMT+01:00) Prague',
            'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
            'Europe/Skopje' => '(GMT+01:00) Skopje',
            'Europe/Warsaw' => '(GMT+01:00) Warsaw',
            'Europe/Zagreb' => '(GMT+01:00) Zagreb',
            'Europe/Brussels' => '(GMT+01:00) Brussels',
            'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
            'Europe/Madrid' => '(GMT+01:00) Madrid',
            'Europe/Paris' => '(GMT+01:00) Paris',
            'Africa/Algiers' => '(GMT+01:00) West Central Africa',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
            'Europe/Berlin' => '(GMT+01:00) Berlin',
            'Europe/Rome' => '(GMT+01:00) Rome',
            'Europe/Stockholm' => '(GMT+01:00) Stockholm',
            'Europe/Vienna' => '(GMT+01:00) Vienna',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Europe/Helsinki' => '(GMT+02:00) Helsinki',
            'Europe/Riga' => '(GMT+02:00) Riga',
            'Europe/Sofia' => '(GMT+02:00) Sofia',
            'Europe/Tallinn' => '(GMT+02:00) Tallinn',
            'Europe/Vilnius' => '(GMT+02:00) Vilnius',
            'Europe/Athens' => '(GMT+02:00) Athens',
            'Europe/Bucharest' => '(GMT+02:00) Bucharest',
            'Europe/Istanbul' => '(GMT+02:00) Istanbul',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Asia/Amman' => '(GMT+02:00) Amman',
            'Asia/Beirut' => '(GMT+02:00) Beirut',
            'Africa/Windhoek' => '(GMT+02:00) Windhoek',
            'Africa/Harare' => '(GMT+02:00) Harare',
            'Asia/Kuwait' => '(GMT+03:00) Kuwait',
            'Asia/Riyadh' => '(GMT+03:00) Riyadh',
            'Asia/Baghdad' => '(GMT+03:00) Baghdad',
            'Africa/Nairobi' => '(GMT+03:00) Nairobi',
            'Asia/Tbilisi' => '(GMT+03:00) Tbilisi',
            'Europe/Moscow' => '(GMT+03:00) Moscow',
            'Europe/Volgograd' => '(GMT+03:00) Volgograd',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Asia/Muscat' => '(GMT+04:00) Muscat',
            'Asia/Baku' => '(GMT+04:00) Baku',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
            'Asia/Karachi' => '(GMT+05:00) Karachi',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Calcutta',
            'Asia/Colombo' => '(GMT+05:30) Sri Jayawardenepura',
            'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Dhaka' => '(GMT+06:00) Dhaka',
            'Asia/Almaty' => '(GMT+06:00) Almaty',
            'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
            'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
            'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok',
            'Asia/Jakarta' => '(GMT+07:00) Jakarta',
            'Asia/Brunei' => '(GMT+08:00) Beijing',
            'Asia/Chongqing' => '(GMT+08:00) Chongqing',
            'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
            'Asia/Urumqi' => '(GMT+08:00) Urumqi',
            'Asia/Irkutsk' => '(GMT+08:00) Irkutsk',
            'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
            'Asia/Singapore' => '(GMT+08:00) Singapore',
            'Asia/Taipei' => '(GMT+08:00) Taipei',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Tokyo' => '(GMT+09:00) Tokyo',
            'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Canberra' => '(GMT+10:00) Canberra',
            'Australia/Melbourne' => '(GMT+10:00) Melbourne',
            'Australia/Sydney' => '(GMT+10:00) Sydney',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
            'Pacific/Guam' => '(GMT+10:00) Guam',
            'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
            'Asia/Magadan' => '(GMT+11:00) Magadan',
            'Pacific/Fiji' => '(GMT+12:00) Fiji',
            'Asia/Kamchatka' => '(GMT+12:00) Kamchatka',
            'Pacific/Auckland' => '(GMT+12:00) Auckland',
            'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa'
        );
                               
        return $timezones;
    }
}

?>
