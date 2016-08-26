<?php
/*
Module Name: Border
Module URI: http://doc.zariss.com/border
Description: Module Border For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdBorder extends StyleEditorClass{

      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->border();
      }

     function border(){
     global $site_editor_app;
     $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "effcet" ,
                      "border" ,
                      __("Border","site-editor") ,
                      "border_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'border' , 'file' => 'border.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                          'border_color' => array(
                              'value'     => array(
                                                   'default' => '#FFFFFF'
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'border_width' => array(
                              'value'     => array(
                                                   'default' => 0
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'border_style' => array(
                              'value'     => array(
                                                   'default' => 'none'
                                             ),
                              'transport'   => 'postMessage'
                          ),
                          'border_side' => array(
                              'value'     => array(
                                                   'default' => array()
                                             ),
                              'transport'   => 'postMessage'
                          )
                       ),
                      array(
                          'sted_border_color' => array(
                              'settings'     => array(
                                  'default'       => 'border_color'
                              ),
                              'type'          => 'color',
                              'category' => 'style-editor'
                          ),
                          'sted_border_width' => array(
                              'settings'     => array(
                                  'default'       => 'border_width'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'min'     => 0,
                          ),
                          'sted_border_style' => array(
                              'settings'     => array(
                                  'default'       => 'border_style'
                              ),
                              'type'              => 'dropdown',
                              'category' => 'style-editor',
                              'options_selector'  => '.border',
                              'selected_class'    => 'active_border'
                          ),
                          'sted_border_side' => array(
                              'settings'     => array(
                                  'default'       => 'border_side'
                              ),
                              'type'              => 'checkboxes',
                              'category' => 'style-editor',
                              'options_selector'  => '.border-side'
                          )

                       )
                  );


       }


}

new StEdBorder();

 ?>