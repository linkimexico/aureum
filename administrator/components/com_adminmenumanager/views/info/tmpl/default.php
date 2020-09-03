<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.2
* @copyright Copyright (C) 2012 - 2019 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<form class="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<div class="clr"> </div><!-- needed for some admin templates -->
		<fieldset class="adminform pi_wrapper_nice">	
			<legend class="pi_legend"><?php echo JText::_('COM_ADMINMENUMANAGER_SUPPORT_INFO'); ?></legend>		
			<table class="adminlist amm_table">	
				<tr>
					<td style="width: 10px;">
						1.
					</td>			
					<td>
						<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_FAQS'); ?></a>
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_FAQS_INFO'); ?>.
					</td>
				</tr>
				<tr>
					<td>
						2.
					</td>			
					<td>
						<a href="http://www.pages-and-items.com/forum/advsearch?catids=43" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_SEARCH_FORUM'); ?></a> 
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_SEARCH_FORUM_INFO'); ?> 'Admin-Menu-Manager'.
					</td>
				</tr>
				<tr>
					<td>
						3.
					</td>			
					<td>
						<a href="http://www.pages-and-items.com/forum/43-admin-menu-manager" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_POST_FORUM'); ?></a>
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_POST_FORUM_INFO'); ?> 'Admin-Menu-Manager'.
					</td>
				</tr>
				<tr>
					<td>
						4.
					</td>			
					<td>
						<a href="http://www.pages-and-items.com/contact" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_CONTACT'); ?></a>
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_CONTACT_INFO'); ?>.
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform pi_wrapper_nice">	
			<legend class="pi_legend"><?php echo JText::_('COM_ADMINMENUMANAGER_UPDATE_NOTIFICATIONS'); ?></legend>
			<table class="adminlist amm_table">	
				<tr>
					<td style="width: 10px;">
						<img src="components/com_adminmenumanager/images/mail.png" alt="mail" />
					</td>
					<td>
						<a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_EMAIL_UPDATE_NOTIFICATIONS'); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<img src="components/com_adminmenumanager/images/rss.png" alt="rss" />
					</td>
					<td>
						<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/update-notifications-for-admin-menu-manager" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_RSS'); ?></a>
					</td>
				</tr>
				<tr>
					<td>
						<img src="components/com_adminmenumanager/images/twitter.png" alt="twitter" />
					</td>
					<td>
						<a href="http://twitter.com/PagesAndItems" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_TWITTER'); ?> Twitter</a>
					</td>
				</tr>
			</table>
		</fieldset>	
		<fieldset class="adminform pi_wrapper_nice">	
			<legend class="pi_legend"><?php echo JText::_('COM_ADMINMENUMANAGER_REVIEW'); ?></legend>		
			<p>			
			<?php 
			echo JText::_('COM_ADMINMENUMANAGER_REVIEW_B'); 
			if($this->controller->get_version_type()=='pro'){
				$url_jed = '22805';
			}else{
				$url_jed = '21305';
			}		
			?>
			<a href="http://extensions.joomla.org/extensions/administration/admin-navigation/<?php echo $url_jed; ?>" target="_blank">
				Joomla! Extensions Directory</a>.
			</p>
		</fieldset>	
		<fieldset class="adminform pi_wrapper_nice">	
			<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> Redirect-On-Login</legend>
			<table class="adminlist amm_table">	
				<tr>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_ROL'); ?>:
						<br />
						<ul class="pi_show_bullets">
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_FOR').' '.(JText::_('COM_ADMINMENUMANAGER_ALLUSERS')); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_PER').' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_USERGROUP')); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_PER').' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_ACCESSLEVEL')); ?>
							</li>						
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_PER').' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_SPECIFIC_USER')); ?> (pro)
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_WHEN').' '.$this->controller->amm_strtolower(JText::_('COM_LOGIN')).' / '.$this->controller->amm_strtolower(JText::_('MOD_MENU_LOGOUT')).' '.JText::_('COM_ADMINMENUMANAGER_BACKEND'); ?> (pro)
							</li>
							<li>
								<?php echo $this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_SCRIPTS')); ?> (pro)
							</li>						
						</ul>	
						<br />							
						<img src="components/com_adminmenumanager/images/screenshot_rol.png" alt="Redirect-on-Login" class="pi_imgborder" />				
						<br /><br />					
						<a href="http://www.pages-and-items.com/extensions/redirect-on-login" target="_blank" class="pi_font">
							<?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?>
						</a>	
					</td>
				</tr>
			</table>					
		</fieldset>	
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> Admin-Help-Pages</legend>	
				<table class="adminlist amm_table">	
				<tr>
					<td>
						<ul class="pi_show_bullets">
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_AHP_A'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_AHP_B'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_AHP_C'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_AHP_D'); ?>
							</li>				
						</ul>
						<br />	
						<img src="components/com_adminmenumanager/images/screenshot-ahp.jpg" alt="Admin-Help-Pages" class="pi_imgborder" />
						<br /><br />					
						<a href="http://www.pages-and-items.com/extensions/admin-help-pages" target="_blank" class="pi_font">
							<?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?>
						</a>
					</td>
				</tr>
			</table>
		</fieldset>					
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> Dynamic-Menu-Links</legend>
			<table class="adminlist amm_table">	
				<tr>
					<td>				
						<?php 
							echo JText::_('COM_ADMINMENUMANAGER_DML');
						?>:
						<ul class="pi_show_bullets">
							<li>
								<?php echo $this->controller->amm_strtolower(JText::_('COM_MODULES_OPTION_POSITION_USER_DEFINED')); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_USERGROUP'); ?>
							</li>
							<li>
								<?php echo $this->controller->amm_strtolower(JText::_('JDATE')).' / '.$this->controller->amm_strtolower(JText::_('MOD_STATS_TIME')); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_DEVICE'); ?>
							</li>
							<li>
								<?php echo $this->controller->amm_strtolower(JText::_('COM_CONTACT_FIELD_CONFIG_COUNTRY_LABEL')); ?>
							</li>
							<li>
								<?php echo $this->controller->amm_strtolower(JText::_('JGRID_HEADING_LANGUAGE')); ?>
							</li>
							<li>
								IP
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_CURRENTPAGE'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_ANDMUCHMORE'); ?>
							</li>							
						</ul>
						<br />
						<img src="components/com_adminmenumanager/images/screenshot-dml.png" alt="Dynamic-Menu-Links" class="pi_imgborder" />
						<br /><br />						
						<a href="http://www.pages-and-items.com/extensions/dynamic-menu-links" target="_blank" class="pi_font">
							<?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?>
						</a>	
					</td>
				</tr>
			</table>
		</fieldset>	
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('COM_INSTALLER_TYPE_COMPONENT'); ?> User-Private-Page</legend>				
			<table class="adminlist amm_table">	
				<tr>
					<td>
						<ul class="pi_show_bullets">
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_UPP_A'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_UPP_B'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_UPP_C'); ?>
							</li>
							<li>
								<?php echo JText::_('COM_ADMINMENUMANAGER_UPP_D'); ?>
							</li>				
						</ul>				
						<br />	
						<a href="http://www.pages-and-items.com/extensions/user-private-page" target="_blank">
							<img src="components/com_adminmenumanager/images/screenshot-upp.jpg" alt="User-Private-Page" class="pi_imgborder" />
						</a>
						<br /><br />					
						<a href="http://www.pages-and-items.com/extensions/user-private-page" target="_blank" class="pi_font">
							<?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?>
						</a>
					</td>
				</tr>
			</table>		
		</fieldset>			
	</div>
</form>