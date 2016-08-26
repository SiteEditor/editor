<?php
/*
Module Name: Gradient
Module URI: http://doc.zariss.com/gradient
Description: Module Gradient For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdGradient extends StyleEditorClass{
      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->gradient();
      }

      function gradient(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "effcet" ,
                      "gradient" ,
                      __("Gradient","site-editor") ,
                      "gradient_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'gradient' , 'file' => 'gradient.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array( ),
                      array(
                          'sted_gradient' => array(
                              'settings'     => array(
                                  'default'       => 'background_gradient'
                              ),
                              'type'              => 'gradient',
                              'category' => 'style-editor',
                              'options_selector'  => '.sed-gradient',
                              'selected_class'    => 'gradient_select'
                          )
                      )
                  );



       }


}

new StEdGradient();

 ?>