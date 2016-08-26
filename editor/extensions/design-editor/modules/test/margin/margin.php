<?php
/*
Module Name: Margin
Module URI: http://doc.zariss.com/margin
Description: Module Margin For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdMargin extends StyleEditorClass{

      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->margin();
      }

      function margin(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "layout" ,
                      "margin" ,
                      __("Margin","site-editor") ,
                      "margin_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'margin' , 'file' => 'margin.php'),
                      'all' ,//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                          'margin_top' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'margin_right' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'margin_bottom' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'margin_left' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'margin_lock' => array(
                              'value'     => array(
                                                "default" => true
                                             ),
                              'transport'   => 'postMessage'
                          )
                      ),
                      array(
                          'sted_margin_top' => array(
                              'settings'     => array(
                                  'default'       => 'margin_top'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-margins',
                                  'spinner'  => '.sed-margin-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_margin_right' => array(
                              'settings'     => array(
                                  'default'       => 'margin_right'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-margins',
                                  'spinner'  => '.sed-margin-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_margin_bottom' => array(
                              'settings'     => array(
                                  'default'       => 'margin_bottom'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-margins',
                                  'spinner'  => '.sed-margin-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_margin_left' => array(
                              'settings'     => array(
                                  'default'       => 'margin_left'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-margins',
                                  'spinner'  => '.sed-margin-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_margin_lock' => array(
                              'settings'     => array(
                                  'default'       => 'margin_lock'
                              ),
                              'type'    => 'spinnerlock',
                              'category' => 'style-editor',
                              'spinner' =>  '.sed-margin-spinner',
                          ),
                      )
                  );

       }


}
new StEdMargin();



 ?>