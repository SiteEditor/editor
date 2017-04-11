<?php
class PBSkinLoaderClass{

    public $module;
    public $shortcode;
    public $modules_path;
    public $modules_base_url;
    public $skin_def_screenshot;
    public $js_tpl_skin = array();
    public $style_tpl_skin = array();
    public $skin_less_files = array();
    public $skin_vars = array();
    public $pagebuilder;
    public $patterns;
    public $scripts = array();
    public $styles = array();
    public $loaded_skins = array();
    public $files_loaded = array();

    function __construct(  $args = array() ) {

        $this->skin_def_screenshot = SED_PB_IMAGES_URL . "skin_def_screenshot.png";

        add_action( 'site_editor_ajax_load_skins' , array($this,'pb_module_load_skins') );
        add_action( 'sed_footer' , array($this,'add_tmpls_modules_skins') );
        add_filter( "sed_app_refresh_nonces", array($this,'set_nonces') , 10 , 2);

        if( site_editor_app_on() ){
            add_action( 'wp_footer', array( $this, 'shortcode_skin_js_tpl' ), 25 );
        }

	}                 

    function shortcode_skin_js_tpl(){

       global $sed_apps;

       $activate_modules = SiteEditorModules::pb_module_active_list();
       $activate_modules_names = array_keys( $activate_modules );
       $modules = $sed_apps->module_info;

        foreach( $activate_modules_names AS $module_name  ){
            $module_info = $modules[$module_name];
            $skins = $module_info['skins'];
            foreach( $skins AS $skin => $skin_info ){
                $tpls = $skin_info['tpls'];
                if( !empty( $tpls ) ){
                    foreach( $tpls AS $tpl_file ){
                        $tpl_file = WP_CONTENT_DIR . DS . $tpl_file;
                        $content = $this->get_content( $tpl_file );
                        $id = "sed-tpl-" . $skin . "-" . substr( basename($tpl_file) , 0 , -4 ) . "-" . $module_name;
                        ?>
                        <script type="text/x-handlebars-template" id="<?php echo $id; ?>">
                            <?php echo $content;?>
                        </script>
                        <?php
                    }
                }

            }
        }


    }

    public function set_nonces( $nonces , $manager ){

        $nonces['pbSkins'] = array(
            'load'    =>  wp_create_nonce( 'sed_app_module_load_skins_' . $manager->get_stylesheet() )
        );

        return $nonces;
    }

    function add_tmpls_modules_skins(){
        echo '<script type="text/html" class="sed-dialog" id="tmpl-dialog-modules-skins" title="'.__( "Skins" , "site-editor" ).'"><div class="loading skin-loading"></div><div class="error error-load-skins"><span></span></div> <div class="skins-dialog-inner"></div> </script>';
    }

    public function pb_module_load_skins(){
      
        SED()->editor->manager->check_ajax_handler('module_load_skins' , 'sed_app_module_load_skins');

        do_action( 'sed_shortcode_register' );
        
        if(!empty( $_POST['module'] )){
            $this->module = $_POST['module'];
            $this->shortcode = $_POST['shortcode'];
            $module_skins = $this->get_skins();

            if(empty( $module_skins )){
                $output = __("No item exists for this module" , "site-editor");
                $js_tpl = array();
            }else{
                $output = $this->modules_skins_tmpl( $module_skins );
                $js_tpl = $this->js_tpl_skin;
            }

            die( wp_json_encode( array(
              'success' => true,
              'data'    => array(
                    'output'             => $output ,
                    'js_tpl'             => $js_tpl ,
                    'data_module_skins'  => array( $this->module => $module_skins )
              ),
            ) ) );

        }else{
            die( wp_json_encode( array(
              'success' => false,
              'data'    => array(
                    'output' => __("invalid send data" , "site-editor") ,
              ),
            ) ) );
        }
    }

    public function modules_skins_tmpl( $skins ){

        $output = '<div class="pb-skins-modules"><ul id="pb-skins-modules-container-'.$this->module.'" class="pb-skins-modules-container">';

        if(!empty($skins)){
            foreach( $skins As $skin => $data ){

                $screenshot = !empty( $data["screenshot"] ) ? $data["screenshot"]: $this->skin_def_screenshot;

                $output .= '<li><a class="pb-skin-item" data-skin-name="'.$skin.'" title="'.$skin.'"  href="#"><img class="skin-item-screenshot" src="'.$screenshot.'" alt="'.$skin.'" /></a></li>';
            }
        }

        $output .= '</ul></div>';

        return $output;
    }


    /*function get_skin_info( $skin , $module , $type ){
        global $sed_apps;
        return $sed_apps->module_info[$module]['skins'][$skin][$type];
    }*/

    function load_parent_skin($skin , $parent_module , $ext = ''){

        $php_view = "";

        $path = $this->modules_path . DS . $parent_module . DS .'skins'. DS . $skin . DS;

        $file_name = (empty($ext)) ? $this->shortcode . '.php' : $this->shortcode . '-' . $ext . '.php';

        $php_view =  $path . $file_name;
                          
        return $php_view;

    }

    function load_skin($skin , $parent_module = '' , $ext = '' , $parent_skin = '' ){
        global $sed_apps;

        $php_view = "";
        $module_name = $this->module;
        if( !empty( $parent_module ) ){

            $skin = !empty( $parent_skin ) ? $parent_skin : $skin;
            $php_view = $this->load_parent_skin( $skin , $parent_module , $ext );

            if( !file_exists($php_view) ){
                $php_view = "";
            }else
                $module_name = $parent_module;

        }

        if(empty($php_view)){

            $path = $this->modules_path . DS . $this->module . DS .'skins'. DS . $skin . DS;

            $file_name = (empty($ext)) ? $this->shortcode . '.php' : $this->shortcode . '-' . $ext . '.php';

            $php_view = $path . $file_name;



            $module_skin = array( "module" => $this->module  , "skin" => $skin  );

            if( !in_array( $module_skin , $this->loaded_skins  ) ){

                /*$scripts = $this->get_skin_info( $skin , $this->module , "scripts" );

                if( !is_null( $scripts ) && !empty( $scripts ) ){
                    foreach( $scripts AS $handle => $script ){
                        wp_enqueue_script( $script['handle'] , content_url( "/" . $script['src'] ) , $script['deps'] , $script['ver'] , $script['in_footer'] );
                    }
                }

                $styles = $this->get_skin_info( $skin , $this->module , "styles" );

                if( !is_null( $styles ) && !empty( $styles ) ){
                    foreach( $styles AS $handle => $style ){
                        wp_enqueue_style( $style['handle'] , content_url( "/" . $style['src'] ) , $style['deps'] , $style['ver'] , $style['media'] );
                    }
                }

                $lesses = $this->get_skin_info( $skin , $this->module , "less" );

                if( !is_null( $lesses ) && !empty( $lesses ) ){
                    foreach( $lesses AS $handle => $style ){
                        wp_enqueue_style( $style['handle'] , SED_UPLOAD_URL. "/modules" . $style['src_rel'] , $style['deps'] , $style['ver'] , $style['media'] );
                    }
                }*/

                $this->loaded_skins[] = $module_skin;

            }

        }
        

        $content = $this->get_content( $php_view , $this->skin_vars );

        return $content;
    }


    function get_content( $file , $vars = array() ){

        $content ="";

        //$all_files = array_keys( $this->files_loaded );

        //if( !in_array( $file , $all_files ) ){
            if(!empty($vars))
                extract( $vars );

            if( file_exists($file) ) {
                ob_start();
                    include $file;
                $content = ob_get_clean();
            }

            //$this->files_loaded[$file] = $content;
        //}else
            //$content = $this->files_loaded[$file];

        return $content;
    }

    function get_skins(){
        $activate_modules = SiteEditorModules::pb_module_active_list();

        if( isset( $activate_modules[ $this->module ] ) ){
            $this->modules_path = WP_CONTENT_DIR . "/" . dirname( dirname( $activate_modules[ $this->module ] ) );
            $this->modules_base_url = content_url( "/" . dirname( dirname( $activate_modules[ $this->module ] ) ) );
        }else{
            return array();
        }

        $path = $this->modules_path . DS . $this->module . DS .'skins' . DS;
        $skins = array();
        $skins_folders = glob($path.'*');

        if(!empty( $skins_folders )){
            foreach ($skins_folders as $folder) {

                if( !is_dir( $folder ) )
                    continue;

                $folder_name = basename($folder);
                $skins[$folder_name] = array("js" => array() , "css" => array() , "screenshot" => "" , "pattern" => array() );

                $path_skin = $this->modules_path . DS . $this->module . DS .'skins'. DS . $folder_name . DS;

            	foreach (glob($path_skin.'*') as $file) {

                    if( is_file($file) ){
                        $file_name = basename($file);
                        $file_url = $this->modules_base_url . "/" .$this->module."/skins/".$folder_name."/".$file_name;

                        if( preg_match("/^(screenshot)\.(jpg|png|gif)/" , $file_name) || substr($file_name, -5) == '.jpeg' ){
                            $skins[$folder_name]["screenshot"] = $file_url;
                            continue;
                        }

                        if( $file_name == "shortcode.pattern" ){
                            $content = $this->get_content( $file );

                            $content = str_replace("{{@sed_module_url}}", $this->modules_base_url . "/$this->module", $content );
                            $content = str_replace("{{@sed_skin_url}}", $this->modules_base_url . "/$this->module/skins/$folder_name", $content );

                            $shortcodes_model = PageBuilderApplication::get_pattern_shortcodes( $content , "root" , $this->module , $this->shortcode );
                            $skins[$folder_name]["pattern"] = $shortcodes_model['shortcodes'];
                            continue;
                        }

                    }else if(  is_dir($file)  ){

                        if( substr($file, -4) == '/css' || substr($file, -4) == '\css' ){

                            $css_folder = $this->get_css_folder( $folder_name );

                            foreach (glob($css_folder.'*') as $css_file) {
                                $css_file_name = basename($css_file);
                                $file_url = $this->modules_base_url . "/" .$this->module."/skins/".$folder_name."/css/".$css_file_name;

                                $css_info = $this->get_file_data($css_file , "css_info");

                                if($css_info === false || !isset($css_info['handle']) || empty($css_info['handle']) )
                                    continue;


                                $css_info['src'] = $file_url;

                                $deps = (isset($css_info['deps']) && !empty( $css_info['deps'] )) ? $css_info['deps'] : array();
                                $ver = (isset($css_info['ver']) && !empty( $css_info['ver'] )) ? $css_info['ver'] : '1.0.0';
                                $media = (isset($css_info['media']) && !empty( $css_info['media'] )) ? $css_info['media'] : "all";

                                $style = array($css_info['handle'] , $css_info['src'] , $deps , $ver , $media);

                                $skins[$folder_name]["css"][] = $style;
                            }

                            continue;
                        }

                        if(substr($file, -3) == '/js' || substr($file, -3) == '\js'){

                            $js_folder = $this->get_js_folder( $folder_name );

                            foreach (glob($js_folder.'*') as $js_file) {
                                $js_file_name = basename($js_file);
                                $file_url = $this->modules_base_url."/".$this->module."/skins/".$folder_name."/js/".$js_file_name;

                                $js_info = $this->get_file_data($js_file , "js_info");
                                $js_info['src'] = $file_url;

                                if($js_info !== false && isset($js_info['handle']) && !empty($js_info['handle']) && !empty($js_info['src'])){

                                    $deps = (isset($js_info['deps']) && !empty( $js_info['deps'] )) ? $js_info['deps'] : array();
                                    $ver = (isset($js_info['ver']) && !empty( $js_info['ver'] )) ? $js_info['ver'] : '1.0.0';
                                    $in_footer = (isset($js_info['in_footer']) && !empty( $js_info['in_footer'] )) ? $js_info['in_footer'] : false;

                                    $script = array($js_info['handle'] , $js_info['src'] , $deps , $ver , $in_footer);
                                    $skins[$folder_name]["js"][] = $script;
                                }


                            }

                            continue;
                        }


                        if(substr($file, -4) == '/tpl' || substr($file, -4) == '\tpl' ){

                            $tpl_folder = $this->get_tpl_folder( $folder_name );

                            foreach (glob($tpl_folder.'*') as $tpl_file) {
                                $content = $this->get_content( $tpl_file );

                                $id = "sed-tpl-" . $folder_name . "-" . substr( basename($tpl_file) , 0 , -4 ) . "-" . $this->module;

                                $this->js_tpl_skin[ $id ] = $content ;
                            }

                        }

                    }
                }
            }
        }


        return $skins;
    }


    function get_css_folder( $skin , $module = '' ){
        $module = (!empty($module)) ? $module : $this->module ;
        return $this->modules_path . DS . $module . DS .'skins'. DS . $skin . DS . 'css' . DS;
    }

    function get_less_file( $skin , $module = '' ){
        $module = (!empty($module)) ? $module : $this->module ;
        return $this->modules_path . DS . $module . DS .'skins'. DS . $skin . DS . 'less' . DS ;
    }

    function get_js_folder( $skin , $module = '' ){
        $module = (!empty($module)) ? $module : $this->module ;
        return $this->modules_path . DS . $module . DS .'skins'. DS . $skin . DS . 'js' . DS;
    }

    function get_tpl_folder( $skin , $module = '' ){
        $module = (!empty($module)) ? $module : $this->module ;
        return $this->modules_path . DS . $module . DS .'skins'. DS . $skin . DS . 'tpl' . DS;
    }

    function get_file_data( $file, $type = 'js_info' ) {
        // We don't need to write to the file, so just open for reading.
        $fp = fopen( $file, 'r' );

        // Pull only the first 8kiB of the file in.
        $file_data = fread( $fp, 8192 );

        // PHP will close file handle, but we are good citizens.
        fclose( $fp );

        // Make sure we catch CR-only line endings.
        $file_data = str_replace( "\r", "\n", $file_data );

        if ( preg_match( '/^\/\*\s*\#'. $type .'\#(\([^\}]+\))\#\s*\*\//' , $file_data , $matches) && $matches[1] ){
                eval("\$info = array" . $matches[1].";");
                return $info;
        }else{
                return false;
        }

	}

}
