<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Easyblog extends JPlugin
{
	
	public function renderTabs($data, $config)
	{
		// Only Authors can see this tab
		$acl = EB::acl($data->id);
		if(!$acl->get('add_entry')) return;
		
		$lang = JFactory::getLanguage();
		$lang->load('com_easyblog');
		$plugin=array(JText::_($this->params->get('tabtitle','Blog')));

		$app = JFactory::getApplication();
		$input = $app->input;
		$input->set('id',$data->id);

		$view=new JsnEasyBlogViewBlogger();

		ob_start();
		$view->listings();
		
		$script='<script>jQuery(document).ready(function(){
			var ebtabname=jQuery("#eblog-wrapper").closest(".jsn-form-fieldset").data("name");
			jQuery(\'.eb-pager a\').each(function(){
				var link = this.href + "#" + ebtabname;
				jQuery(this).attr("href",link);
			});
			jQuery("#profileTabs a,#jsn-profile-tabs a").click(function(){
				if(jQuery(".jsn_profile_fields").width()>450 || jQuery(".jsn-p-fields").width()>450) jQuery("#fd").removeClass("w320 w480");
				if(jQuery(\'.eb-masonry\').length) setTimeout(function(){jQuery(\'.eb-masonry\').masonry()},500);

			});
		});
		</script>';
		
		$css='<style>
			div#fd.eb .eb-header,#eb .eb-header,div#fd.eb .eb-author,#eb .eb-author{display:none !important;}
		</style>';
		
		$plugin[]=$script.$css.'<div id="eblog-wrapper" class="eblog-view-profile">'.ob_get_clean().'</div>';
		
		return $plugin;
		
	}
}

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');
require_once(EBLOG_ROOT . '/views/views.php');

class JsnEasyBlogViewBlogger extends EasyBlogView
{	
	/**
	 * Displays blog posts created by specific users
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function listings()
	{
		// Get sorting options
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$id = $this->input->get('id', 0, 'int');

		// Load the author object
		$author = EB::user($id);

		// Disallow all users from being viewed
		if ((!$this->config->get('main_nonblogger_profile') && !EB::isBlogger($author->id)) || !$author->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_INVALID_AUTHOR_ID_PROVIDED'));
		}

		// Get the authors acl
		$acl = EB::acl($author->id);

		// Set meta tags for the author if allowed to
		if ($acl->get('allow_seo')) {
			EB::setMeta($author->id, META_TYPE_BLOGGER, true);
		}

		// Set the breadcrumbs
		/*if (!EBR::isCurrentActiveMenu('blogger', $author->id) && !EBR::isCurrentActiveMenu('blogger')) {
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , EB::_('index.php?option=com_easyblog&view=blogger') );

			$this->setPathway($author->getName());
		}*/

		// Get the current active menu
		$active = $this->app->getMenu()->getActive();

		// Excluded categories
		$excludeCats = array();

		if (isset($active->params)) {

			$excludeCats = $active->params->get('exclusion');

			// Ensure that this is an array
			if (!is_array($excludeCats) && $excludeCats) {
				$excludeCats = array($excludeCats);
			}
		}

		// Get the blogs model now to retrieve our blog posts
		$model = EB::model('Blog');

		// Get blog posts
		$posts = $model->getBlogsBy('blogger', $author->id, $sort, 0, '', false, false, '', false, false, true, $excludeCats);
		$pagination	= $model->getPagination();

		// Format the blogs with our standard formatter
		$posts = EB::formatter('list', $posts);

		// Add canonical urls
		/*$this->canonical('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id);

		// Add authors rss links on the header
		if ($this->config->get('main_rss')) {
			if ($this->config->get('main_feedburner') && $this->config->get('main_feedburnerblogger')) {
				$this->doc->addHeadLink(EB::string()->escape($author->getRssLink()), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
			} else {

				// Add rss feed link
				$this->doc->addHeadLink($author->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
				$this->doc->addHeadLink($author->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
			}
		}

		// Set the title of the page
		$title 	= EB::getPageTitle($author->getName());
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));*/

		// Check if subscribed
		$bloggerModel = EB::model('Blogger');
		$isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($author->id, $this->my->email);

		$return = base64_encode($author->getPermalink());

		// Generate pagination
		$pagination = $pagination->getPagesLinks();

		$this->set('pagination', $pagination);
		$this->set('return', $return);
		$this->set('author', $author);
		$this->set('posts', $posts);
		$this->set('sort', $sort);
		$this->set('isBloggerSubscribed', $isBloggerSubscribed);

		parent::display('authors/item');
	}
}

?>