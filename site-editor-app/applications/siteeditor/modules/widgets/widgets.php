<?php
/*
Module Name: Widgets
Module URI: http://www.siteeditor.org/modules/widgets
Description: Module Widgets For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org/products/widgets
Version: 1.0.0
*/


class SiteEditorWidgets{

	/**
	 * All id_bases for widgets defined in core.
	 *
	 * @since 3.9.0
	 * @access protected
	 * @var array
	 */
	protected $core_widget_id_bases = array(
		'archives', 'calendar', 'categories', 'links', 'meta',
		'nav_menu', 'pages', 'recent-comments', 'recent-posts',
		'rss', 'search', 'tag_cloud', 'text',
	);


	public function __construct( ) {

        $this->template = 'default';
        $this->current_app = 'siteeditor';//site_editor_app_on

        if( site_editor_app_on() ){
            add_action( 'wp_footer', array( $this, 'output_widget_templates' ) );

            add_action( 'wp_footer', array( $this, 'widget_tpl_live' ) );
        }

        add_action( 'wp_footer', array( $this, 'print_widgets_settings' ) );

        //for siteeditor
        add_action( 'print_sed_widget_settings_tmpl', array( $this, 'output_widget_box' ) );

        add_action( 'sed_footer', array( $this, 'output_widget_templates' ) );

        add_action( 'widgets_init', array( $this , 'register_widgets' ) , 99999 );

        add_action('site_editor_ajax_widget_load', array($this, 'widget_load') );

        add_filter( "sed_js_I18n", array($this,'js_I18n'));

        add_filter( "sed_addon_settings", array($this,'widget_general_settings'));

        add_action("after_inner_tab_content_widgets" , array($this, "other_widgets_panel")  );

	}

    function other_widgets_panel(){
        require SED_BASE_SED_APP_PATH."/modules/widgets/view/other_widgets_panel.php";
    }

    function print_widgets_settings(){

        $widgets_settings = array();

        //var_dump($this->get_available_widgets());

        foreach ( $this->get_available_widgets() as $available_widget ){
            $php_class = $available_widget['php_class'];

            $custom_settings = apply_filters( "sed_widget_settings_" . $available_widget['id_base'] , array() );

            $widgets_settings[$available_widget['id_base']] = array_merge(
                array(
                    'php_class'    => $php_class ,
                    'transport'    => 'ajax_refresh',
                    'scripts'      => array() ,
                    'styles'       => array() ,
                ) ,
                $custom_settings
            );

        }

        $widgets_settings = apply_filters( "sed_widget_settings" , $widgets_settings );

		?>

		<script type="text/javascript">
		        var _sedAppEditorWidgetsSettings = <?php echo wp_json_encode( $widgets_settings ); ?>;
		</script>
		<?php
    }

    function widget_tpl_live( $sed_addon_settings ){

        $widget_tpls = array();

        foreach ( $this->get_available_widgets() as $available_widget ){
            $php_class = $available_widget['php_class'];

            if( method_exists( $php_class , 'tpl' ) ){
                $widget = new $php_class();

                ob_start();

                    $widget->tpl();

                $output = ob_get_contents();
                ob_end_clean();

                $widget_tpls[$available_widget['id_base']] = $output;
            }

        }
		?>

		<script type="text/javascript">
		        var _sedAppEditorWidgetTpls = <?php echo wp_json_encode( $widget_tpls ); ?>;
		</script>
		<?php
    }


    function widget_general_settings( $sed_addon_settings ){
        global $site_editor_app;
        $sed_addon_settings['widgets'] = array(
            'nonce'  => wp_create_nonce( 'sed_app_widget_load_' . $site_editor_app->get_stylesheet() )
        );
        return $sed_addon_settings;
    }

    function js_I18n( $I18n ){
        $I18n['empty_widget']   =  __('This is an empty widget.' , "site-editor");
        return $I18n;
    }

    function widget_load(){
        global $sed_apps;
        $sed_apps->check_ajax_handler('sed_widget_loader' , 'sed_app_widget_load');

        if( !is_array( $_REQUEST['args'] ) || empty( $_REQUEST['args'] ) )
            $_REQUEST['args'] = array();

        $_REQUEST['args'] = array_merge( array(
    		'before_widget' => sprintf( '<aside class="widget %1$s">' , $_REQUEST['class_name'] ),    //id="%1$s", $_REQUEST['id_base']
    		'after_widget'  => '</aside>',
    		'before_title'  => '<h2 class="widget-title">',
    		'after_title'   => '</h2>',
        ) , $_REQUEST['args'] );

        extract( $_REQUEST );

        $instance = urldecode($instance);

        preg_match_all("/(widget-". $id_base .")\[__\i__\]\[([^\[\]]+)\]/", $instance, $matches);

        for ($i=0; $i < count($matches[0]); $i++){
            $instance = str_replace( $matches[0][$i] , $matches[2][$i] , $instance);
        }


        if(isset( $widget) && !empty($widget) && class_exists($widget) ){

            ob_start();

                the_widget( $widget , $instance , $args );

            $output = ob_get_contents();
            ob_end_clean();

            $this->sed_die( true , $output );
        }else{
            $this->sed_die( false , __("Widget data is inccorect" , "site-editor") );
        }

    }

    public function sed_die($success = true , $output = '' , $scripts = array() , $styles = array() ){
        die( wp_json_encode( array(
          'success' => $success,
          'data'    => array(
                'output'   => $output ,
                'scripts'  => $scripts ,
                'styles'   => $styles
          )
        ) ) );
    }

    public function register_widgets( ) {
        global $site_editor_app;
        $controls = array();

        foreach ( $this->get_available_widgets() as $widget ){
             //var_dump( $widget );

            $title = esc_html( strip_tags( $widget['name'] ) );
            $id = (!isset($widget['id_base']) || empty($widget['id_base']) ) ? $widget['id'] : $widget['id_base'];
            $element_html = $this->toolbar_widget( $widget );
            $group = ( isset($group) && !empty($group) ) ? $group: "basic";

            $group = apply_filters( "widget_toolbar_group" , $group , $widget['id_base'] );

            $group = apply_filters( "widget_toolbar_group_" . $widget['id_base'] , $group );

            $site_editor_app->toolbar->add_element(
                "widgets" ,
                $group ,
                $id ,
                $title ,
                $element_html ,     //$def_content
                "" ,                //icon
                "" ,  //$capability=
                array(),
                array( "row" => 1 ,"rowspan" => 2 ) ,
                array() ,
                "all" //mixed string(eg all) or array( "pages" , "blog" , "woocommerce" , "search" , "single_post" , "archive" )
            );

            $controls["widget-".$id] = array(
                'settings'     => array(
                    'default'       => 'sed_pb_modules'
                ),
                'type'                => 'widget',
                'category'            => 'module-settings',
                'sub_category'        =>  $id,           //shortcode name :: sed_image
                'default_value'       =>  '',
                'attr_name'           =>  'instance',
                'shortcode'           =>  'sed_widget'
            );

        }

        sed_add_controls( $controls );

    }

    private function toolbar_widget( $widget ){

        $title = esc_html( strip_tags( $widget['name'] ) );
        $id = (!isset($widget['id_base']) || empty($widget['id_base']) ) ? $widget['id'] : $widget['id_base'];
        $icon = $widget['classname'];
        $type_icon = "font";

        if($type_icon == "font")
            $icon_class = $icon;
        elseif($type_icon == "img")
            $icon_img = $icon;

        ob_start();

        if(file_exists(SED_TMPL_PATH . DS . $this->template . DS . "modules/widgets/view/widget_element.php" )){
            require SED_TMPL_PATH . DS . $this->template . DS . "modules/widgets/view/widget_element.php" ;
        }elseif(file_exists(SED_APPS_PATH . DS . $this->current_app . DS . "modules/widgets/view/widget_element.php" )){
            require SED_APPS_PATH . DS . $this->current_app . DS .  "modules/widgets/view/widget_element.php" ;
        }

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

	/**
	 * Render the widget form control templates into the DOM.
	 *
	 * @since 3.9.0
	 * @access public
	 */
	public function output_widget_box() {
		?>

        <div id="dialog-page-box-widgets-settings"  data-title="<?php echo __("Widgets Settings") ;?>" data-multi-level-box="true">
    		<div id="widgets-left"><!-- compatibility with JS which looks for widget templates here -->
    		<div id="available-widgets" class="widgets-box-container"> <!-- widgets-box-container using in siteeditor/plugins/settings/plugin.min.js-->
    			<!--<div id="available-widgets-filter">
    				<label class="screen-reader-text" for="widgets-search"><?php _e( 'Search Widgets' ); ?></label>
    				<input type="search" id="widgets-search" placeholder="<?php esc_attr_e( 'Search widgets&hellip;' ) ?>" />
    			</div>-->

    		</div><!-- #available-widgets -->
    		</div><!-- #widgets-left -->
        </div>

		<?php
	}


	public function output_widget_templates() {

        foreach ( $this->get_available_widgets() as $available_widget ):
          $widget_title = esc_html( strip_tags( $available_widget['name'] ) )
        ?>
          <script type="text/html" id="widget-tpl-<?php echo esc_attr( $available_widget['id_base'] ) ?>" data-widget-title="<?php echo $widget_title ?>" data-widget-id="<?php echo esc_attr( $available_widget['id'] ) ?>" >
            <div id="sed-app-control-widget-<?php echo esc_attr( $available_widget['id_base'] ) ?>" class="widget-tpl row_settings <?php echo esc_attr( $available_widget['id'] ) ?>" tabindex="0">
                <div class="row_setting_box">
                    <?php                      // <script\s*([^\>]*)\s*\>  "/<script((?:(?!src=).)*?)>(.*?)<\/script>/smix"
                    $template = str_replace( "<script" , "{{&lt;script" , $available_widget['control_tpl'] );
                    $template = str_replace( "</script>" , "{{&lt;script&gt;}}" , $template );
                    echo $template;
                    ?>
                </div>
            </div>
          </script>
        <?php endforeach;

	}

	/**
	 * Build up an index of all available widgets for use in Backbone models.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @see wp_list_widgets()
	 *
	 * @return array List of available widgets.
	 */
	public function get_available_widgets() {
		static $available_widgets = array();
		if ( ! empty( $available_widgets ) ) {
			return $available_widgets;
		}

		global $wp_registered_widgets, $wp_registered_widget_controls;
        require_once ABSPATH . '/wp-admin/includes/template.php';
		require_once ABSPATH . '/wp-admin/includes/widgets.php'; // for next_widget_id_number()
                                   
		$sort = $wp_registered_widgets;
		usort( $sort, array( $this, '_sort_name_callback' ) );
		$done = array();

		foreach ( $sort as $widget ) {
			if ( in_array( $widget['callback'], $done, true ) ) { // We already showed this multi-widget
				continue;
			}

			$sidebar = is_active_widget( $widget['callback'], $widget['id'], false, false );
			$done[]  = $widget['callback'];

			if ( ! isset( $widget['params'][0] ) ) {
				$widget['params'][0] = array();
			}

            $php_class = get_class($widget['callback'][0]);

			$available_widget = $widget;
			unset( $available_widget['callback'] ); // not serializable to JSON

			$args = array(
				'widget_id'   => $widget['id'],
				'widget_name' => $widget['name'],
				'_display'    => 'template',
			);

			$is_disabled     = false;
			$is_multi_widget = ( isset( $wp_registered_widget_controls[$widget['id']]['id_base'] ) && isset( $widget['params'][0]['number'] ) );
			if ( $is_multi_widget ) {
				$id_base            = $wp_registered_widget_controls[$widget['id']]['id_base'];
				$args['_temp_id']   = "$id_base-__i__";
				$args['_multi_num'] = next_widget_id_number( $id_base );
				$args['_add']       = 'multi';
			} else {
				$args['_add'] = 'single';

				if ( $sidebar && 'wp_inactive_widgets' !== $sidebar ) {
					$is_disabled = true;
				}
				$id_base = $widget['id'];
			}

			$list_widget_controls_args = wp_list_widget_controls_dynamic_sidebar( array( 0 => $args, 1 => $widget['params'][0] ) );
			$control_tpl = $this->get_widget_control( $list_widget_controls_args );

			// The properties here are mapped to the Backbone Widget model.
			$available_widget = array_merge( $available_widget, array(
				'temp_id'      => isset( $args['_temp_id'] ) ? $args['_temp_id'] : null,
				'is_multi'     => $is_multi_widget,
				'control_tpl'  => $control_tpl,
				'multi_number' => ( $args['_add'] === 'multi' ) ? $args['_multi_num'] : false,
				'is_disabled'  => $is_disabled,
				'id_base'      => $id_base,
				'transport'    => 'refresh',
				'width'        => $wp_registered_widget_controls[$widget['id']]['width'],
				'height'       => $wp_registered_widget_controls[$widget['id']]['height'],
				'is_wide'      => $this->is_wide_widget( $widget['id'] ),
                'php_class'    => $php_class
			) );

			$available_widgets[] = $available_widget;
		}

		return $available_widgets;
	}

	/**
	 * Get the widget control markup.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param array $args Widget control arguments.
	 * @return string Widget control form HTML markup.
	 */
	public function get_widget_control( $args ) {
	  //var_dump( $args );
		ob_start();

		//$this->wp_widget_control( $args );
        call_user_func_array( array( $this, 'wp_widget_control' ) , $args );

		$control_tpl = ob_get_clean();

		return $control_tpl;
	}

	/**
	 * Naturally order available widgets by name.
	 *
	 * @since 3.9.0
	 * @static
	 * @access protected
	 *
	 * @param array $widget_a The first widget to compare.
	 * @param array $widget_b The second widget to compare.
	 * @return int Reorder position for the current widget comparison.
	 */
	protected function _sort_name_callback( $widget_a, $widget_b ) {
		return strnatcasecmp( $widget_a['name'], $widget_b['name'] );
	}

	/**
	 * Determine whether the widget is considered "wide".
	 *
	 * Core widgets which may have controls wider than 250, but can
	 * still be shown in the narrow customizer panel. The RSS and Text
	 * widgets in Core, for example, have widths of 400 and yet they
	 * still render fine in the customizer panel. This method will
	 * return all Core widgets as being not wide, but this can be
	 * overridden with the is_wide_widget_in_customizer filter.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $widget_id Widget ID.
	 * @return bool Whether or not the widget is a "wide" widget.
	 */
	public function is_wide_widget( $widget_id ) {
		global $wp_registered_widget_controls;

		$parsed_widget_id = $this->parse_widget_id( $widget_id );
		$width            = $wp_registered_widget_controls[$widget_id]['width'];
		$is_core          = in_array( $parsed_widget_id['id_base'], $this->core_widget_id_bases );
		$is_wide          = ( $width > 250 && ! $is_core );

		/**
		 * Filter whether the given widget is considered "wide".
		 *
		 * @since 3.9.0
		 *
		 * @param bool   $is_wide   Whether the widget is wide, Default false.
		 * @param string $widget_id Widget ID.
		 */
		return apply_filters( 'is_wide_widget_in_customizer', $is_wide, $widget_id );
	}

	/**
	 * Covert a widget ID into its id_base and number components.
	 *
	 * @since 3.9.0
	 * @access public
	 *
	 * @param string $widget_id Widget ID.
	 * @return array Array containing a widget's id_base and number components.
	 */
	public function parse_widget_id( $widget_id ) {
		$parsed = array(
			'number' => null,
			'id_base' => null,
		);

		if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $matches ) ) {
			$parsed['id_base'] = $matches[1];
			$parsed['number']  = intval( $matches[2] );
		} else {
			// likely an old single widget
			$parsed['id_base'] = $widget_id;
		}
		return $parsed;
	}

    /**
     * Meta widget used to display the control form for a widget.
     *
     * Called from dynamic_sidebar().
     *
     * @since 2.5.0
     *
     * @param array $sidebar_args
     * @return array
     */
    function wp_widget_control( $sidebar_args ) {
    	global $wp_registered_widgets, $wp_registered_widget_controls, $sidebars_widgets;

    	$widget_id = $sidebar_args['widget_id'];
    	$sidebar_id = isset($sidebar_args['id']) ? $sidebar_args['id'] : false;
    	$key = $sidebar_id ? array_search( $widget_id, $sidebars_widgets[$sidebar_id] ) : '-1'; // position of widget in sidebar
    	$control = isset($wp_registered_widget_controls[$widget_id]) ? $wp_registered_widget_controls[$widget_id] : array();
    	$widget = $wp_registered_widgets[$widget_id];

    	$id_format = $widget['id'];
    	$widget_number = isset($control['params'][0]['number']) ? $control['params'][0]['number'] : '';
    	$id_base = isset($control['id_base']) ? $control['id_base'] : $widget_id;
    	$multi_number = isset($sidebar_args['_multi_num']) ? $sidebar_args['_multi_num'] : '';
    	$add_new = isset($sidebar_args['_add']) ? $sidebar_args['_add'] : '';

    	$query_arg = array( 'editwidget' => $widget['id'] );
    	if ( $add_new ) {
    		$query_arg['addnew'] = 1;
    		if ( $multi_number ) {
    			$query_arg['num'] = $multi_number;
    			$query_arg['base'] = $id_base;
    		}
    	} else {
    		$query_arg['sidebar'] = $sidebar_id;
    		$query_arg['key'] = $key;
    	}

    	/*
    	 * We aren't showing a widget control, we're outputting a template
    	 * for a multi-widget control.
    	 */
    	if ( isset($sidebar_args['_display']) && 'template' == $sidebar_args['_display'] && $widget_number ) {
    		// number == -1 implies a template where id numbers are replaced by a generic '__i__'
    		$control['params'][0]['number'] = -1;
    		// With id_base widget id's are constructed like {$id_base}-{$id_number}.
    		if ( isset($control['id_base']) )
    			$id_format = $control['id_base'] . '-__i__';
    	}

    	$wp_registered_widgets[$widget_id]['callback'] = $wp_registered_widgets[$widget_id]['_callback'];
    	unset($wp_registered_widgets[$widget_id]['_callback']);

    	$widget_title = esc_html( strip_tags( $sidebar_args['widget_name'] ) );
    	$has_form = 'noform';

    	echo $sidebar_args['before_widget']; ?>

    	<div class="widget-description">
    <?php echo ( $widget_description = wp_widget_description($widget_id) ) ? "$widget_description\n" : "$widget_title\n"; ?>
    	</div>

    	 <div class="widget-inside">
        	<form class="widget-form" >
            	<div class="widget-content">
            <?php
                $php_class = "";
            	if ( isset($control['callback']) ){
                    $php_class = get_class($control['callback'][0]);
            		$has_form = call_user_func_array( $control['callback'], $control['params'] );
            	}else
            		echo "\t\t<p>" . __('There are no options for this widget.') . "</p>\n"; ?>
            	</div>
            </form>
        </div>
        <form class="widget-option-form" >
        	<input type="hidden" name="widget-id" class="widget-id" value="<?php echo esc_attr($id_format); ?>" />
        	<input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr($id_base); ?>" />
            <input type="hidden" name="widget-classname" class="widget-classname" value="<?php echo esc_attr($widget['classname']); ?>" />
        	<input type="hidden" name="widget-width" class="widget-width" value="<?php if (isset( $control['width'] )) echo esc_attr($control['width']); ?>" />
        	<input type="hidden" name="widget-height" class="widget-height" value="<?php if (isset( $control['height'] )) echo esc_attr($control['height']); ?>" />
        	<input type="hidden" name="widget_number" class="widget_number" value="<?php echo esc_attr($widget_number); ?>" />
        	<input type="hidden" name="multi_number" class="multi_number" value="<?php echo esc_attr($multi_number); ?>" />
        	<input type="hidden" name="add_new" class="add_new" value="<?php echo esc_attr($add_new); ?>" />
            <input type="hidden" name="php_class" class="php_class" value="<?php echo esc_attr($php_class); ?>" />
        </form>


    <?php
    	echo $sidebar_args['after_widget'];

    	return $sidebar_args;
    }


}

new SiteEditorWidgets();


?>