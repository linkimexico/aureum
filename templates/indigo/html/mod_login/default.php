<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

JLoader::register('UsersHelperRoute', JPATH_SITE . '/components/com_users/helpers/route.php');

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');

?>

<div class="sp-custom-login sp-mod-login">
    <span class="info-text">
        <a href="#login" class="sppb-btn sppb-btn-link" role="button" data-toggle="modal"><img src="<?php echo JURI::root();?>images/demo/author-icon.svg" alt="cart"> <span class="text"><?php echo JText::_('CUSTOM_LOGIN'); ?></span></a>
        <a href="index.php?option=com_users&view=registration" class="sppb-btn sppb-btn-link registration-btn" role="button">Registro<?php //echo JText::_('CUSTOM_SIGNUP'); ?></a>
    </span>
    
    <!--Modal-->
    <div id="login" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                    <h2><?php echo ($user->id > 0) ? JText::_('MY_ACCOUNT') : JText::_('CUSTOM_LOGIN_HEADING'); ?></h2>
                </div>
                <div class="modal-body">
                    <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
                        <?php if ($params->get('pretext')) : ?>
                            <div class="pretext mb-2">
                                <?php echo $params->get('pretext'); ?>
                            </div>
                        <?php endif; ?>
                    
                        <div id="form-login-username" class="form-group">
                            <?php if (!$params->get('usetext')) : ?>
                                <div class="input-group">
                                    <input id="modlgn-username" type="text" name="username" class="sppb-form-control" tabindex="0" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?>" />
                                </div>
                            <?php else : ?>
                                <label for="modlgn-username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
                                <input id="modlgn-username" type="text" name="username" class="sppb-form-control" tabindex="0" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?>" />
                            <?php endif; ?>
                        </div>
                    
                        <div id="form-login-password" class="form-group">
                            <?php if (!$params->get('usetext')) : ?>
                                <div class="input-group">
                                    <input id="modlgn-passwd" type="password" name="password" class="sppb-form-control" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />
                                </div>
                            <?php else : ?>
                                <label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
                                <input id="modlgn-passwd" type="password" name="password" class="sppb-form-control" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />
                            <?php endif; ?>
                        </div>
                    
                        <?php if (count($twofactormethods) > 1) : ?>
                            <div id="form-login-secretkey" class="form-group">
                                <?php if (!$params->get('usetext')) : ?>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" aria-label="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>"><span class="fa fa-star"></span></span>
                                        </div>
                                        <input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="sppb-form-control" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" />
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary hasTooltip" type="button" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
                                                <span class="fa fa-support"></span>
                                            </button>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <label for="modlgn-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
                                    <input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="sppb-form-control" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" />
                                    <small class="form-text text-muted"><span class="fa fa-asterisk"></span> <?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="remeber-forget-wrap d-flex justify-content-between">
                            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                                <div id="form-login-remember" class="form-group form-check">
                                    <input id="modlgn-remember" type="checkbox" name="remember" class="form-check-input" value="yes"/>
                                    <label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME'); ?></label>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a class="forget-pass" href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
                                <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
                            </div>
                        </div>
                    
                        <div id="form-login-submit" class="form-group">
                            <button type="submit" tabindex="0" name="Submit" class="sppb-btn sppb-btn-default"><?php echo JText::_('JLOGIN'); ?></button>
                        </div>
                    
                        <?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
                        <div class="reg-link">
                            <?php if ($usersConfig->get('allowUserRegistration')) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
                                <?php echo JText::_('MOD_LOGIN_REGISTER'); ?> <span class="icon-arrow-right"></span></a>
                            <?php endif; ?>
                        </div>
                    
                        <input type="hidden" name="option" value="com_users" />
                        <input type="hidden" name="task" value="user.login" />
                        <input type="hidden" name="return" value="<?php echo $return; ?>" />
                        <?php echo JHtml::_('form.token'); ?>
                    
                        <?php if ($params->get('posttext')) : ?>
                            <div class="posttext mt-2">
                                <?php echo $params->get('posttext'); ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div><!--/Modal body-->
            </div> <!-- Modal content-->
        </div> <!-- /.modal-dialog -->
    </div><!--/Modal-->
</div>
