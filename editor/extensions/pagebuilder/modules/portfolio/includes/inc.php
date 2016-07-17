<?php

function portfolio_settings_relations(){
    /* standard format for related fields */
    $relations = array(
          "excerpt_type" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "value"    => "text-layout"
              )
          ),
          "excerpt_length" => array(
              'controls'  =>  array(
                  'relation' => 'AND',
                  array(
                    "control"  =>  "portfolio_layout_type" ,
                    "value"    => "text-layout"
                  ),
                  array(
                      "control"  =>  "excerpt_type" ,
                      "value"    =>  "excerpt"
                  ),
              )
          ),
          "excerpt_html" => array(
              'controls'  =>  array(
                  'relation' => 'AND',
                  array(
                    "control"  =>  "portfolio_layout_type" ,
                    "value"    => "text-layout"
                  ),
                  array(
                      "control"  =>  "excerpt_type" ,
                      "value"    =>  "excerpt"
                  ),
              )
          ),
          "number_columns" => array(
              'controls'  =>  array(
                array(
                  "control"  =>  "content_box_type" ,
                  "values"    => array(
                      "skin1","skin2"
                  ),
                  "type"     =>  "exclude"
                ),
              )
          ),
          "text_layout_type" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),
          "content_box_type" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),
          "content_box_border_width" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),
          "content_box_img_spacing" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),
          "content_box_img_arrow" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),
          "button_size" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),
          "button_type" => array(
              'controls'  =>  array(
                  "control"  =>  "portfolio_layout_type" ,
                  "values"    => array(
                      "grid","masonry"
                  ),
                  "type"     =>  "exclude"
              )
          ),            
         'image_skin' => array(
                'values'   =>  array(

                    'default'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                    'glossy-reflection'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'square-effect3,top_to_bottom effect3',
                              'square-effect3,bottom_to_top effect3',
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                    'simple-square'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                    'square'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                    'normal-image'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'square-effect3,top_to_bottom effect3',
                              'square-effect3,bottom_to_top effect3',
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                    'greyscale'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                        ),
                        "type"     =>  "exclude"
                    ),

                    'image-blur'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'sepia-toning-effect,sepia-toning-effect',
                              'img-reset-sepia,img-reset-sepia',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                    'sepia-toning'  =>  array(
                        "control"  =>  'image_hover_effect' ,
                        "values"    =>    array(
                              'image-blur-effect,image-blur-effect',
                              'img-reset-blur,img-reset-blur',
                              'greyscale-effect,greyscale-effect',
                              'img-reset-greyscale,img-reset-greyscale'
                        ),
                        "type"     =>  "exclude"
                    ),

                )
            ),

           'image_hover_effect' => array(
                  'values'   =>  array(

                      'square-effect3,top_to_bottom effect3'  =>  array(
                          "control"  =>  'image_skin' ,
                          "values"    =>    array(
                                "simple-square" ,"square", "default", "greyscale", "image-blur", "sepia-toning"
                          )
                      ),
                      'square-effect3,bottom_to_top effect3'  =>  array(
                          "control"  =>  'image_skin' ,
                          "values"    =>    array(
                                "simple-square" ,"square", "default", "greyscale", "image-blur", "sepia-toning"
                          )
                      ),
                      'image-blur-effect,image-blur-effect'  =>  array(
                          "control"  =>  'image_skin' ,
                          "value"    => "image-blur"
                      ),
                      'img-reset-blur,img-reset-blur'  =>  array(
                          "control"  =>  'image_skin' ,
                          "value"    => "image-blur"
                      ),
                      'sepia-toning-effect,sepia-toning-effect'  =>  array(
                          "control"  =>  'image_skin' ,
                          "value"    => "sepia-toning"
                      ),
                      'img-reset-sepia,img-reset-sepia'  =>  array(
                          "control"  =>  'image_skin' ,
                          "value"    => "sepia-toning"
                      ),
                      'greyscale-effect,greyscale-effect'  =>  array(
                          "control"  =>  'image_skin' ,
                          "value"    => "greyscale"
                      ),
                      'img-reset-greyscale,img-reset-greyscale'  =>  array(
                          "control"  =>  'image_skin' ,
                          "value"    => "greyscale"
                      )

                  )
              ),

          );

    return $relations;
}
