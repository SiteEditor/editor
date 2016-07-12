<?php
/*
Module Name: Links
Module URI: http://www.siteeditor.org/modules/links
Description: Module Links For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
add_action( 'sed_footer' , 'add_tmpls_hover_effect' );
function add_tmpls_hover_effect(){
  $src = SED_EDITOR_FOLDER_URL . 'applications/siteeditor/modules/hover-effect/images/image-hover.jpg';

     $diloge_effects='<script type="text/html" id="tmpl-dialog-image-hover-effect" title="'.__( "Hover Effect" , "site-editor" ).'">';
     $diloge_effects .='<div class="row_setting_box"><button class="remove-current-hover-effect sed-btn-red">'.__( "Remove Hover Effect" , "site-editor" ).'</button></div><div class="group-hover-effect"> ';

     echo $diloge_effects;

     $hover_effects = array(
        'example2'                                      => 'bs-example',
        'circle-effect2,left_to_right effect2'          => 'html_effect1',
        'circle-effect2,right_to_left effect2'          => 'html_effect1',
        'circle-effect2,top_to_bottom effect2'          => 'html_effect1',
        'circle-effect2,bottom_to_top effect2'          => 'html_effect1',

        'example3'                                      => 'bs-example',
        'circle-effect3,left_to_right effect3'          => 'html_effect1',
        'circle-effect3,right_to_left effect3'          => 'html_effect1',
        'circle-effect3,top_to_bottom effect3'          => 'html_effect1',
        'circle-effect3,bottom_to_top effect3'          => 'html_effect1',


        'example4'                                      => 'bs-example',
        'circle-effect4,left_to_right effect4'          => 'html_effect1',
        'circle-effect4,right_to_left effect4'          => 'html_effect1',
        'circle-effect4,top_to_bottom effect4'          => 'html_effect1',
        'circle-effect4,bottom_to_top effect4'          => 'html_effect1',

        'example6'                                      => 'bs-example',
        'circle-effect6,scale_up  effect6'              => 'html_effect1',
        'circle-effect6,scale_down effect6'             => 'html_effect1',
        'circle-effect6,scale_down_up effect6'          => 'html_effect1',

        'example7'                                      => 'bs-example',
        'circle-effect7,left_to_right effect7'          => 'html_effect1',
        'circle-effect7,right_to_left effect7'          => 'html_effect1',
        'circle-effect7,top_to_bottom effect7'          => 'html_effect1',
        'circle-effect7,bottom_to_top effect7'          => 'html_effect1',

        'example9'                                      => 'bs-example',
        'circle-effect9,left_to_right effect9'          => 'html_effect1',
        'circle-effect9,right_to_left effect9'          => 'html_effect1',
        'circle-effect9,top_to_bottom effect9'          => 'html_effect1',
        'circle-effect9,bottom_to_top effect9'          => 'html_effect1',

        'example10'                                     => 'bs-example',
        'circle-effect10,top_to_bottom effect10'        => 'html_effect1',
        'circle-effect10,bottom_to_top effect10'        => 'html_effect1',

        'example11'                                     => 'bs-example',
        'circle-effect11,left_to_right effect11'        => 'html_effect1',
        'circle-effect11,right_to_left effect11'        => 'html_effect1',
        'circle-effect11,top_to_bottom effect11'        => 'html_effect1',
        'circle-effect11,bottom_to_top effect11'        => 'html_effect1',


        'example12'                                      => 'bs-example',
        'circle-effect12,left_to_right effect12'        => 'html_effect1',
        'circle-effect12,right_to_left effect12'        => 'html_effect1',
        'circle-effect12,top_to_bottom effect12'        => 'html_effect1',
        'circle-effect12,bottom_to_top effect12'        => 'html_effect1',

        'example14'                                      => 'bs-example',
        'circle-effect14,left_to_right effect14'        => 'html_effect1',
        'circle-effect14,right_to_left effect14'        => 'html_effect1',
        'circle-effect14,top_to_bottom effect14'        => 'html_effect1',
        'circle-effect14,bottom_to_top effect14'        => 'html_effect1',

        'example15'                                     => 'bs-example',
        'circle-effect15,left_to_right effect15'        => 'html_effect1',

        'example16'                                     => 'bs-example',
        'circle-effect16,left_to_right effect16'        => 'html_effect1',
        'circle-effect16,right_to_left effect16'        => 'html_effect1',

        'example17'                                     => 'bs-example',
        'circle-effect17,effect17'                      => 'html_effect1',

        'example19'                                     => 'bs-example',
        'circle-effect19,effect19'                      => 'html_effect1',



       /*<div class="img-container">
        <div class="img"><img src="images/assets/5.jpg" alt="img"></div>
        </div>
        <div class="info-container">
        <div class="info">*/
        'example8'                                      => 'bs-example',
        'circle-effect8,left_to_right effect8'          => 'html_effect3',
        'circle-effect8,right_to_left effect8'          => 'html_effect3',
        'circle-effect8,top_to_bottom effect8'          => 'html_effect3',
        'circle-effect8,bottom_to_top effect8'          => 'html_effect3',



        //<div class="info-back">
        'example5'                                      => 'bs-example',
        'circle-effect5,effect5'                        => 'html_effect2',


        //<div class="info-back">
        'example13'                                     => 'bs-example',
        'circle-effect13,from_left_and_right effect13'  => 'html_effect2',
        'circle-effect13,top_to_bottom effect13'        => 'html_effect2',
        'circle-effect13,bottom_to_top effect13'        => 'html_effect2',



        //<div class="info-back">
        'example18'                                     => 'bs-example',
        'circle-effect18,left_to_right effect18'        => 'html_effect2',
        'circle-effect18,right_to_left effect18'        => 'html_effect2',
        'circle-effect18,top_to_bottom effect18'        => 'html_effect2',
        'circle-effect18,bottom_to_top effect18'        => 'html_effect2',


        //<div class="info-back">
        'example20'                                     => 'bs-example',
        'circle-effect20,top_to_bottom effect20'        => 'html_effect2',
        'circle-effect20,bottom_to_top effect20'        => 'html_effect2',



        //<div class="info-back">
        'example-sq9'                                   => 'bs-example',
        'square-effect9,left_to_right effect9'          => 'html_effect5',
        'square-effect9,right_to_left effect9'          => 'html_effect5',
        'square-effect9,top_to_bottom effect9'          => 'html_effect5',
        'square-effect9,bottom_to_top effect9'          => 'html_effect5',


        //'example-sq1'                                   => 'bs-example',
        //'square-effect1,left_and_right effect1'         => 'html_effect4',
        //'square-effect1,top_to_bottom effect1'          => 'html_effect4',
        //'square-effect1,bottom_to_top effect1'          => 'html_effect4',

        'example-sq2'                                   => 'bs-example',
        'square-effect2,effect2'                        => 'html_effect4',

        'example-sq3'                                   => 'bs-example',
        'square-effect3,top_to_bottom effect3'          => 'html_effect4',
        'square-effect3,bottom_to_top effect3'          => 'html_effect4',


        //'example-sq5'                                   => 'bs-example',
        //'square-effect5,left_to_right effect5'          => 'html_effect4',
        //'square-effect5,right_to_left effect5'          => 'html_effect4',


        'example-sq6'                                   => 'bs-example',
        'square-effect6,from_top_and_bottom effect6'    => 'html_effect4',
        'square-effect6,from_left_and_right effect6'    => 'html_effect4',
        'square-effect6,top_to_bottom effect6'          => 'html_effect4',
        'square-effect6,bottom_to_top effect6'          => 'html_effect4',

        'example-sq7'                                   => 'bs-example',
        'square-effect7,effect7'                        => 'html_effect4',

        'example-sq8'                                   => 'bs-example',
        'square-effect8,scale_up  effect8'              => 'html_effect4',
        'square-effect8,scale_down effect8'             => 'html_effect4',

        'example-sq10'                                  => 'bs-example',
        'square-effect10,left_to_right effect10'        => 'html_effect4',
        'square-effect10,right_to_left effect10'        => 'html_effect4',
        'square-effect10,top_to_bottom effect10'        => 'html_effect4',
        'square-effect10,bottom_to_top effect10'        => 'html_effect4',

        'example-sq11'                                  => 'bs-example',
        'square-effect11,left_to_right effect11'        => 'html_effect4',
        'square-effect11,right_to_left effect11'        => 'html_effect4',
        'square-effect11,top_to_bottom effect11'        => 'html_effect4',
        'square-effect11,bottom_to_top effect11'        => 'html_effect4',

        'example-sq12'                                  => 'bs-example',
        'square-effect12,left_to_right effect12'        => 'html_effect4',
        'square-effect12,right_to_left effect12'        => 'html_effect4',
        'square-effect12,top_to_bottom effect12'        => 'html_effect4',
        'square-effect12,bottom_to_top effect12'        => 'html_effect4',


        'example-sq13'                                  => 'bs-example',
        'square-effect13,left_to_right effect13'        => 'html_effect4',
        'square-effect13,right_to_left effect13'        => 'html_effect4',
        'square-effect13,top_to_bottom effect13'        => 'html_effect4',
        'square-effect13,bottom_to_top effect13'        => 'html_effect4',


        'example-sq14'                                  => 'bs-example',
        'square-effect14,left_to_right effect14'        => 'html_effect4',
        'square-effect14,right_to_left effect14'        => 'html_effect4',
        'square-effect14,top_to_bottom effect14'        => 'html_effect4',
        'square-effect14,bottom_to_top effect14'        => 'html_effect4',

        'example-sq15'                                  => 'bs-example',
        'square-effect15,left_to_right effect15'        => 'html_effect4',
        'square-effect15,right_to_left effect15'        => 'html_effect4',
        'square-effect15,top_to_bottom effect15'        => 'html_effect4',
        'square-effect15,bottom_to_top effect15'        => 'html_effect4',

        'example-sq16'                                  => 'bs-example',
        'image-blur-effect,image-blur-effect'           => 'html_effect6',
        'img-reset-blur,img-reset-blur'                 => 'html_effect6',

        'example-sq17'                                  => 'bs-example',
        'sepia-toning-effect,sepia-toning-effect'       => 'html_effect7',
        'img-reset-sepia,img-reset-sepia'               => 'html_effect7',

        'example-sq18'                                  => 'bs-example',
        'greyscale-effect,greyscale-effect'             => 'html_effect8',
        'img-reset-greyscale,img-reset-greyscale'       => 'html_effect8',

      );


     $i=0;
     $hover_title = "";
     foreach($hover_effects as $key=>$val) {
       if($val!=  "bs-example" ) {
              list($hover_type , $hover_class) = explode("," , $key);
              }
              switch ($val) {
                case "bs-example":
                 $i++;
                 $example = "";
                 if($i > 1){
                    $example .= "</div>";
                 }
                 if($i < 38){
                     if($i < 20){
                       $title = 'Hover Effect '.$i;
                         $example .='<h4 class="sed-hover-effect-title">'.__( $title , "site-editor" ).'</h4>
                          <div class="group-effect">
                         ';
                     }else{
                       $title = 'Hover Effect '.($i - 19);
                         $example .='<h4 class="sed-hover-effect-title">'.__( $title , "site-editor" ).'</h4>
                          <div class="group-effect">
                         ';
                     }
                 }
                 echo $example;
                break;
                case "html_effect1":
                    $html_effect1 ='
                        <div class="ih-item  hover-effect-item  circle '.$hover_class.'" data-value="'.$key.'">
                        <div class="img"><img src="'.$src.'" alt="img"></div>
                        <div class="info">
                        <div class="image-hover">
                        <div class="image-hover-inner">
                          <h3>'.__( "Heading" , "site-editor" ).'</h3>
                          <p>'.__( "Description" , "site-editor" ).'</p>
                        </div></div>
                        </div></div>
                    ';
                    echo $html_effect1;
                break;
                case "html_effect4":
                    $html_effect4 ='
                        <div class="ih-item  hover-effect-item  square  '.$hover_class.'" data-value="'.$key.'">
                        <div class="img"><img src="'.$src.'" alt="img"></div>
                        <div class="info">
                        <div class="image-hover">
                        <div class="image-hover-inner">
                          <h3>'.__( "Heading" , "site-editor" ).'</h3>
                          <p>'.__( "Description" , "site-editor" ).'</p>
                        </div></div>
                        </div></div>
                    ';
                  echo $html_effect4;
                break;
                case "html_effect2":
                  $html_effect2 ='
                      <div class="ih-item  hover-effect-item  circle '.$hover_class.'" data-value="'.$key.'">
                      <div class="img"><img src="'.$src.'" alt="img"></div>
                      <div class="info">
                      <div class="info-back">
                      <div class="image-hover">
                     <div class="image-hover-inner">
                        <h3>'.__( "Heading" , "site-editor" ).'</h3>
                        <p>'.__( "Description" , "site-editor" ).'</p>
                      </div></div></div>
                      </div></div>
                  ';
                  echo $html_effect2;
                break ;
                case "html_effect3":
                  $html_effect3 ='
                      <div class="ih-item  hover-effect-item  circle  '.$hover_class.'" data-value="'.$key.'">
                      <div class="img-container">
                      <div class="img"><img src="'.$src.'" alt="img"></div>
                      </div>
                      <div class="info-container">
                      <div class="info">
                      <div class="image-hover">
                       <div class="image-hover-inner">
                        <h3>'.__( "Heading" , "site-editor" ).'</h3>
                        <p>'.__( "Description" , "site-editor" ).'</p>
                      </div></div></div>
                      </div></div>
                  ';
                  echo $html_effect3;
                break;
                case "html_effect5":
                    $html_effect5 ='
                        <div class="ih-item  hover-effect-item  square  '.$hover_class.'" data-value="'.$key.'">
                        <div class="img"><img src="'.$src.'" alt="img"></div>
                        <div class="info">
                        <div class="info-back">
                        <div class="image-hover">
                       <div class="image-hover-inner">
                          <h3>'.__( "Heading" , "site-editor" ).'</h3>
                          <p>'.__( "Description" , "site-editor" ).'</p>
                        </div></div></div>
                        </div></div>
                    ';
                  echo $html_effect5;
                 break;
                case "html_effect6":
                    $html_effect6 ='
                        <div class="module-image ih-item  hover-effect-item '.$hover_class.'" data-value="'.$key.'">
                        <div class="img"><img class="blur" src="'.$src.'" alt="img"></div>
                        <div class="info">
                        <div class="image-hover">
                        <div class="image-hover-inner">
                          <h3>'.__( "Heading" , "site-editor" ).'</h3>
                          <p>'.__( "Description" , "site-editor" ).'</p>
                        </div></div>
                        </div></div>
                    ';
                  echo $html_effect6;
                break;
                case "html_effect7":
                    $html_effect7 ='
                        <div class="module-image ih-item  hover-effect-item '.$hover_class.'" data-value="'.$key.'">
                        <div class="img"><img class="sepia" src="'.$src.'" alt="img"></div>
                        <div class="info">
                        <div class="image-hover">
                        <div class="image-hover-inner">
                          <h3>'.__( "Heading" , "site-editor" ).'</h3>
                          <p>'.__( "Description" , "site-editor" ).'</p>
                        </div></div>
                        </div></div>
                    ';
                  echo $html_effect7;
                break;
                default:
                $html_effect8 ='
                        <div class="module-image ih-item  hover-effect-item '.$hover_class.'" data-value="'.$key.'">
                        <div class="img"><img class="roma" src="'.$src.'" alt="img"></div>
                        <div class="info">
                        <div class="image-hover">
                        <div class="image-hover-inner">
                          <h3>'.__( "Heading" , "site-editor" ).'</h3>
                          <p>'.__( "Description" , "site-editor" ).'</p>
                        </div></div>
                        </div></div>
                    ';
                  echo $html_effect8;
              }

     }


     $diloge_effects='</div></script>';
     echo $diloge_effects;
  }
