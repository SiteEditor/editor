<?php
/*
Module Name: Box Shadow
Module URI: http://doc.zariss.com/box_shadow
Description: Module Box Shadow For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdBoxShadow extends StyleEditorClass{

      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->box_shadow();
      }

      function box_shadow(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "effcet" ,
                      "box-shadow" ,
                      __("Box Shadow","site-editor") ,
                      "box-shadow_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'box-shadow' , 'file' => 'box-shadow.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'shadow_color' => array(
                              'value'     => array(
                                                   'default' => '#000000'
                                             ),
                              'transport'   => 'postMessage'
                          ),

                       'shadow' => array(
                              'value'     => array(
                                                   'default' => array(
                                                      'values' => '1px 1px 0 0',
                                                      'inset'  => false
                                                   )
                                             ),
                              'transport'   => 'postMessage'
                          )

                       ),
                      array(
                          'sted_shadow_color' => array(
                              'settings'     => array(
                                  'default'       => 'shadow_color'
                              ),
                              'type'            => 'color',
                              'category'        => 'style-editor'
                          ),

                       'sted_shadow' => array(
                              'settings'     => array(
                                  'default'       => 'shadow'
                              ),
                              'type'                => 'shadow',
                              'category'            => 'style-editor',
                              'options_selector'    => '.shadow',
                              'selected_class'      => 'shadow_select'
                          )
                       )
                  );


       }


}
new StEdBoxShadow();

 ?>