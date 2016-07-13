<?php

class SiteEditorScript{

    /*** Declare instance ***/
    //private static $instance = NULL;
    var $library_path;

    var $registered = array();

    var $ajax_load_script = array();

    var $basic_load_script = array();
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


    function add( $handle, $src, $deps = array(), $ver = false, $in_footer = null ){
        wp_register_script( $handle, $src, $deps, $ver, $in_footer );
        /*$exist_deps = true;

        if(!empty($deps)){
          if(!empty($this->registered)){
              foreach($deps AS $value){
                  //var_dump(array_key_exists($value,$this->registered));
                  //echo $handle."<br>";
                  if(array_key_exists($value,$this->registered) === false){
                      $exist_deps = false;
                      //break;
                  }
              }
          }else{
             $exist_deps = false;
          }
        }

        if($exist_deps === true){
            $this->registered[$handle] = array(
                                         "handle" => $handle,
                                         "src"    => $src,
                                         "deps"   => $deps,
                                         "ver"    => $ver,
                                         "in_footer"   => $in_footer
                                         );
        }else{
          echo "error: ".$handle."<br>";
        }*/
    }


    function default_scripts(){

        $this->add( 'yepnope', $this->base_library_url.'yepnope/yepnope.min.js', array(), '2.5.6' );

        $this->add( 'modernizr', $this->base_library_url.'modernizr/modernizr.custom.min.js', array('yepnope'), '2.8.1' );

        $this->add( 'jquery-livequery', $this->base_library_url.'livequery/jquery.livequery.min.js', array( 'jquery' ) , '1.0.0' );

        //jquery css 3 support
        $this->add( 'jquery-css', $this->base_library_url.'jquery/jquery.css.min.js', array('jquery'), '2.3' );

        //scrollbar
        $this->add( 'jquery-scrollbar', $this->base_library_url.'scrollbar/js/jquery.mCustomScrollbar.concat.min.js', array('jquery'), '2.3' );

        $this->add( 'multi-level-box', $this->base_library_url.'multilevelbox/multiLevelBox.min.js', array('jquery'), '2.3' );

        //site editor drag & drop
        $this->add( 'sed-drag-drop', $this->base_library_url.'jquery/jquery.drag-drop.min.js', array('jquery'), '2.3' );

        // bootstrap
        $this->add("bootstrap-affix", $this->base_library_url."bootstrap/js/affix/affix".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-alert", $this->base_library_url."bootstrap/js/alert/alert".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-button", $this->base_library_url."bootstrap/js/button/button".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-carousel", $this->base_library_url."bootstrap/js/carousel/carousel".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-collapse", $this->base_library_url."bootstrap/js/collapse/collapse".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-dropdown", $this->base_library_url."bootstrap/js/dropdown/dropdown".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-modal", $this->base_library_url."bootstrap/js/modal/modal".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-popover", $this->base_library_url."bootstrap/js/popover/popover".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-scrollspy", $this->base_library_url."bootstrap/js/scrollspy/scrollspy".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-tab", $this->base_library_url."bootstrap/js/tab/tab".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-tooltip", $this->base_library_url."bootstrap/js/tooltip/tooltip".$this->suffix.".js" , array('jquery') , "3.3.5" );
        $this->add("bootstrap-transition", $this->base_library_url."bootstrap/js/transition/transition".$this->suffix.".js" , array('jquery') , "3.3.5" );

        //color picker
        $this->add("sed-colorpicker", $this->base_library_url."colorpicker/js/spectrum".$this->suffix.".js" , array('jquery') , "1.0.0" );

        //site editor
        $this->add("seduploader", $this->base_library_url."media/uploader/js/siteeditor.plupload".$this->suffix.".js" , array('jquery' , 'plupload' ) , "1.0.0" );

        //site editor
        $this->add("siteeditor-css", $this->base_library_url."siteeditor/siteEditorCss".$this->suffix.".js" , array('jquery' ) , "1.0.0" );

        $this->add("siteeditor-base", $this->base_library_url."siteeditor/siteeditor-base".$this->suffix.".js" , array('jquery','underscore' ) , "1.0.0" );

        $this->add("siteeditor-shortcode", $this->base_library_url."siteeditor/siteeditor-shortcode".$this->suffix.".js" , array('jquery','underscore' ) , "1.0.0" );

        $this->add("siteeditor-ajax", $this->base_library_url."siteeditor/siteeditor-ajax".$this->suffix.".js" , array( 'siteeditor-base','underscore' ) , "1.0.0" );

        $this->add("siteeditor-modules-scripts", $this->base_library_url."siteeditor/siteeditor-modules-scripts".$this->suffix.".js" , array( 'siteeditor-base','underscore' ) , "1.0.0" );

        //$upload_dir = wp_upload_dir();
        //$upload_url = $upload_dir['baseurl'];
        //$this->add("siteeditor", $upload_url."/site-editor/siteeditor".$this->suffix.".js" , array('jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker' ) , "1.0.0" );

        $this->add("siteEditorControls", $this->base_library_url."siteeditor/core/siteEditorControls.js" , array('jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("styleEditorControls", $this->base_library_url."siteeditor/core/styleEditorControls.js" , array( 'siteEditorControls', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("pbModulesControls", $this->base_library_url."siteeditor/core/pbModulesControls.js" , array( 'styleEditorControls', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("mediaClass", $this->base_library_url."siteeditor/modules/mediaClass.js" , array( 'pbModulesControls', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("appPreviewClass", $this->base_library_url."siteeditor/modules/appPreviewClass.js" , array( 'mediaClass', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("appTemplateClass", $this->base_library_url."siteeditor/modules/appTemplateClass.js" , array( 'appPreviewClass', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("pagebuilder", $this->base_library_url."siteeditor/plugins/pagebuilder/plugin.min.js" , array( 'appTemplateClass', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("contextmenu", $this->base_library_url."siteeditor/plugins/contextmenu/plugin.min.js" , array( 'pagebuilder', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("sed-settings", $this->base_library_url."siteeditor/plugins/settings/plugin.min.js" , array( 'contextmenu', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );
        $this->add("sed-save", $this->base_library_url."siteeditor/plugins/save/plugin.min.js" , array( 'sed-settings', 'jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css',   'seduploader',   'sed-colorpicker','underscore' ) , "1.0.0" );

        //chosen
        $this->add("chosen", $this->base_library_url."chosen/chosen.jquery".$this->suffix.".js" , array('jquery'  ) , "1.1.0" );


    }

    //support  js
    function main_path( $href,$rtl = false ){

        $path = str_replace(SED_EDITOR_FOLDER_URL, "" , $href);

        return $path;
    }

    function get_scripts( $scripts_handle ){
        if(is_array($scripts_handle) && !empty($scripts_handle)){
            $scripts_handle = implode("," , $scripts_handle);

        	if ( $concat = trim( $scripts_handle, ', ' ) ) {
                //$concat = urlencode( $concat );

        		$concat = str_split( $concat, 128 );
        		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

        		$src = SED_EDITOR_FOLDER_URL."includes/load_scripts.php?c=1&amp;" . $concat;
        		//echo "<script type='text/javascript' src='" . esc_attr($src) . "'></script>\n";
                return esc_attr($src);
        	}
        }
    }
    /*
    @param $load_time is onload[body] or beforeload[body] or load after action

    */
    function ajax_load_scripts($scripts_handle, $load_time = "onload"){

        if(is_array($scripts_handle)){
            foreach( $scripts_handle AS  $value){
               if(array_key_exists($value,$this->registered)){
                    $this->ajax_load_script[$load_time][] = $value;
               }
            }

        }else{
            if(array_key_exists($scripts_handle,$this->registered)){
                $this->ajax_load_script[$load_time][] = $scripts_handle;
            }
        }
    }


    function load_scripts($scripts_handle){

        foreach( $scripts_handle AS  $handle) {
            wp_enqueue_script($handle);
        }
      /*$exist_deps = true;
      if(is_array($scripts_handle)){
          foreach( $scripts_handle AS  $value){
             if(array_key_exists($value,$this->registered)){
                $script = $this->registered[$value];
                if(!empty($script['deps'])){
                      foreach($script['deps'] AS $val){
                          //var_dump(array_key_exists($value,$this->registered));
                          //echo $handle."<br>";
                          if(!in_array($val,$this->basic_load_script) && !in_array($val,$scripts_handle)){
                              $exist_deps = false;
                              break;
                          }
                      }
                }
                if($exist_deps === true){
                    $this->basic_load_script[] = $value;
                }
             }
          }

      }else{
          if(array_key_exists($scripts_handle,$this->registered)){
             if(array_key_exists($scripts_handle,$this->registered)){
                $script = $this->registered[$scripts_handle];
                if(!empty($script['deps'])){
                      foreach($script['deps'] AS $value){
                          //var_dump(array_key_exists($value,$this->registered));
                          //echo $handle."<br>";
                          if(!in_array($value,$this->basic_load_script) && !in_array($value,$scripts_handle)){
                              $exist_deps = false;
                              break;
                          }
                      }
                }
                if($exist_deps === true){
                    $this->basic_load_script[] = $scripts_handle;
                }
             }
          }
      }*/

    }



} /*** end of class ***/

