<?php

class SiteEditorStyle{

    /*** Declare instance ***/
    //private static $instance = NULL;
    var $library_path;

    var $registered = array();

    var $ajax_load_style = array();

    var $basic_load_style = array();
    /**
    *
    * the constructor is set to private so
    * so nobody can create a new instance using new
    *
    */
    function __construct() {
      /*** maybe set the db name here later ***/
      $this->library_path = SED_LIB_PATH ;
      $this->base_library_url = SED_EDITOR_FOLDER_URL."libraries/";
      $this->suffix = '.min';
    }


    function add( $handle, $href, $deps = array(), $rtl = false, $ver = false, $media = '' ){
        $exist_deps = true;

        if(!empty($deps)){
          if(!empty($this->registered)){
              foreach($deps AS $value){
                  //var_dump(array_key_exists($value,$this->registered));
                  //echo $handle."<br>";
                  if(array_key_exists($value,$this->registered) === false ){
                      $exist_deps = false;
                      break;
                  }
              }
          }else{
             $exist_deps = false;
          }
        }

        if($exist_deps === true){
            $this->registered[$handle] = array(
                                         "handle" => $handle,
                                         "href"   => $href,
                                         "deps"   => $deps,
                                         "rtl"    => $rtl,
                                         "ver"    => $ver,
                                         "media"  => $media
                                         );
        }else{
          echo "error: ".$handle."<br>";
        }
    }


    function default_styles(){

        //colorpicker
        $this->add("colorpicker", $this->base_library_url."colorpicker/css/spectrum".$this->suffix.".css" , array() , false, "1.0.0" );
        $this->add("colorpicker-theme", $this->base_library_url."colorpicker/css/sp-dark".$this->suffix.".css" , array('colorpicker') , false, "1.0.0" );

        //scrollbar
        $this->add( 'scrollbar', $this->base_library_url.'scrollbar/css/jquery.mCustomScrollbar.css', array(), '2.3' );

        //scrollbar
        $this->add( 'chosen', $this->base_library_url.'chosen/chosen.min.css', array(), '2.3' );
    }

    //support only zmind js
    function main_path( $href,$rtl = false ){
        if($rtl === true){
            $href = str_replace( '.min.css', '_rtl.min.css', $href );
        }
        $path = str_replace(site_url("/wp-content/"), "" , $href);

        return $path;
    }

    function get_styles( $styles_handle ){
        if(is_array($styles_handle) && !empty($styles_handle)){
            $styles = implode(",", $styles_handle);

        	if ( $concat = trim( $styles, ', ' ) ) {
                //$concat = urlencode( $concat );

        		$concat = str_split( $concat, 128 );
        		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

        		$src = SED_EDITOR_FOLDER_URL."includes/load_styles.php?c=1&amp;base_url=".urlencode( site_url("/wp-content/") )."&amp;" . $concat;
        		//echo "<script type='text/javascript' src='" . esc_attr($src) . "'></script>\n";
                return esc_attr($src);
        	}
        }
    }
    /*
    @param $load_time is onload[body] or beforeload[body] or load after action

    */
    function ajax_load_styles($styles_handle, $load_time = "onload"){

        if(is_array($styles_handle)){
            foreach( $styles_handle AS  $value){
               if(array_key_exists($value,$this->registered)){
                    $this->ajax_load_style[$load_time][] = $value;
               }
            }

        }else{
            if(array_key_exists($styles_handle,$this->registered)){
                $this->ajax_load_style[$load_time][] = $styles_handle;
            }
        }
    }


    function load_styles($styles_handle){

      $exist_deps = true;
      if(is_array($styles_handle)){
          foreach( $styles_handle AS  $value){
             if(array_key_exists($value,$this->registered)){
                $style = $this->registered[$value];
                if(!empty($style['deps'])){
                      foreach($style['deps'] AS $val){
                          //var_dump(array_key_exists($value,$this->registered));
                          //echo $handle."<br>";
                          if(!in_array($val,$this->basic_load_style) && !in_array($val,$styles_handle)){
                              $exist_deps = false;
                              break;
                          }
                      }
                }
                if($exist_deps === true){

                    $this->basic_load_style[] = $value;
                }
             }
          }

      }else{
          if(array_key_exists($styles_handle,$this->registered)){
             if(array_key_exists($styles_handle,$this->registered)){
                $style = $this->registered[$styles_handle];
                if(!empty($style['deps'])){
                      foreach($style['deps'] AS $value){
                          //var_dump(array_key_exists($value,$this->registered));
                          //echo $handle."<br>";
                          if(!in_array($value,$this->basic_load_style) && !in_array($value,$styles_handle)){
                              $exist_deps = false;
                              break;
                          }
                      }
                }
                if($exist_deps === true){
                    $this->basic_load_style[] = $styles_handle;
                }
             }
          }
      }

    }



} /*** end of class ***/
