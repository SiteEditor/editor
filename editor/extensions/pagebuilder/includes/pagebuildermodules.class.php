<?php
class PageBuilderModulesClass{

    public $skin;
    public $modules = array();
    public $js_plugins = array();
    public $js_modules = array();
    public $page_modules_using = array();
    public $modules_activate = array();
    public $custom_css_class_counter = 1;

    function __construct(  $args = array() ) {

        $this->set_skin_loader();

        add_action( 'sed_module_register', array($this , 'add_modules') , 11 , 1 );
        add_filter('sed_custom_js_plugins', array($this , 'sed_add_js_plugins' ) );
                                    
        if( site_editor_app_on() ){
            add_action("wp_footer" , array($this , "page_modules_using_print") );
        }
    }

    function page_modules_using_print(){
        ?>
        <script>
            var _sedAppPageModulesUsing = <?php echo wp_json_encode( $this->page_modules_using )?>;
        </script>
        <?php
    }

    function set_skin_loader(){
        require SED_BASE_PB_APP_PATH."/includes/pb-skin-loader.class.php";
        $this->skin = new PBSkinLoaderClass();
    }

    #step 3 : Add Shortcode Module To page Builder
    function add_modules( ){

        global $site_editor_app;

        $pagebuilder = $site_editor_app->pagebuilder;

        if(!empty($this->modules) && is_site_editor() ){

            $module_priorities = array();
            foreach($this->modules AS $module){
                $module_priorities[$module['name']] = $module['priority'];
            }
            asort( $module_priorities );
            foreach($module_priorities AS $name => $priority){
                $module = $this->getModuleByName( $name );
                if(!empty( $module ))
                    $pagebuilder->add_shortcode_module( $module );
            }

        }
    }

    function getModuleByName( $name ){
        if(!empty($this->modules)){
            $currentModule = array();
            foreach($this->modules AS $module){
                if($module['name'] == $name){
                    $currentModule = $module;
                    break;
                }
            }

            return $currentModule;
        }
    }

    function register_module( $module = array()){
        if(is_array( $module ) && isset($module['name']) && !empty($module['name'])){

            if(!empty($module['js_plugin'])){
                $this->js_plugins[$module['name']] = $module['js_plugin'];
                unset($module['js_plugin']);
            }

            if(!empty($module['js_module'])){
                $this->js_modules[$module['name']] = $module['js_module'];
                unset($module['js_module']);
            }

            array_push( $this->modules , array_merge(array(
                "group"       => "basic" ,
                "type_icon"   => "font" ,
                "show_settings_on_create" => true,
                "site_editor_type"  => "all" ,
                "priority"          => 100
            ) , $module) );

        }
    }

    function is_module($shortcode_name){
        $is_module = false;
        if(!empty($this->modules)){
            foreach($this->modules AS $module){
                if($module["shortcode"] == $shortcode_name){
                    $is_module =  true;
                    break;
                }
            }

        }

        return $is_module;
    }

    function generate_custom_css_class(){
        $id =  md5( time() . '-' . $this->custom_css_class_counter ++ );
        $new_class = "sed_custom_" . $id;
        return $new_class;
    }

    #step 6 : add js for save module created content & module settings & related with this module(add js plugin)
    function sed_add_js_plugins($plugin_array) {
        if(!empty($this->js_plugins) && is_array($this->js_plugins)){
            $activate_modules = SiteEditorModules::pb_module_active_list();

            foreach( $this->js_plugins AS $module_name => $url ){
                $plugin_array[$module_name] = content_url( "/" . dirname( dirname( $activate_modules[ $module_name ] ) ) . "/" . $url );
            }
        }
        return $plugin_array;
    }

	public function animation($animation){
        $animate_attr = "";
        $animate_class = "wow ";

		if( $animation[0] != "" )
			$animate_attr .= $this->set_attr( 'data-wow-delay', $animation[0] );

		if( $animation[1] != "" )
			$animate_attr .= $this->set_attr( 'data-wow-iteration', $animation[1] );

		if( $animation[2] != "")
			$animate_attr .= $this->set_attr( 'data-wow-duration', $animation[2] );

		if( $animation[3] != ""){
			$animate_class .= $animation[3] ;
		}

		if( $animation[4] != "")
			$this->set_attr( 'data-wow-offset', $attr[4] );

        return array(
            'attr'    => $animate_attr ,
            'class'   => $animate_class
        );
	}

	public function set_attr( $nameAttr, $valueAttr ){

		if( $valueAttr != "" )
		    return  $nameAttr.'="'.$valueAttr.'" ';
	}

    function array2obj($array){
        foreach ($array as $key => $value){
          if (is_array($value)){
              $array[$key] = $this->array2obj($value);
          }
        }
        return (object) $array;
    }

    function obj2array($object){
        $array = (array) $object;
        foreach ($array as $key => $value){
          if (is_object($value)){
              $array[$key] = $this->obj2array($value);
          }
        }
        return $array;
    }

}
