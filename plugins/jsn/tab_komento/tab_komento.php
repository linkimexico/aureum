<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Komento extends JPlugin
{
	
	public function renderTabs($data, $config)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_komento');
		$plugin=array(JText::_($this->params->get('tabtitle','Comments')));
		
		Komento::getHelper( 'Document' )->loadHeaders();
		
		$view=new JsnKomentoViewProfile();

		ob_start();
		$view->display();
		
		$script='';
		
		$css='<style>div.kmt-profile-head{display:none !important;}</style>';
		
		$plugin[]=$script.$css.'<div id="komento-wrapper" class="komento-view-profile">'.ob_get_clean().'</div>';
		
		return $plugin;
		
	}
}

require_once( JPATH_ROOT . '/components/com_komento/helpers/helper.php' );
require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'view.php' );

class JsnKomentoViewProfile extends KomentoView
{
	public function display( $tpl = null )
	{
		$konfig			= Komento::getKonfig();
		$id				= JRequest::getInt( 'id', 0 );

		// @task: If profiles are disabled, do not show the profile here.
		if( !$konfig->get( 'profile_enable' ) )
		{
			$app 	= JFactory::getApplication();
			$app->redirect( 'index.php' , JText::_( 'COM_KOMENTO_PROFILE_SYSTEM_DISABLED' ) );
			$app->close();
		}

		$profileModel	= Komento::getModel( 'Profile' );
		$activityModel	= Komento::getModel( 'Activity' );
		$commentsModel	= Komento::getModel( 'Comments' );
		$actionsModel	= Komento::getModel( 'Actions' );

		$count			= new stdClass;

		$user = JFactory::getUser();

		if( $id === 0 && $user->id > 0 )
		{
			$id = $user->id;
		}

		// Block non-exists profile
		if (!$profileModel->exists( $id ))
		{
			echo JText::_( 'COM_KOMENTO_PROFILE_NOT_FOUND' );
			return;
		}

		// TODO: Block custom profile id
		// ..

		$profile		= Komento::getProfile( $id );
		// $activities		= $activityModel->getUserActivities( $profile->id );
		$count->totalComments	= $commentsModel->getTotalComment( $profile->id );
		$count->likesReceived	= $actionsModel->getLikesReceived( $profile->id );
		$count->likesGiven		= $actionsModel->getLikesGiven( $profile->id );

		// Set Pathway
		// Check if Komento profile menu item exist before setting profile pathway
		/*$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getActive();
		if( empty( $item ) || $item->query['view'] != 'profile' )
		{
			$this->setPathway( JText::_('COM_KOMENTO_PROFILE') , '' );
		}
		$this->setPathway( $profile->getName() , '' );*/

		// Set browser title
		/*$document = JFactory::getDocument();
		$document->setTitle( JText::_('COM_KOMENTO_USER_PROFILE') . ' - ' . $profile->getName() );*/

		// set component to com_komento
		Komento::setCurrentComponent( 'com_komento' );
		$theme = Komento::getTheme();
		$theme->set( 'profile', $profile );
		$theme->set( 'count', $count );
		// $theme->set( 'activities', $activities );
		echo $theme->fetch('profile/profile.php');
	}

	function getModel( $name = null )
	{
		return Komento::getModel( 'Profile' );
	}
}


?>