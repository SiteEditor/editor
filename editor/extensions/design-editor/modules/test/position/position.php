<?php
/*
Module Name: Position
Module URI: http://doc.zariss.com/position
Description: Module Position For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdPosition extends StyleEditorClass{


      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->position();
      }

      function position(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "layout" ,
                      "position" ,
                      __("Position","site-editor") ,
                      "position_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'position' , 'file' => 'position.php'),
                      'all',//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                          'position' => array(
                              'value'     => array(
                                                   'default' => 'static'
                                             ),
                              'transport'   => 'postMessage'
                          )
                      ),
                      array(
                          'sted_position' => array(
                              'settings'     => array(
                                  'default'       => 'position'
                              ),
                              'type'              => 'dropdown',
                              'category' => 'style-editor',
                              'options_selector'  => '.position',
                              'selected_class'    => 'active_pos'
                          )
                      )
                  );



       }


}
new StEdPosition();

 ?>