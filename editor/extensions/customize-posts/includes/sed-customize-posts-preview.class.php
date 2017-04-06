<?php
/**
 * SiteEditor Customize Posts Preview Class
 *
 * Implements post management in the Customizer.
 *
 * @package WordPress
 * @subpackage Customize
 */

/**
 * Class SiteEditorCustomizePostsPreview
 */
final class SiteEditorCustomizePostsPreview {

	/**
	 * SiteEditorCustomizePosts instance.
	 *
	 * @access public
	 * @var SiteEditorCustomizePosts
	 */
	public $component;

	/**
	 * Previewed post settings by post ID.
	 *
	 * @var SiteEditorPostSetting[]
	 */
	public $previewed_post_settings = array();

	/**
	 * Previewed postmeta settings by post ID and meta key.
	 *
	 * @var SiteEditorPostmetaSetting[]
	 */
	public $previewed_postmeta_settings = array();

	/**
	 * Whether the preview filters have been added.
	 *
	 * @see SiteEditorCustomizePostsPreview::add_preview_filters()
	 * @var bool
	 */
	protected $has_preview_filters = false;

	/**
	 * Initial loader.
	 *
	 * @access public
	 *
	 * @param SiteEditorCustomizePosts $component Component.
	 */
	public function __construct( SiteEditorCustomizePosts $component ) {
		$this->component = $component;

		add_action( 'sed_app_preview_init', array( $this, 'sed_app_preview_init' ) );
	}

	/**
	 * Setup the customizer preview.
	 */
	public function sed_app_preview_init() {
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		//add_filter( 'the_posts', array( $this, 'filter_the_posts_to_add_dynamic_post_meta_settings' ), 1000 );
		add_filter( 'the_content'   , array( &$this, 'add_dynamic_post_meta_settings' ) , 0 );
		
		//add_filter( 'get_post_metadata', array( $this, 'filter_get_post_meta_to_add_dynamic_postmeta_settings' ), 1000, 2 );
		
		add_action( 'wp_footer', array( $this, 'export_preview_data' ), 10 );
		
	}

	/**
	 * Add preview filters for post and postmeta settings.
	 */
	public function add_preview_filters() {

		if ( $this->has_preview_filters ) {
			return false;
		}

		add_filter( 'get_post_metadata', array( $this, 'filter_get_post_meta_to_preview' ), 1000, 4 );

		$this->has_preview_filters = true;

		return true;
	}

	/**
	 * Enqueue scripts for the customizer preview.
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( 'customize-post-field-partial' );
		wp_enqueue_script( 'sed-app-preview-posts' );
	}

	public function add_dynamic_post_meta_settings( $post_content ){

		if ( is_singular() && in_the_loop() && is_main_query() ) {

			global $post;

			$this->component->register_post_type_meta_settings( $post->ID );

		}

		return $post_content;

	}


	/**
	 * Create dynamic post/postmeta settings and sections for posts queried in the page.
	 *
	 * @param array $posts Posts.
	 * @return array
	 */
	public function filter_the_posts_to_add_dynamic_post_meta_settings( array $posts ) {
		foreach ( $posts as &$post ) {

			$this->component->register_post_type_meta_settings( $post->ID );
		}
		return $posts;
	}


	/**
	 * Filter postmeta to dynamically add postmeta settings.
	 *
	 * @param null|array|string $value     The value get_metadata() should return - a single metadata value, or an array of values.
	 * @param int               $object_id Object ID.
	 * @return mixed Value.
	 */
	public function filter_get_post_meta_to_add_dynamic_postmeta_settings( $value, $object_id ) {
		$this->component->register_post_type_meta_settings( $object_id );
		return $value;
	}

	/**
	 * Filter postmeta to inject customized post meta values.
	 *
	 * @param null|array|string $value     The value get_metadata() should return - a single metadata value, or an array of values.
	 * @param int               $object_id Object ID.
	 * @param string            $meta_key  Meta key.
	 * @param bool              $single    Whether to return only the first value of the specified $meta_key.
	 * @return mixed Value.
	 */
	public function filter_get_post_meta_to_preview( $value, $object_id, $meta_key, $single ) {

		static $is_recursing = false;
		$should_short_circuit = (
			$is_recursing
			||
			// Abort if another filter has already short-circuited.
			null !== $value
			||
			// Abort if the post has no meta previewed.
			! isset( $this->previewed_postmeta_settings[ $object_id ] )
			||
			( '' !== $meta_key && ! isset( $this->previewed_postmeta_settings[ $object_id ][ $meta_key ] ) )
		);

		//var_dump( $meta_key ); var_dump( $object_id ); var_dump( $should_short_circuit );

		if ( $should_short_circuit ) {
			if ( is_null( $value ) ) {
				return null;
			} elseif ( ! $single && ! is_array( $value ) ) {
				return array( $value );
			} else {
				return $value;
			}
		}

		/**
		 * Setting.
		 *
		 * @var SiteEditorPostmetaSetting $postmeta_setting
		 */

		$post_values = $this->component->manager->unsanitized_post_values();

		if ( '' !== $meta_key ) {
			$postmeta_setting = $this->previewed_postmeta_settings[ $object_id ][ $meta_key ];
			$can_preview = (
				$postmeta_setting
				&&
				array_key_exists( $postmeta_setting->id, $post_values )
			);
			if ( $can_preview ) {
				$value = $postmeta_setting->post_value();
			} else {
				return null;
			}

			//for fixed bug support array meta value in single mode
			if( is_array( $value ) && $single ){
				$value = array( $value );
			}

			if ( $postmeta_setting->single ) {
				return $single ? $value : array( $value );
			} else {
				return $single ? $value[0] : $value;
			}
		} else {

			$is_recursing = true;
			$meta_values = get_post_meta( $object_id, '', $single );
			$is_recursing = false;

			foreach ( $this->previewed_postmeta_settings[ $object_id ] as $postmeta_setting ) {
				if ( ! array_key_exists( $postmeta_setting->id, $post_values ) ) {
					continue;
				}

				if ( $postmeta_setting->single ) {
					$meta_value = $postmeta_setting->post_value();
					$meta_value = maybe_serialize( $meta_value );

					// Note that $single has no effect when $meta_key is ''.
					$meta_values[ $postmeta_setting->meta_key ] = array( $meta_value );
				} else {
					$meta_value = $postmeta_setting->post_value();
					$meta_value = maybe_serialize( $meta_value );
					$meta_values[ $postmeta_setting->meta_key ] = $meta_value;
				}
			}
			return $meta_values;
		}

	}

	/**
	 * Export data into the customize preview.
	 */
	public function export_preview_data() {
		$queried_post_id = 0; // Can't be null due to wp.customize.Value.
		if ( get_queried_object() instanceof WP_Post ) {
			$queried_post_id = get_queried_object_id();
		}

		$setting_properties = array();
		foreach ( $this->component->manager->settings() as $setting ) {
			if ( $setting instanceof SiteEditorPostmetaSetting ) {

				if ( ! $setting->check_capabilities() ) {
					continue;
				}

				// Note that the value and dirty properties are already exported in wp.customize.settings.
				$setting_properties[ $setting->id ] = array(
					'transport' => $setting->transport,
					'option_type' => $setting->option_type,
				);
			}
		}

		/*$exported_partial_schema = array();
		foreach ( $this->get_post_field_partial_schema() as $key => $schema ) {
			unset( $schema['render_callback'] ); // PHP callbacks are generally not JSON-serializable.
			$exported_partial_schema[ $key ] = $schema;
		}*/

		$sed_page_id = SED()->framework->sed_page_id;
		$sed_page_type = SED()->framework->sed_page_type;

		if( $sed_page_type == "post" ){
			$post = get_post( $sed_page_id );
			$post_type = $post->post_type;
		}else{
			$post_type = '';
		}

		$exported = array(
			'isPostPreview' => is_preview(),
			'isSingular' => is_singular(),
			'queriedPostId' => $queried_post_id,
			'settingProperties' => $setting_properties,
			'currentPostType'	=> $post_type
			//'partialSchema' => $exported_partial_schema,
		); 

		$data = sprintf( 'var _sedAppPreviewPostsData = %s;', wp_json_encode( $exported ) );
		//wp_scripts()->add_data( 'sed-app-preview-posts', 'data', $data );
		?>
		<script>
			<?php echo $data;?>
		</script>
		<?php
	}

}
