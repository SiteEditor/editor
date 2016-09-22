<?php
/**
 * SiteEditor Control Class.
 *
 * Handles add control in options engine
 *
 * @package SiteEditor
 * @subpackage Options
 * @since 3.4.0
 */
class SiteEditorOptionsControl{

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
	 * @access public
	 * @var SiteEditorManager
	 */
	public $manager;

	/**
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * All settings tied to the control.
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * The primary setting for the control (if there is one).
	 *
	 * @access public
	 * @var string
	 */
	public $setting = 'default';

	/**
	 * Capability required to use this control.
	 *
	 * Normally this is empty and the capability is derived from the capabilities
	 * of the associated `$settings`.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $capability;

	/**
	 * @access public
	 * @var int
	 */
	public $priority = 10;

	/**
	 * panel id that this control blong to it
	 *
	 * @access public
	 * @var string
	 */
	public $panel = '';

	/**
	 * @access public
	 * @var string
	 */
	public $label = '';

	/**
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * @todo: Remove choices
	 *
	 * @access public
	 * @var array
	 */
	public $choices = array();

	/**
	 * @access public
	 * @var array
	 */
	public $atts = array();

	/**
	 * @deprecated It is better to just call the json() method
	 * @access public
	 * @var array
	 */
	public $json = array();

	/**
	 * @access public
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Js type for some case with different js type and type
	 * @access public
	 * @var string
	 */
	public $js_type = '';

	/**
	 * @access public
	 * @var string
	 */
	public $has_border_box = true;

	/**
	 * Custom user params for send to js
	 *
	 * @access public
	 * @var string
	 */
	public $js_params = array();

	/**
	 * Option group id of this panel.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $option_group = '';

	/**
	 * Callback.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @see WP_Customize_Control::active()
	 *
	 * @var callable Callback is called with one argument, the instance of
	 *               WP_Customize_Control, and returns bool to indicate whether
	 *               the control is active (such as it relates to the URL
	 *               currently being previewed).
	 */
	public $active_callback = '';

    /**
     * Control category like "app-settings" or "module-settings" or "style-editor"
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $category = 'app-settings';

    /**
     * Control sub category sample for "module-settings" category is "sed_image"
	 * For "app-settings" category : sub category === settings Id === optionsGroup [+ "_" + CurrentPageId] (if options group
	 * === "sed_page_options" or "sed_content_options" )
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $sub_category = '';

    /**
     * Control default value for modules controls ( sed_pb_modules or style editor settings )
	 * default value is for helper settings
	 * helper settings not saved in db directly 
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $default_value;

    /**
     * Control shortcode for modules controls
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $shortcode;

    /**
     * Control shortcode attribute name for modules controls
     *
     * @since 1.0.0
     * @access public
     * @var string
     */
    public $attr_name;

    /**
     * If control is module control is_attr will be true
     *
     * @since 1.0.0
     * @access public
     * @var bool
     */
    public $is_attr = false;

    /**
     * is control a style setting ?
     *
     * @since 1.0.0
     * @access public
     * @var bool
     */
    public $is_style_setting = false;

	/**
	 * is control a style setting ?
	 *
	 * @since 1.0.0
	 * @access public
	 * @var bool
	 */
	public $css_setting_type = '';

	/**
	 * Lock Control Id
	 *
	 * @access protected
	 * @var array
	 */
	public $lock_id = '';

	/**
	 * Constructor.
	 *
	 * Supplied `$args` override class property defaults.
	 *
	 * If `$args['settings']` is not defined, use the $id as the setting ID.
	 *
	 * @since 3.4.0
	 *
	 * @param SiteEditorManager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    {
	 *     Optional. Arguments to override class property defaults.
	 *
	 *     @type int                  $instance_number Order in which this instance was created in relation
	 *                                                 to other instances.
	 *     @type SiteEditorManager $manager         Customizer bootstrap instance.
	 *     @type string               $id              Control ID.
	 *     @type array                $settings        All settings tied to the control. If undefined, `$id` will
	 *                                                 be used.
	 *     @type string               $setting         The primary setting for the control (if there is one).
	 *                                                 Default 'default'.
	 *     @type int                  $priority        Order priority to load the control. Default 10.
	 *     @type string               $panel         Section the control belongs to. Default empty.
	 *     @type string               $label           Label for the control. Default empty.
	 *     @type string               $description     Description for the control. Default empty.
	 *     @type array                $choices         List of choices for 'radio' or 'select' type controls, where
	 *                                                 values are the keys, and labels are the values.
	 *                                                 Default empty array.
	 *     @type array                $atts     	   List of custom input attributes for control output, where
	 *                                                 attribute names are the keys and values are the values. Not
	 *                                                 used for 'checkbox', 'radio', 'select', 'textarea', or
	 *                                                 'dropdown-pages' control types. Default empty array.
	 *     @type array                $json            Deprecated. Use {@see WP_Customize_Control->json()} instead.
	 *     @type string               $type            Control type. Core controls include 'text', 'checkbox',
	 *                                                 'textarea', 'radio', 'select', and 'dropdown-pages'. Additional
	 *                                                 input types such as 'email', 'url', 'number', 'hidden', and
	 *                                                 'date' are supported implicitly. Default 'text'.
	 * }
	 */
	public function __construct( $manager, $id, $args = array() ) {

        if( isset( $args['control_param'] ) ){
            $args['js_params'] = $args['control_param'];
            unset( $args['control_param'] );
        }

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

		// Process settings.
		if ( ! isset( $this->settings ) ) {
			$this->settings = $id;
		}

		$settings = array();
		if ( is_array( $this->settings ) ) {
			foreach ( $this->settings as $key => $setting ) {
				$settings[ $key ] = $this->manager->get_setting( $setting );
			}
		} else if ( is_string( $this->settings ) ) {
			$this->setting = $this->manager->get_setting( $this->settings );
			$settings['default'] = $this->setting;
		}
		$this->settings = $settings;
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @since 3.4.0
	 */
	public function enqueue() {}

	/**
	 * Check whether control is active to current Customizer preview.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	final public function active() { 
		$control = $this;
		$active = call_user_func( $this->active_callback, $this );

		/**
		 * Filter response of WP_Customize_Control::active().
		 *
		 * @since 4.0.0
		 *
		 * @param bool                 $active  Whether the Customizer control is active.
		 * @param WP_Customize_Control $control WP_Customize_Control instance.
		 */
		$active = apply_filters( 'sed_app_control_active', $active, $control );

		return $active;
	}

	/**
	 * Default callback used when invoking WP_Customize_Control::active().
	 *
	 * Subclasses can override this with their specific logic, or they may
	 * provide an 'active_callback' argument to the constructor.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @return true Always true.
	 */
	public function active_callback() {
		return true;
	}

	/**
	 * Fetch a setting's value.
	 * Grabs the main setting by default.
	 *
	 * @since 3.4.0
	 *
	 * @param string $setting_key
	 * @return mixed The requested setting's value, if the setting exists.
	 */
	final public function value( $setting_key = 'default' ) {
		if ( isset( $this->settings[ $setting_key ] ) ) {
			return $this->settings[ $setting_key ]->value();
		}
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @since 3.4.0
	 */
	public function to_json() {

	}

	/**
	 * Get the data to export to the client via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array Array of parameters passed to the JavaScript.
	 */
	public function json() {
		$this->to_json();

		$json_array = array();

		$json_array['settings'] = array();
		foreach ( $this->settings as $key => $setting ) {
			$json_array['settings'][ $key ] = $setting->id;
		}

		$json_array = $this->js_params_json( $json_array );

		$json_array['control_id'] = $this->id;
		$json_array['type'] = ( !empty( $this->js_type ) ) ? $this->js_type : $this->type;
		$json_array['priority'] = $this->priority;
		$json_array['active'] = $this->active();
		$json_array['panel'] = $this->panel;
		$json_array['label'] = $this->label;
		$json_array['description'] = $this->description;
		$json_array['instanceNumber'] = $this->instance_number;

		$json_array['category'] = $this->category;

        $sub_category = ( !empty( $this->sub_category ) ) ? $this->sub_category : $this->option_group;
        $sub_category = apply_filters( "sed_control_sub_category" , $sub_category , $this );

		$json_array['sub_category'] = $sub_category;

		$json_array['default_value'] = $this->default_value;
		$json_array['is_style_setting'] = $this->is_style_setting;

		$json_array['option_group'] = $this->option_group;

		if( $this->category == "module-settings" ){
			$json_array['shortcode'] = $this->shortcode;
			$json_array['attr_name'] = $this->attr_name;
			$json_array['is_attr'] = $this->is_attr;
		}else if( $this->category == "style-editor" && !empty( $this->css_setting_type ) ){
			$json_array['css_setting_type'] = $this->css_setting_type;
		}

		if( !empty( $this->lock_id ) ){
			$json_array['lock_id'] = $this->lock_id;
		}

		$json_array = array_merge( $json_array , $this->json );

		return $json_array;
	}

	/**
	 * @param $json_array
	 * @return array
	 */
	protected function js_params_json( $json_array ){

		if( !empty( $this->js_params ) && is_array( $this->js_params ) ){
			$json_array = array_merge( $json_array , $this->js_params );
		}

		return $json_array;

	}

	/**
	 * Checks if the user can use this control.
	 *
	 * Returns false if the user cannot manipulate one of the associated settings,
	 * or if one of the associated settings does not exist. Also returns false if
	 * the associated panel does not exist or if its capability check returns
	 * false.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the control or user doesn't have the required permissions, otherwise true.
	 */
	final public function check_capabilities() {
		if ( ! empty( $this->capability ) && ! current_user_can( $this->capability ) ) {
			return false;
		}

		foreach ( $this->settings as $setting ) {
			if ( ! $setting || ! $setting->check_capabilities() ) {
				return false;
			}
		}

		$panel = $this->manager->get_panel( $this->panel );
		if ( isset( $panel ) && ! $panel->check_capabilities() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the control's content for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Contents of the control.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the control.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Control::render()
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() )
			return;

		/**
		 * Fires just before the current Customizer control is rendered.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Control $this WP_Customize_Control instance.
		 */
		do_action( 'sed_app_render_control', $this );

		/**
		 * Fires just before a specific Customizer control is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the control ID.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Control $this {@see WP_Customize_Control} instance.
		 */
		do_action( 'sed_app_render_control_' . $this->id, $this );

		$this->render();
	}

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 * @todo: Using Tpl file for template instead directly in class
	 *
	 * @since 3.4.0
	 */
	protected function render() {

		$id    = 'sed-app-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );

		$class = 'row_setting_inner sed-app-container-control sed-app-container-control-' . $this->type;

		$class .= ( $this->has_border_box ) ? ' row_setting_box' : '';

		?>

        <!-- * required for panel & control container because needed for dependency in js -->
		<div class="row_settings">

			<div class="<?php echo esc_attr( $class ); ?>">

				<div id="<?php echo esc_attr( $id ); ?>" class="clearfix">

					<?php $this->render_content(); ?>

				</div>

			</div>

		</div>

		<?php
	}

	/**
	 * Render the custom attributes for the control's input or main element.
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
	 * Render the control's content.
	 * @todo: Using Tpl file for template instead directly in class
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
	 *
	 * Supports basic input types `text`, `checkbox`, `textarea`, `radio`, `select` and `dropdown-pages`.
	 * Additional input types such as `email`, `url`, `number`, `hidden` and `date` are supported implicitly.
	 *
	 * Control content can alternately be rendered in JS. See {@see WP_Customize_Control::print_template()}.
	 *
	 * @since 3.4.0
	 */
	protected function render_content() {

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
