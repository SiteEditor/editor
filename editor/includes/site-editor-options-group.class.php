<?php
/**
 * SiteEditor Options Group primary class
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 * SiteEditor Options Group class.
 *
 * Manage Various types of options
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Manager
 */
class SiteEditorOptionsGroup {

	/**
	 * WP_Customize_Manager instance.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Unique identifier.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * Capability required for the options group.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the options group.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 * Title of the options group to show in UI.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var string
	 */
	public $description = '';

	/**
	 * Customizer controls for this options group.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var array
	 */
	public $controls = array();

	/**
	 * Customizer controls for this options group.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * Customizer controls for this options group.
	 *
	 * @since 3.4.0
	 * @access public
	 * @var array
	 */
	public $panels = array();

	/**
	 * Type of this options group.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $type = 'default';

	/**
	 * Type of this options group.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $pages_dependency = false;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      An specific ID of the options group.
	 * @param array                $args    Section arguments.
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

		$this->controls = array(); // Users cannot customize the $controls array.

        $this->panels = array(); // Users cannot customize the $controls array.

        $this->settings = array(); // Users cannot customize the $controls array.

	}

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @since 4.1.0
	 *
	 * @return array The array to be exported to the client as JSON.
	 */
	public function json() {
		$array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'type' , 'pages_dependency' ) );
		$array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		//$array['content'] = $this->get_content();

		return $array;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the options group.
	 *
	 * @since 3.4.0
	 *
	 * @return bool False if theme doesn't support the options group or user doesn't have the capability.
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
	 * Get the options group's content for insertion into the Customizer pane.
	 *
	 * @since 4.1.0
	 *
	 * @return string Contents of the options group.
	 */
	final public function get_content() {
		ob_start();
		$this->maybe_render();
		return trim( ob_get_clean() );
	}

	/**
	 * Check capabilities and render the options group.
	 *
	 * @since 3.4.0
	 */
	final public function maybe_render() {
		if ( ! $this->check_capabilities() ) {
			return;
		}

		/**
		 * Fires before rendering a Customizer options group.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Section $this WP_Customize_Section instance.
		 */
		do_action( 'sed_app_render_options_group', $this );
		/**
		 * Fires before rendering a specific Customizer options group.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to the ID
		 * of the specific Customizer options group to be rendered.
		 *
		 * @since 3.4.0
		 */
		do_action( "sed_app_render_options_group_{$this->id}" );

		$this->render();
	}

	/**
	 * Render the options group UI in a subclass.
	 *
	 * Sections are now rendered in JS by default, see {@see WP_Customize_Section::print_template()}.
	 *
	 * @since 3.4.0
	 */
	protected function render() {
		?>
		<div id="dialog-level-box-settings-<?php echo $this->id;?>-container" data-title="<?php echo $this->title;?>" class="dialog-level-box-settings-container " ><!--hide-->

            <?php $this->render_content(); ?>

		</div>
		<?php
    }

    /**
     * Render the group's content.
     *
     * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
     *
     *
     * @since 3.4.0
     */
    protected function render_content() {

        $options_template = new SiteEditorOptionsTemplate( $this->controls , $this->panels );

        echo $options_template->render();
        
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
