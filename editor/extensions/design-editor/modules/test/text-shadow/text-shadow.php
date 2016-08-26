<?php
/*
Module Name: Text Shadow
Module URI: http://doc.zariss.com/text_shadow
Description: Module Text Shadow For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdTextShadow extends StyleEditorClass{


      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->text_shadow();
      }

      function text_shadow(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "effcet" ,
                      "text-shadow" ,
                      __("Text Shadow","site-editor") ,
                      "text-shadow_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'text-shadow' , 'file' => 'text-shadow.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'text_shadow_color' => array(
                              'value'     => array(
                                                   'default' => '#000000'
                                             ),
                              'transport'   => 'postMessage'
                          ),
                      'text_shadow' => array(
                              'value'     => array(
                                                   'default' => array(
                                                      'values' => '1px 1px 0 ',
                                                   )
                                             ),
                              'transport'   => 'postMessage'
                          )

                       ),
                      array(
                       'sted_text_shadow_color' => array(
                              'settings'     => array(
                                  'default'       => 'text_shadow_color'
                              ),
                              'type'          => 'color',
                              'category' => 'style-editor'
                          ),

                       'sted_text_shadow' => array(
                              'settings'     => array(
                                  'default'       => 'text_shadow'
                              ),
                              'type'              => 'dropdown',
                              'category'          => 'style-editor',
                              'options_selector'  => '.text-shadow'
                          )
                     )
                  );


       }


}

new StEdTextShadow();

 ?>