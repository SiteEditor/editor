<?php
/*
Module Name: Font Family
Module URI: http://doc.zariss.com/font_family
Description: Module Font Family For Style Editor
Author: Parsa Atef
Author URI: http://www.zariss.com/products/styleeditor
Version: 1.0.0
*/
class StEdFont extends StyleEditorClass{

      function __construct(){
          $this->ready();
      }

      function ready(){
          $this->font_family();
          $this->font_size();
          $this->font_ppt_biu();
          $this->font_align();
          $this->font_color();
      }



      function font_family(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "text_editor" ,
                      "font-family" ,
                      __("Font Family","site-editor") ,
                      "font-family_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'font' , 'file' => 'font-family.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'font_family' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          )

                       ),
                      array(
                      'sted_font_family' => array(
                              'settings'     => array(
                                  'default'       => 'font_family'
                              ),
                              'type'          => 'dropdown',
                              'category' => 'style-editor'
                          )
                       )
                  );


       }

      function font_size(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "text_editor" ,
                      "font-size" ,
                      __("Font Size","site-editor") ,
                      "font-size_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'font' , 'file' => 'font-size.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'font_size' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          )

                       ),
                      array(
                          'sted_font_size' => array(
                              'settings'     => array(
                                  'default'       => 'font_size'
                              ),
                              'type'          => 'dropdown',
                              'category' => 'style-editor'
                          )
                       )
                  );


       }

      function font_ppt_biu(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "text_editor" ,
                      "font-ppt-biu" ,
                      __("Font","site-editor") ,
                      "font-ppt-biu_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 1 ),
                      array('module' => 'font' , 'file' => 'font-ppt-biu.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'font_weight' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          ),

                      'font_style' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          ),
                      'text_decoration' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          )
                       ),
                      array(
                          'sted_font_weight' => array(
                              'settings'     => array(
                                  'default'       => 'font_weight'
                              ),
                              'type'          => '',
                              'category' => 'style-editor'
                          ),
                        'sted_font_style' => array(
                              'settings'     => array(
                                  'default'       => 'font_style'
                              ),
                              'type'          => '',
                              'category' => 'style-editor'
                          ),
                        'sted_underline' => array(
                              'settings'     => array(
                                  'default'       => 'text_decoration'
                              ),
                              'type'          => '',
                              'category' => 'style-editor'
                          )
                        )


          );
       }

      function font_align(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "text_editor" ,
                      "font-align" ,
                      __("Font Weight","site-editor") ,
                      "font-align_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'font' , 'file' => 'font-align.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'text_align' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage' ,
                              'type'        =>  'style-editor'
                          ),
                      array(
                       'sted_font_align' => array(
                              'settings'     => array(
                                  'default'       => 'text_align'
                              ),
                              'type'          => '',
                              'category' => 'style-editor'
                          )
                      )
                   )


          );
       }



      function font_color(){
      global $site_editor_app;
      $site_editor_app->toolbar->add_element(
                      "style-editor" ,
                      "text_editor" ,
                      "font-color" ,
                      __("Font Color","site-editor") ,
                      "font-color_element" ,     //$func_action
                      "" ,                //icon
                      "" ,  //$capability=
                      array(),
                      array( "row" => 2 ),
                      array('module' => 'font' , 'file' => 'font-color.php'),
                      "all",//array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
                      array(
                      'font_color' => array(
                              'value'     => array(
                                                   'default' => ''
                                             ),
                              'transport'   => 'postMessage'
                          ),
                       ),
                      array(
                          'sted_font_color' => array(
                              'settings'     => array(
                                  'default'       => 'font_color'
                              ),
                              'type'          => '',
                              'category' => 'style-editor'
                          )
                       )
                  );


       }



}

new StEdFont();



 ?>