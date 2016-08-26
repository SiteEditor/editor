<?php
/*
Module Name: Corner Sizes
Module URI: http://doc.zariss.com/corner_sizes
Description: Module Corner Sizes For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdCornerSize extends StyleEditorClass{

      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->corner_sizes();
      }

      function corner_sizes(){
      global $site_editor_app;
          $site_editor_app->toolbar->add_element(
                          "style-editor" ,
                          "effcet" ,
                          "corner-sizes" ,
                          __("Corner Sizes","site-editor") ,
                          "corner-sizes_element" ,     //$func_action
                          "" ,                //icon
                          "" ,  //$capability=
                          array(),
                          array( "row" => 1 ),
                          array('module' => 'corner-sizes' , 'file' => 'corner-sizes.php'),
                          "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                          array(
                          'border_radius_tr' => array(
                                  'value'     => array(
                                                    "default" => 0
                                                 ),
                                  'transport'   => 'postMessage'
                              ),
                              'border_radius_tl' => array(
                                  'value'     =>  array(
                                                    "default" => 0
                                                 ),
                                  'transport'   => 'postMessage'
                              ),
                              'border_radius_br' => array(
                                  'value'     =>  array(
                                                    "default" => 0
                                                 ),
                                  'transport'   => 'postMessage'
                              ),
                              'border_radius_bl' => array(
                                  'value'     =>  array(
                                                    "default" => 0
                                                 ),
                                  'transport'   => 'postMessage'
                              ),
                              'border_radius_lock' => array(
                                  'value'     => array(
                                                    "default" => true
                                                 ),
                                  'transport'   => 'postMessage'
                              )
                           ),
                          array(
                          'sted_border_radius_tr' => array(
                                  'settings'     => array(
                                      'default'       => 'border_radius_tr'
                                  ),
                                  'type'    => 'spinner',
                                  'category' => 'style-editor',
                                  'min'     => 0,
                                  'radius_demo' => true,
                                  'lock'    => array(
                                      'id'       => 'sted-lock-corners',
                                      'spinner'  => '.sed-corner-spinner'
                                  ),
                                  //'max'     => 100,
                                  //'step'    => 2,
                                  //'page'    => 5
                              ),
                              'sted_border_radius_tl' => array(
                                  'settings'     => array(
                                      'default'       => 'border_radius_tl'
                                  ),
                                  'type'    => 'spinner',
                                  'category' => 'style-editor',
                                  'min'     => 0,
                                  'radius_demo' => true,
                                  'lock'    => array(
                                      'id'       => 'sted-lock-corners',
                                      'spinner'  => '.sed-corner-spinner'
                                  ),
                                  //'max'     => 100,
                                  //'step'    => 2,
                                  //'page'    => 5
                              ),
                              'sted_border_radius_br' => array(
                                  'settings'     => array(
                                      'default'       => 'border_radius_br'
                                  ),
                                  'type'    => 'spinner',
                                  'category' => 'style-editor',
                                  'min'     => 0,
                                  'radius_demo' => true,
                                  'lock'    => array(
                                      'id'       => 'sted-lock-corners',
                                      'spinner'  => '.sed-corner-spinner'
                                  ),
                                  //'max'     => 100,
                                  //'step'    => 2,
                                  //'page'    => 5
                              ),
                              'sted_border_radius_bl' => array(
                                  'settings'     => array(
                                      'default'       => 'border_radius_bl'
                                  ),
                                  'type'    => 'spinner',
                                  'category' => 'style-editor',
                                  'min'     => 0,
                                  'radius_demo' => true,
                                  'lock'    => array(
                                      'id'       => 'sted-lock-corners',
                                      'spinner'  => '.sed-corner-spinner'
                                  ),
                                  //'max'     => 100,
                                  //'step'    => 2,
                                  //'page'    => 5
                              ),
                              'sted_border_radius_lock' => array(
                                  'settings'     => array(
                                      'default'       => 'border_radius_lock'
                                  ),
                                  'type'    => 'spinnerlock',
                                  'category' => 'style-editor',
                                  'spinner' =>  '.sed-corner-spinner',
                                  'min'     => 0,
                                  //'max'     => 100,
                                  //'step'    => 2,
                                  //'page'    => 5
                              )
                          )
         );
      }


}

new StEdCornerSize();


 ?>