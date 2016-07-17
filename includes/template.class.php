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

    function render_template(){

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


}

