<?php

Class Template {

    /*
     * @the registry
     * @access private
     */
    private $registry;

    /*
     * @Variables array
     * @access private
     */
    private $vars = array();

    /*
     * @Variables string
     * @access public
     */
    var $template = "default";

    /*
     * @Variables string
     * @access public
     */
    var $tmpl_path = SED_TMPL_PATH;

    var $head_script = '';

    var $head_style = '';

    var $footer_script = '';

    var $current_template_path = '';

    /**
     *
     * @constructor
     *
     * @access public
     *
     * @return void
     *
     */
    function __construct($registry,$tmpl_path = SED_TMPL_PATH,$template = "default") {
    	$this->registry = $registry;
        $this->tmpl_path = $tmpl_path;
        $this->template = $template;
        $this->current_template_path = $this->tmpl_path . DS . $template;
    }


     /**
     *
     * @set undefined vars
     *
     * @param string $index
     *
     * @param mixed $value
     *
     * @return void
     *
     */
     public function __set($index, $value)
     {
            $this->vars[$index] = $value;
     }


    function show($name) {
    	$path = $this->tmpl_path . DS . $this->template . DS . $name . '.php';

    	if (file_exists($path) == false)
    	{
    		throw new Exception('Template not found in '. $path);
    		return false;
    	}

    	// Load variables
    	foreach ($this->vars as $key => $value)
    	{
    		$$key = $value;
    	}

    	include ($path);
    }

    function get_content($name){
        ob_start();

        $this->show($name);

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    function functions_template_loader($name , $base = ''){
        if(!empty($base))
            include_once( $this->current_template_path . DS . $base . DS ."functions.php"  );
        else
            include_once( $this->current_template_path . DS . "functions.php"  );

        $class = $name."Functions";
        $functions = new $class($this->registry);
        $functions->render();

    }


    function apps_loader($application){
      global $site_editor_script,$site_editor_style;

      $registry = $this->registry;
      include_once( SED_APP_PATH . DS . "application.class.php"  );
      include_once( SED_APPS_PATH . DS . $application . DS . $application.".class.php"  );
      include ( SED_APPS_PATH . DS . $application . DS . "index.php" );

    }

    function render_template(){
        $this->load_scripts();
        $this->load_styles();
    }

    function site_editor_head(){
      $output = $this->head_style;
      $output .= $this->head_script;
      return $output;
    }

    function site_editor_footer(){
      $output = $this->footer_script;
      return $output;
    }


    function load_styles(){
        global $site_editor_style;

        $basic_load_style = $site_editor_style->basic_load_style;
        $style_in_head = array();
        //var_dump($site_editor_style->registered);
        if(!empty($basic_load_style)){
            $loaded_styles = array();
            //$handles = $basic_load_style;

            foreach( $basic_load_style AS  $handle ){
                $loaded_styles[$handle] = array("handle"=>$handle , "isloaded" => false ,"order" => 0);
                //$handles[] = $handle;
            }
            $handles_sort = array();
            $i = 1;
            $load_controller = count($basic_load_style);
            while($load_controller > 0){
                $j = 0;
                foreach( $basic_load_style AS  $handle ){
                    $style = $site_editor_style->registered[$handle];

                    if( (empty($style['deps']) || !is_array($style['deps'])) && $loaded_styles[$handle]["isloaded"] === false ){
                        $loaded_styles[$handle]["isloaded"] = true;
                        $loaded_styles[$handle]["order"] = $i;
                        $load_controller--;
                    }elseif($loaded_styles[$handle]["isloaded"] === false){
                        $c = 0;
                        foreach($style['deps'] AS $dep){
                            if( $loaded_styles[$dep]["isloaded"] === true  ){
                               $c++;
                            }
                        }
                        if($c == count($style['deps'])){
                            $loaded_styles[$handle]["isloaded"] = true;
                            $loaded_styles[$handle]["order"] = $i;
                            $load_controller--;
                        }
                    }

                    $j++;
                }
                $i++;
            }
            //var_dump($loaded_styles);
            $handles_final = array();
            foreach( $basic_load_style AS  $handle ){
                $key = $loaded_styles[$handle]["order"];
                $handles_final[$key][] = $handle;
            }
            krsort($handles_final);

            foreach( $handles_final AS  $order_key ){
              foreach( $order_key AS  $handle ){
                  $style = $site_editor_style->registered[$handle];
                  $style_in_head[$style['media']][] = $site_editor_style->main_path($style['href'], $style['rtl']);
              }
            }

            if(!empty($style_in_head)){
                foreach( $style_in_head AS  $media => $styles ){
                    $media = !empty($media) ?  $media:"all" ;
                    $href = $site_editor_style->get_styles($styles);
                    $this->head_style .= "<link rel='stylesheet' href='{$href}' type='text/css' media='{$media}' />";
                }
            }


        }

    }

    function load_scripts(){
        global $site_editor_script;

        $basic_load_script = $site_editor_script->basic_load_script;

        $script_in_footer = array();
        $script_in_head = array();

        if(!empty($basic_load_script)){
            $loaded_scripts = array();
            //$handles = $basic_load_script;

            foreach( $basic_load_script AS  $handle ){
                $loaded_scripts[$handle] = array("handle"=>$handle , "isloaded" => false ,"order" => 0);
                //$handles[] = $handle;
            }
            $handles_sort = array();
            $i = 1;
            $load_controller = count($basic_load_script);
            while($load_controller > 0){
                $j = 0;
                foreach( $basic_load_script AS  $handle ){
                    $script = $site_editor_script->registered[$handle];

                    if( (empty($script['deps']) || !is_array($script['deps'])) && $loaded_scripts[$handle]["isloaded"] === false ){
                        $loaded_scripts[$handle]["isloaded"] = true;
                        $loaded_scripts[$handle]["order"] = $load_controller;
                        $load_controller--;
                    }elseif($loaded_scripts[$handle]["isloaded"] === false){
                        $c = 0;
                        foreach($script['deps'] AS $dep){
                            if( $loaded_scripts[$dep]["isloaded"] === true  ){
                               $c++;
                            }
                        }
                        if($c == count($script['deps'])){
                            $loaded_scripts[$handle]["isloaded"] = true;
                            $loaded_scripts[$handle]["order"] = $load_controller;
                            $load_controller--;
                        }
                    }

                    $j++;
                }
                $i++;
            }


            $handles_final = array();
            foreach( $basic_load_script AS  $handle ){
                $key = $loaded_scripts[$handle]["order"];
                $handles_final[$key][] = $handle;
            }
            krsort($handles_final);

            foreach( $handles_final AS  $order_key ){
              foreach( $order_key AS  $handle ){
                  $script = $site_editor_script->registered[$handle];
                  if($script['in_footer']){
                      $script_in_footer[] = $site_editor_script->main_path($script['src']);
                  }else{
                      $script_in_head[] = $site_editor_script->main_path($script['src']);
                  }
              }
            }

            if(!empty($script_in_head)){
                $src = $site_editor_script->get_scripts($script_in_head);
                $this->head_script = "<script type='text/javascript' src='{$src}' ></script>";
            }

            if(!empty($script_in_footer)){
                $src = $site_editor_script->get_scripts($script_in_footer);
                $this->footer_script = "<script type='text/javascript' src='{$src}' ></script>";
            }

        }

    }


}

