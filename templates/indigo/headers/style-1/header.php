<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

$data = $displayData;
$offcanvs_position = $displayData->params->get('offcanvas_position', 'right');

$feature_folder_path     = JPATH_THEMES . '/' . $data->template->template . '/features/';

include_once $feature_folder_path.'logo.php';
include_once $feature_folder_path.'social.php';
include_once $feature_folder_path.'contact.php';
include_once $feature_folder_path.'menu.php';
include_once $feature_folder_path.'menu2.php';

$output  = '';

$output .= '<div class="header-wrapper">';

    $output .= '<header id="sp-header">';
        $output .= '<div class="container">';
            $output .= '<div class="container-inner">';
                $output .= '<div class="row">';

                $class1 = 'col-8 col-lg-2';
                $class2 = 'col-4 col-lg-5';
                $class3 = 'col-4 col-lg-5';
                if($offcanvs_position == 'left')
                {
                    $class1 = 'col-auto col-lg-2';
                    $class2 = 'col-auto col-lg-5';
                    $class3 = 'col-auto col-lg-5';
                }

                    // Primary menu area
                    $output .= '<div class="sp-top1-wrapper '. $class2 .'">';
                        
                        //Topbar
                        $output .= '<div id="sp-top1">';
                            $output .= '<div class="sp-column text-center text-lg-left">';
                                $contact = new HelixUltimateFeatureContact($data->params);
                                if(isset($contact->load_pos) && $contact->load_pos == 'before') {
                                    $output .= $contact->renderFeature();
                                    $output .= '<jdoc:include type="modules" name="top1" style="sp_xhtml" />';
                                } else {
                                    $output .= '<jdoc:include type="modules" name="top1" style="sp_xhtml" />';
                                    $output .= $contact->renderFeature();
                                }
                            $output .= '</div>'; //.sp-column
                        $output .= '</div>'; //#sp-top1

                        $output .= '<div id="sp-menu" class="d-none d-lg-block">';
                            $output .= '<div class="sp-column">';
                                $menu    = new HelixUltimateFeatureMenu($data->params);
                                if(isset($menu->load_pos) && $menu->load_pos == 'before') {
                                    $output .= $menu->renderFeature();
                                    $output .= '<jdoc:include type="modules" name="menu" style="sp_xhtml" />';
                                } else {
                                    $output .= '<jdoc:include type="modules" name="menu" style="sp_xhtml" />';
                                    $output .= $menu->renderFeature();
                                }
                            $output .= '</div>'; //.sp-column    
                        $output .= '</div>'; //#sp-menu
                    $output .= '</div>'; // /.col-sm

                    //Logo area
                    $output .= '<div id="sp-logo" class="'. $class1 .'">';
                        $output .= '<div class="sp-column">';
                            $logo    = new HelixUltimateFeatureLogo($data->params);
                            if(isset($logo->load_pos) && $logo->load_pos == 'before') {
                                $output .= $logo->renderFeature();
                                $output .= '<jdoc:include type="modules" name="logo" style="sp_xhtml" />';
                            } else {
                                $output .= '<jdoc:include type="modules" name="logo" style="sp_xhtml" />';
                                $output .= $logo->renderFeature();
                            }
                        $output .= '</div>';
                    $output .= '</div>'; // /#sp-logo

                    // Secondary menu area
                    $output .= '<div class="sp-top2-wrapper '. $class3 .'">';
                        $output .= '<div id="sp-top2">';
                            $output .= '<div class="sp-column text-center text-lg-right">';
                                $social = new HelixUltimateFeatureSocial($data->params);
                                if (isset($social->load_pos) && $social->load_pos == 'before') {
                                    $output .= $social->renderFeature();
                                    $output .= '<jdoc:include type="modules" name="top2" style="sp_xhtml" />';
                                } else {
                                    $output .= '<jdoc:include type="modules" name="top2" style="sp_xhtml" />';
                                    $output .= $social->renderFeature();
                                }
                            $output .= '</div>'; // /#sp-top2
                        $output .= '</div>'; // /.col-sm

                        $output .= '<div id="sp-menu2" class="d-none d-lg-block">';
                            $output .= '<div class="sp-column">';
                                $menu2    = new HelixUltimateFeatureMenu2($data->params);
                                if(isset($menu2->load_pos) && $menu2->load_pos == 'before') {
                                    $output .= $menu2->renderFeature();
                                    $output .= '<jdoc:include type="modules" name="menu2" style="sp_xhtml" />';
                                } else {
                                    $output .= '<jdoc:include type="modules" name="menu2" style="sp_xhtml" />';
                                    $output .= $menu2->renderFeature();
                                }
                            $output .= '</div>'; //.sp-column    
                        $output .= '</div>'; // /#sp-menu2
                    $output .= '</div>'; // /.col-sm

                $output .= '</div>';
            $output .= '</div>';
        $output .= '</div>';
    $output .= '</header>';

$output .= '</div>';
echo $output;