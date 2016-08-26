<?php
/*
Module Name: Line Height
Module URI: http://doc.zariss.com/line_heights
Description: Module Line Height For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdLineHeight extends StyleEditorClass{

      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->line_height();
      }

      function line_height(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "text_editor" ,
                      "line-height" ,
                      __("Line Height","site-editor") ,
                      "line-height_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'line-height' , 'file' => 'line-height.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'line_height' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          )

                       ),
                      array(
                          'sted_line_height' => array(
                              'settings'     => array(
                                  'default'       => 'line_heightr'
                              ),
                              'type'          => '',
                              'category' => 'style-editor'
                          )
                     )
                  );


       }


}

new StEdLineHeight();

 ?>