<?php
Class SiteEditorModules {

    var $app_modules_dir;
    var $app_name;
    var $modules;

    function __construct( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'app_name' => ''
        ) );

        add_filter("modules_activate_pagebuilder", array( $this , "get_active_module_pagebuilder" ) , 10 , 1 );

        extract( $args );

        if(!empty($app_name))
            $this->app_name = $app_name;
    }

    function modules_activate(){
        $modules = array();
        if($this->app_name != "pagebuilder"){
            $modules_data = $this->get_modules();

            foreach( $modules_data AS $key=>$value ){
                $modules[$key] = $this->app_modules_dir . DS . $key;
                //var_dump( $key );
            }
        }

        $modules = apply_filters("modules_activate_".$this->app_name, $modules);

        $this->modules = $modules;

        return $modules;
    }

    function get_active_module_pagebuilder( $old_module = array() ){
          $modules = (array) sed_get_setting("live_module")  ;
          $live_modules = array();

          foreach ( $modules as $slug => $rel_path ) {
              $live_modules[$rel_path] = SED_PB_MODULES_PATH . DS . $rel_path;
          }
          return $live_modules;
    }

    function get_modules(){

        $application = $this->app_name; //var_dump( $application );
        $site_editor_modules = array();

    	if ( ! $cache_modules = wp_cache_get( 'modules', 'modules') )
    		$cache_modules = array();

    	if ( isset($cache_modules[ $application ]) )
    		return $cache_modules[ $application ];

        $module_root = $this->app_modules_dir;


    	$modules_dir = @ opendir( $module_root);
    	$module_files = array();
    	if ( $modules_dir ) {
    		while (($file = readdir( $modules_dir ) ) !== false ) {
    			if ( substr($file, 0, 1) == '.' )
    				continue;
    			if ( is_dir( $module_root.'/'.$file ) ) {
    				$modules_subdir = @ opendir( $module_root.'/'.$file );
    				if ( $modules_subdir ) {
    					while (($subfile = readdir( $modules_subdir ) ) !== false ) {
    						if ( substr($subfile, 0, 1) == '.' )
    							continue;
    						if ( substr($subfile, -4) == '.php' )
    							$module_files[] = "$file/$subfile";
    					}
    					closedir( $modules_subdir );
    				}
    			} else {
    				if ( substr($file, -4) == '.php' )
    					$module_files[] = $file;
    			}
    		}
    		closedir( $modules_dir );
    	}

    	if ( empty($module_files) )
    		return $site_editor_modules;

    	foreach ( $module_files as $module_file ) {
    		if ( !is_readable( "$module_root/$module_file" ) )
    			continue;

    		$module_data = $this->get_module_data( $module_root.DS.$module_file, false, false ); //Do not apply markup/translate as it'll be cached.

    		if ( empty ( $module_data['Name'] ) )
    			continue;

    		$site_editor_modules[$this->module_basename( $module_file )] = $module_data;
    	}

    	//uasort( $site_editor_modules, '_sort_uname_callback' );

    	$cache_modules[ $application ] = $site_editor_modules;
    	wp_cache_set('modules', $cache_modules, 'modules');

    	return $site_editor_modules;

    }

    /**
     * Gets the basename of a module.
     *
     * This method extracts the name of a module from its filename.
     *
     * @access private
     *
     * @param string $file The filename of module.
     * @return string The name of a module.
     * @uses app_modules_dir
     */
    function module_basename($file) {
    	$file = str_replace('\\','/',$file);     // sanitize for Win32 installs
    	$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
    	$module_dir = str_replace('\\','/',$this->app_modules_dir); // sanitize for Win32 installs
    	$module_dir = preg_replace('|/+|','/', $module_dir); // remove any duplicate slash
    	$file = preg_replace('#^' . preg_quote($module_dir, '#') . '/#','',$file); // get relative path from modules dir
    	$file = trim($file, '/');
    	return $file;
    }

    function get_module_data( $module_file, $markup = true, $translate = true ){

    	$default_headers = array(
    		'Name'          => 'Module Name',
    		'ModuleURI'     => 'Module URI',
    		//'Version'       => 'Version',
    		'Description'   => 'Description',
    		'Author'        => 'Author',
    		'AuthorURI'     => 'Author URI',
    		'TextDomain'    => 'Text Domain',
    		'DomainPath'    => 'Domain Path' ,
            //'Network'       => 'Network',
    	);

    	$module_data = get_file_data( $module_file, $default_headers, 'modules' );

        //$module_data['Network'] = ( 'true' == strtolower( $module_data['Network'] ) );

    	if ( $markup || $translate ) {
    		$module_data = $this->get_module_data_markup_translate( $module_file, $module_data, $markup, $translate );
    	} else {
    		$module_data['Title']      = $module_data['Name'];
    		$module_data['AuthorName'] = $module_data['Author'];
    	}

    	return $module_data;
    }

    /**
     * Sanitizes module data, optionally adds markup, optionally translates.
     *
     * @since 2.7.0
     * @access private
     * @see get_module_data()
     */
    function get_module_data_markup_translate( $module_file, $module_data, $markup = true, $translate = true ) {

    	// Sanitize the module filename to a WP_PLUGIN_DIR relative path
    	$module_file = $this->module_basename( $module_file );

    	// Translate fields
    	if ( $translate ) {

     		/*if ( $textdomain = $module_data['TextDomain'] ) {
    			if ( $module_data['DomainPath'] )
    				$this->load_module_textdomain( $textdomain, false, dirname( $module_file ) . $module_data['DomainPath'] );
    			else
    				$this->load_module_textdomain( $textdomain, false, dirname( $module_file ) );
    		}*/

    		//if ( $textdomain ) {
                $textdomain = "site-editor";
    			foreach ( array( 'Name', 'ModuleURI', 'Description', 'Author', 'AuthorURI' ) as $field )
    				$module_data[ $field ] = translate( $module_data[ $field ], $textdomain );
    		//}

    	}

    	// Sanitize fields
    	$allowed_tags = $allowed_tags_in_links = array(
    		'abbr'    => array( 'title' => true ),
    		'acronym' => array( 'title' => true ),
    		'code'    => true,
    		'em'      => true,
    		'strong'  => true,
    	);
    	$allowed_tags['a'] = array( 'href' => true, 'title' => true );

    	// Name is marked up inside <a> tags. Don't allow these.
    	// Author is too, but some modules have used <a> here (omitting Author URI).
    	$module_data['Name']        = wp_kses( $module_data['Name'],        $allowed_tags_in_links );
    	$module_data['Author']      = wp_kses( $module_data['Author'],      $allowed_tags );

    	$module_data['Description'] = wp_kses( $module_data['Description'], $allowed_tags );
    	//$module_data['Version']     = wp_kses( $module_data['Version'],     $allowed_tags );

    	$module_data['ModuleURI']   = esc_url( $module_data['ModuleURI'] );
    	$module_data['AuthorURI']   = esc_url( $module_data['AuthorURI'] );

    	$module_data['Title']      = $module_data['Name'];
    	$module_data['AuthorName'] = $module_data['Author'];

    	// Apply markup
    	if ( $markup ) {
    		if ( $module_data['ModuleURI'] && $module_data['Name'] )
    			$module_data['Title'] = '<a href="' . $module_data['ModuleURI'] . '" title="' . esc_attr__( 'Visit module homepage' ) . '">' . $module_data['Name'] . '</a>';

    		if ( $module_data['AuthorURI'] && $module_data['Author'] )
    			$module_data['Author'] = '<a href="' . $module_data['AuthorURI'] . '" title="' . esc_attr__( 'Visit author homepage' ) . '">' . $module_data['Author'] . '</a>';

    		$module_data['Description'] = wptexturize( $module_data['Description'] );

    		if ( $module_data['Author'] )
    			$module_data['Description'] .= ' <cite>' . sprintf( __('By %s.'), $module_data['Author'] ) . '</cite>';
    	}

    	return $module_data;
    }


    public static function pb_module_active_list(){

    	if ( ! $cache_modules = wp_cache_get('modules', 'pb_module_active_list') )
    		$cache_modules = array();
        else
            return $cache_modules;

        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;

        if( empty( $activate_modules ) )
            return array();

        $activate_modules =  $activate_modules;

        $cache_modules = $activate_modules;
        wp_cache_set('modules', $cache_modules, 'pb_module_active_list');

        return $activate_modules;

    }

    public static function is_pb_module_active( $module ){

        $activate_modules = self::pb_module_active_list();

        //$activate_modules = apply_filters( "is_pb_activate_modules" , $activate_modules );

        if( in_array( $module , array_keys( $activate_modules ) ) )
            return true ;
        else
            return false;

    }

    public static function print_message( $msg , $type = "success"){
        sed_print_message( $msg , $type );

    }

}


function is_pb_module_active( $module ){
    return SiteEditorModules::is_pb_module_active( $module ) ;
}

global $sed_notics;
$sed_notics = array();

function sed_admin_notice( $notic ){
    global $sed_notics;
    $sed_notics[] = $notic;
}

function sed_pb_module_notice() {
    global $sed_notics;
    foreach( $sed_notics AS $notic ){
        ?>
        <div class="error settings-error site-editor-notice">
            <p><?php echo $notic; ?></p>
        </div>
        <?php
    }                
}

add_action( 'admin_notices', 'sed_pb_module_notice' , 1000 );

