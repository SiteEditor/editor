<?php
/**
 * SiteEditor Options Panel Class.
 *
 * A UI for multi settings
 *
 * @package SiteEditor
 * @subpackage Settings
 */
class SiteEditorOptionsPanel{

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @since 4.1.0
	 *
	 * @static
	 * @access protected
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var int
	 */
	public $instance_number;

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * Priority of the panel, defining the display order of panels and sections.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var integer
	 */
	public $priority = 160;

	/**
	 * Capability required for the panel.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the panel.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 * Title of the panel to show in UI.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @since 4.0.0
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * Parent panel id if value is "root" show panel
	 * in UI dialog root settings
	 *
	 * @since 4.0.0
	 * @access public
	 * @var array
	 */
	public $parent_id = "root";

	/**
	 * Type of this panel.
     * Built-in types : "default" , "expanded" , "inner_box"
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'default';


    /**
     * SiteEditor controls for this panel
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
	public $controls = array();

    /**
     * SiteEditor sub panels for this panel
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $sub_panels = array();

	/**
	 * Options group id of this panel.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $option_group = '';

    /**
     * Current Options group template
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $template;

	/**
	 * Panel container html attributes
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $atts = '';

	/**
	 * Active callback.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 *
	 * @var callable Callback is called with one argument, the instance of
	 *               {@see WP_Customize_Section}, and returns bool to indicate
	 *               whether the section is active (such as it relates to the URL
	 *               currently being previewed).
	 */
	public $active_callback = '';

    /**
     * current panel depended to other fields
     *
     * @var string
     */
    public $dependency = array();

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      An specific ID for the panel.
	 * @param array                $args    Panel arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager = $manager;
		$this->id = $id;
		if ( empty( $this->active_callback ) ) {
			$this->active_callback = array( $this, 'active_callback' );
		}
		self::$instance_count += 1;
		$this->instance_number = self::$instance_count;
		
	}

	/**
	 * Check whether panel is active to current Customizer preview.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @return bool Whether the panel is active to the current preview.
	 */
	final public function active() {
		$panel = $this;
		$active = call_user_func( $this->active_callback, $this );

		/**
		 * Filter response of WP_Customize_Panel::active().
		 *
		 * @since 4.1.0
		 *
		 * @param bool               $active  Whether the Customizer panel is active.
		 * @param WP_Customize_Panel $panel   {@see WP_Customize_Panel} instance.
		 */
		$active = apply_filters( 'sed_app_panel_active', $active, $panel );

		return $active;
	}

	/**
	 * Default callback used when invoking {@see WP_Customize_Panel::active()}.
	 *
	 * Subclasses can override this with their specific logic, or they may
	 * provide an 'active_callback' argument to the constructor.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @return bool Always true.
	 */
	public function active_callback() {
		return true;
	}

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'type' ) );
		$array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['active'] = $this->active();
		$array['instanceNumber'] = $this->instance_number;
		return $array;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the panel.
	 *
	 * @since 4.0.0
	 *
	 * @return bool False if theme doesn't support the panel or the user doesn't have the capability.
	 */
	final public function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the panel's content template for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Content for the panel.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render( );
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the panel.
	 *
	 * @since 4.0.0
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		/**
		 * Fires before rendering a Customizer panel.
		 *
		 * @since 4.0.0
		 *
		 * @param WP_Customize_Panel $this WP_Customize_Panel instance.
		 */
		do_action( 'sed_app_render_panel', $this );

		/**
		 * Fires before rendering a specific Customizer panel.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the ID of the specific Customizer panel to be rendered.
		 *
		 * @since 4.0.0
		 */
		do_action( "sed_app_render_panel_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the panel container, and then its contents (via `this->render_content()`) in a subclass.
	 *
	 * Panel containers are now rendered in JS by default, see {@see WP_Customize_Panel::print_template()}.
	 *
	 * @since 4.0.0
	 * @access protected
	 */
	protected function render() {

		$atts           = $this->input_attrs();

		$atts_string    = $atts["atts"];

		$classes        = "row_setting_box sed-panel-{$this->type} {$atts['class']}";

		$pkey			= "{$this->option_group}_{$this->id}";

		?>
		<fieldset id="<?php echo $pkey; ?>_fieldset" class="<?php echo $classes;?>" <?php echo $atts_string;?>>

			<legend id="<?php echo $pkey; ?>_title"><?php echo $this->title;?></legend>

			<?php $this->render_content(); ?>

		</fieldset>
		<?php

	}

	/**
	 * Render the custom attributes for the control's input element.
	 *
	 * @since 4.0.0
	 * @access public
	 */
	public function input_attrs() {

		$atts_string = "";
		$class = "";

		if( is_array( $this->atts ) ) {

			foreach ($this->atts as $attr => $value) {

				if( $attr == "class" ){
					$class = $value;
				}else{
					$atts_string .= $attr . '="' . esc_attr($value) . '" ';
				}

			}

		}

		return array(
			"atts"   =>  $atts_string  ,
			"class"  =>  $class
		);
	}

	/**
	 * Render the panel UI in a subclass.
	 *
	 * Panel contents are now rendered in JS by default, see {@see WP_Customize_Panel::print_template()}.
	 *
	 * @since 4.1.0
	 * @access protected
	 */
	protected function render_content() {

        $content = $this->template->get_content( $this->controls , $this->sub_panels );

        echo $content;

	}

	/**
	 * Render the control's JS template.
	 *
	 * This function is only run for control types that have been registered with
	 * {@see WP_Customize_Manager::register_control_type()}.
	 *
	 * In the future, this will also print the template for the control's container
	 * element and be override-able.
	 *
	 * @since 4.1.0
	 */
	final public function print_template() {
		?>
		<script type="text/html" id="tmpl-customize-control-<?php echo $this->type; ?>-content">
			<?php $this->content_template(); ?>
		</script>
		<?php
	}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see WP_Customize_Control::print_template()
	 *
	 * @since 4.1.0
	 */
	protected function content_template() {}

}


