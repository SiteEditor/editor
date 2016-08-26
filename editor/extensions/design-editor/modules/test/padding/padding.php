<?php
/*
Module Name: Padding
Module URI: http://doc.zariss.com/padding
Description: Module Padding For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdPadding extends StyleEditorClass{


      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->padding();
      }

      function padding(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "layout" ,
                      "padding",
                      __("padding","site-editor") ,
                      "padding_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'padding' , 'file' => 'padding.php'),
                      'all' ,//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                          'padding_top' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage' ,
                              'type'        =>  'style-editor'
                          ),
                          'padding_right' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage' ,
                              'type'        =>  'style-editor'
                          ),
                          'padding_bottom' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage' ,
                              'type'        =>  'style-editor'
                          ),
                          'padding_left' => array(
                              'value'     => array(
                                                "default" => 0
                                             ),
                              'transport'   => 'postMessage' ,
                              'type'        =>  'style-editor'
                          ),
                          'padding_lock' => array(
                              'value'     => array(
                                                "default" => true
                                             ),
                              'transport'   => 'postMessage' ,
                              'type'        =>  'style-editor'
                          )
                      ),
                      array(
                          'sted_padding_top' => array(
                              'settings'     => array(
                                  'default'       => 'padding_top'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'min'     => 0,
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-paddings',
                                  'spinner'  => '.sed-padding-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_padding_right' => array(
                              'settings'     => array(
                                  'default'       => 'padding_right'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'min'     => 0,
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-paddings',
                                  'spinner'  => '.sed-padding-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_padding_bottom' => array(
                              'settings'     => array(
                                  'default'       => 'padding_bottom'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'min'     => 0,
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-paddings',
                                  'spinner'  => '.sed-padding-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_padding_left' => array(
                              'settings'     => array(
                                  'default'       => 'padding_left'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'min'     => 0,
                              'radius_demo' => true,
                              'lock'    => array(
                                  'id'       => 'sted-lock-paddings',
                                  'spinner'  => '.sed-padding-spinner'
                              ),
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                          'sted_padding_lock' => array(
                              'settings'     => array(
                                  'default'       => 'padding_lock'
                              ),
                              'type'    => 'spinnerlock',
                              'category' => 'style-editor',
                              'spinner' =>  '.sed-padding-spinner',
                              'min'     => 0,
                              //'max'     => 100,
                              //'step'    => 2,
                              //'page'    => 5
                          ),
                      )
                  );



       }


}
new StEdPadding();

 ?>