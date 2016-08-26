<?php
/*
Module Name: Trancparency
Module URI: http://doc.zariss.com/trancparency
Description: Module Trancparency For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdTrancparency extends StyleEditorClass{



      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->trancparency();
      }

      function trancparency(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "effcet" ,
                      "trancparency" ,
                      __("Trancparency","site-editor") ,
                      "trancparency_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'trancparency' , 'file' => 'trancparency.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                          'trancparency' => array(
                              'value'     => array(
                                                   'default' => 0
                                             ),
                              'transport'   => 'postMessage'
                          )                    
                       ),
                      array(
                          'sted_trancparency' => array(
                              'settings'     => array(
                                  'default'       => 'trancparency'
                              ),
                              'type'    => 'spinner',
                              'category' => 'style-editor',
                              'min'     => 0,
                              'max'     => 100
                          )
                       )
                  );


       }


}

new StEdTrancparency();

 ?>