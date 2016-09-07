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
      $this->base_library_url = SED_BASE_URL."libraries/";
      $this->suffix = '.min';
    }


    function add( $handle, $src, $deps = array(), $ver = false, $in_footer = null ){
        $exist_deps = true;

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
        }
    }


    function default_scripts(){



    	// jQuery   //jquery-core
    	//$this->add( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '1.10.2' );
    	$this->add( 'jquery', $this->base_library_url.'jquery/jquery.js', array(), '1.10.2' );
    	//$this->add( 'jquery-migrate', $this->base_library_url."jquery/jquery-migrate".$this->suffix.".js", array(), '1.2.1' );

        $this->add( 'underscore', $this->base_library_url.'underscore/underscore-min.js', array(), '1.5.0' );
        //
        //$this->add( 'prototype', $this->base_library_url.'scriptaculous/lib/prototype.js', array('jquery'), '2.3' );

        //$this->add( 'scriptaculous', $this->base_library_url.'scriptaculous/src/scriptaculous.js', array('jquery'), '2.3' );


        //$this->add( 'angularjs', $this->base_library_url.'angularjs/angular.min.js', array(), '1.4.8' );

        //$this->add( 'angular-route', $this->base_library_url.'angularjs/angular-route.min.js', array(), '1.4.8' );

        $this->add( 'yepnope', $this->base_library_url.'yepnope/yepnope.min.js', array(), '2.5.6' );

        $this->add( 'modernizr', $this->base_library_url.'modernizr/modernizr.custom.min.js', array('yepnope'), '2.8.1' );

        $this->add( 'ajax-queue', $this->base_library_url.'jquery/jquery.ajaxQueue.min.js', array('jquery'), '1.0.0' );

        $this->add( 'jquery-livequery', $this->base_library_url.'livequery/jquery.livequery.min.js', array( 'jquery' ) , '1.0.0' );

        $this->add( 'jquery-contenteditable', $this->base_library_url.'jquery/jquery.contenteditable.min.js', array( 'jquery' ) , '1.0.0' );

        $this->add( 'jquery-browser', $this->base_library_url.'jquery/jquery.browser.min.js', array('jquery'), '2.3' );

        $this->add("jquery-append", $this->base_library_url."jquery/jquery.append.min.js" , array( 'jquery') );

        //jquery css 3 support
        $this->add( 'jquery-css', $this->base_library_url.'jquery/jquery.css.min.js', array('jquery'), '2.3' );

        //scrollbar
        $this->add( 'jquery-scrollbar', $this->base_library_url.'scrollbar/js/jquery.mCustomScrollbar.concat.min.js', array('jquery'), '2.3' );

        $this->add( 'multi-level-box', $this->base_library_url.'multilevelbox/multiLevelBox.min.js', array('jquery'), '2.3' );

        $this->add( 'iframe-resizer', $this->base_library_url.'iframeresizer/js/iframeResizer.min.js', array('jquery'), '2.3' );

        //site editor drag & drop
        $this->add( 'sed-drag-drop', $this->base_library_url.'jquery/jquery.drag-drop.min.js', array('jquery'), '2.3' );

        // bootstrap
        $this->add("bootstrap", $this->base_library_url."bootstrap/js/bootstrap".$this->suffix.".js" , array('jquery') , "1.2.5" );

    	// full jQuery UI
        $this->add( 'jquery-ui-full', $this->base_library_url.'jquery/ui/full-js/jquery.ui.min.js', array('jquery','bootstrap'), '1.10.3' );


    	$this->add( 'jquery-ui-core', $this->base_library_url.'jquery/ui/jquery.ui.core.min.js', array('jquery'), '1.10.3', 1 );
        $this->add( 'jquery-effects-core', $this->base_library_url.'jquery/ui/jquery.ui.effect.min.js', array('jquery'), '1.10.3', 1 );

    	$this->add( 'jquery-effects-blind', $this->base_library_url.'jquery/ui/jquery.ui.effect-blind.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-bounce', $this->base_library_url.'jquery/ui/jquery.ui.effect-bounce.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-clip', $this->base_library_url.'jquery/ui/jquery.ui.effect-clip.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-drop', $this->base_library_url.'jquery/ui/jquery.ui.effect-drop.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-explode', $this->base_library_url.'jquery/ui/jquery.ui.effect-explode.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-fade', $this->base_library_url.'jquery/ui/jquery.ui.effect-fade.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-fold', $this->base_library_url.'jquery/ui/jquery.ui.effect-fold.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-highlight', $this->base_library_url.'jquery/ui/jquery.ui.effect-highlight.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-pulsate', $this->base_library_url.'jquery/ui/jquery.ui.effect-pulsate.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-scale', $this->base_library_url.'jquery/ui/jquery.ui.effect-scale.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-shake', $this->base_library_url.'jquery/ui/jquery.ui.effect-shake.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-slide', $this->base_library_url.'jquery/ui/jquery.ui.effect-slide.min.js', array('jquery-effects-core'), '1.10.3', 1 );
    	$this->add( 'jquery-effects-transfer', $this->base_library_url.'jquery/ui/jquery.ui.effect-transfer.min.js', array('jquery-effects-core'), '1.10.3', 1 );

        $this->add( 'jquery-ui-widget', $this->base_library_url.'jquery/ui/jquery.ui.widget.min.js', array('jquery'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-accordion', $this->base_library_url.'jquery/ui/jquery.ui.accordion.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.10.3', 1 );
        $this->add( 'jquery-ui-position', $this->base_library_url.'jquery/ui/jquery.ui.position.min.js', array('jquery'), '1.10.3', 1 );
        $this->add( 'jquery-ui-menu', $this->base_library_url.'jquery/ui/jquery.ui.menu.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.10.3', 1 );
    	$this->add( 'jquery-ui-autocomplete', $this->base_library_url.'jquery/ui/jquery.ui.autocomplete.min.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-menu'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-button', $this->base_library_url.'jquery/ui/jquery.ui.button.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-datepicker', $this->base_library_url.'jquery/ui/jquery.ui.datepicker.min.js', array('jquery-ui-core'), '1.10.3', 1 );
        $this->add( 'jquery-ui-mouse', $this->base_library_url.'jquery/ui/jquery.ui.mouse.min.js', array('jquery-ui-widget'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-draggable', $this->base_library_url.'jquery/ui/jquery.ui.draggable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-droppable', $this->base_library_url.'jquery/ui/jquery.ui.droppable.min.js', array('jquery-ui-draggable'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-progressbar', $this->base_library_url.'jquery/ui/jquery.ui.progressbar.min.js', array('jquery-ui-widget'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-resizable', $this->base_library_url.'jquery/ui/jquery.ui.resizable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.3', 1 );
        $this->add( 'jquery-ui-dialog', $this->base_library_url.'jquery/ui/jquery.ui.dialog.min.js', array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-selectable', $this->base_library_url.'jquery/ui/jquery.ui.selectable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-slider', $this->base_library_url.'jquery/ui/jquery.ui.slider.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-sortable', $this->base_library_url.'jquery/ui/jquery.ui.sortable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-spinner', $this->base_library_url.'jquery/ui/jquery.ui.spinner.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button' ), '1.10.3', 1 );
    	$this->add( 'jquery-ui-tabs', $this->base_library_url.'jquery/ui/jquery.ui.tabs.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.10.3', 1 );
    	$this->add( 'jquery-ui-tooltip', $this->base_library_url.'jquery/ui/jquery.ui.tooltip.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.10.3', 1 );

        $this->add( 'jquery-ui-timepicker', $this->base_library_url.'jquery/ui/jquery-ui-timepicker-addon.min.js', array('jquery-ui-full'), '1.10.3', 1 );

        $this->add( 'jquery-ui-droppable-iframe-fix', $this->base_library_url.'jquery/ui/jquery-ui-droppable-iframe-fix.js', array( 'jquery-ui-full' ), '1.10.4' );
        // tinymce
        $this->add("tinymce", $this->base_library_url."tinymce/tinymce".$this->suffix.".js" , array() , "1.2.5" );

        //color picker
        $this->add("colorpicker", $this->base_library_url."colorpicker/js/spectrum".$this->suffix.".js" , array('jquery') , "1.0.0" );

        //color picker
        $this->add("plupload", $this->base_library_url."media/uploader/js/plupload.full".$this->suffix.".js" , array() , "1.0.0" );

        //site editor
        $this->add("seduploader", $this->base_library_url."media/uploader/js/siteeditor.plupload".$this->suffix.".js" , array('jquery' , 'plupload' ) , "1.0.0" );


        //site editor
        $this->add("siteeditor-css", $this->base_library_url."siteeditor/siteEditorCss".$this->suffix.".js" , array('jquery' ) , "1.0.0" );

        $this->add("undomanager", $this->base_library_url."siteeditor/undomanager".$this->suffix.".js" , array( ) , "1.0.0" );
        $this->add("sed-undomanager", $this->base_library_url."siteeditor/sed-undomanager".$this->suffix.".js" , array( 'jquery', 'undomanager' ) , "1.0.0" );

        $this->add("siteeditor-base", $this->base_library_url."siteeditor/siteeditor-base".$this->suffix.".js" , array('jquery' ) , "1.0.0" );

        $this->add("siteeditor-ajax", $this->base_library_url."siteeditor/siteeditor-ajax".$this->suffix.".js" , array( 'siteeditor-base' ) , "1.0.0" );

        $this->add("siteeditor-modules-scripts", $this->base_library_url."siteeditor/siteeditor-modules-scripts".$this->suffix.".js" , array( 'siteeditor-base' ) , "1.0.0" );

        $this->add("siteeditor", $this->base_library_url."siteeditor/siteeditor".$this->suffix.".js" , array('jquery' , "siteeditor-modules-scripts"  , 'siteeditor-ajax' , 'siteeditor-css', 'undomanager' , 'sed-undomanager', 'seduploader', 'bootstrap' , 'jquery-ui-full' , 'colorpicker' ) , "1.0.0" );

        //chosen
        $this->add("chosen", $this->base_library_url."chosen/chosen.jquery".$this->suffix.".js" , array('jquery'  ) , "1.1.0" );


    }

    //support only zmind js
    function main_path( $href,$rtl = false ){

        $path = str_replace(SED_BASE_URL, "" , $href);

        return $path;
    }

    function get_scripts( $scripts_handle ){
        if(is_array($scripts_handle) && !empty($scripts_handle)){
            $scripts_handle = implode("," , $scripts_handle);

        	if ( $concat = trim( $scripts_handle, ', ' ) ) {
                //$concat = urlencode( $concat );

        		$concat = str_split( $concat, 128 );
        		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

        		$src = SED_BASE_URL."includes/load_scripts.php?c=1&amp;" . $concat;
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

      $exist_deps = true;
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
      }

    }



} /*** end of class ***/

