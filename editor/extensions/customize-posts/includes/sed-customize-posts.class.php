<?php
/**
 * Customize Posts Component Class
 *
 * Implements post management in the Customizer.
 *
 * @package WordPress
 * @subpackage Customize
 */

/**
 * Class SiteEditorCustomizePosts
 */
final class SiteEditorCustomizePosts {

	/**
	 * SiteEditorManager instance.
	 *
	 * @access public
	 * @var SiteEditorManager
	 */
	public $manager;

	/**
	 * Previewing posts.
	 *
	 * @var SiteEditorCustomizePostsPreview
	 */
	public $preview;

	/**
	 * List of settings that have update conflicts in the current request.
	 *
	 * @var SedAppSettings[]
	 */
	public $update_conflicted_settings = array();

	/**
	 * Registered post meta.
	 *
	 * @var array
	 */
	public $registered_post_meta = array();

	/**
	 * Registered support classes.
	 *
	 * @var array
	 */
	public $supports = array();

	/**
	 * Whether the post link filters are being suppressed.
	 *
	 * @var bool
	 */
	public $suppress_post_link_filters = false;

	/**
	 * Initial loader.
	 *
	 * @access public
	 *
	 * @param SiteEditorManager $manager Customize manager bootstrap instance.
	 */
	public function __construct( SiteEditorManager $manager ) {
		$this->manager = $manager;

		require_once dirname( __FILE__ ) . '/sed-customize-posts-preview.class.php';
		require_once dirname( __FILE__ ) . '/sed-customize-post-setting.class.php';
		require_once dirname( __FILE__ ) . '/sed-customize-postmeta-setting.class.php';

		add_action( 'sed_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		//add_action( 'customize_register', array( $this, 'register_constructs' ), 20 );

		add_action( 'init', array( $this, 'register_meta' ), 100 );
		add_filter( 'sed_app_dynamic_setting_args', array( $this, 'filter_customize_dynamic_setting_args' ), 10, 2 );
		add_filter( 'sed_app_dynamic_setting_class', array( $this, 'filter_customize_dynamic_setting_class' ), 5, 3 );
		//add_filter( 'customize_save_response', array( $this, 'filter_customize_save_response_for_conflicts' ), 10, 2 );
		//add_filter( 'customize_save_response', array( $this, 'filter_customize_save_response_to_export_saved_values' ), 10, 2 );

		$this->preview = new SiteEditorCustomizePostsPreview( $this );
	}

	/**
	 * Instantiate a Customize Posts support class.
	 *
	 * The support class must extend `Customize_Posts_Support` or one of it's subclasses.
	 *
	 * @param string|Customize_Posts_Support $support The support class name or object.
	 */
	function add_support( $support ) {
		if ( is_string( $support ) && class_exists( $support, false ) ) {
			$support = new $support( $this );
		}

		if ( $support instanceof SiteEditorCustomizePostsSupport ) {
			$class_name = get_class( $support );
			if ( ! isset( $this->supports[ $class_name ] ) ) {
				$this->supports[ $class_name ] = $support;
				$support->init();
			}
		}
	}

	/**
	 * Get post type objects that can be managed in Customizer.
	 *
	 * By default only post types which have show_ui and publicly_queryable as true
	 * will be included. This can be overridden if an explicit show_in_customizer
	 * arg is provided when registering the post type.
	 *
	 * @return array
	 */
	public function get_post_types() {
		$post_types = array();
		$post_type_objects = get_post_types( array(), 'objects' );
		foreach ( $post_type_objects as $post_type_object ) {
			$post_type_object = clone $post_type_object;
			if ( ! isset( $post_type_object->show_in_customizer ) ) {
				$post_type_object->show_in_customizer = $post_type_object->show_ui;
			}
			$post_type_object->supports = get_all_post_type_supports( $post_type_object->name );

			// Remove unnecessary properties.
			unset( $post_type_object->register_meta_box_cb );

			$post_types[ $post_type_object->name ] = $post_type_object;
		}

		// Skip media as special case.
		unset( $post_types['attachment'] );

		return $post_types;
	}

	/**
	 * Set missing post type descriptions for built-in post types.
	 */
	public function set_builtin_post_type_descriptions() {
		global $wp_post_types;
		if ( post_type_exists( 'post' ) && empty( $wp_post_types['post']->description ) ) {
			$wp_post_types['post']->description = __( 'Posts are entries listed in reverse chronological order, usually on the site homepage or on a dedicated posts page. Posts can be organized by tags or categories.', 'site-editor' );
		}
		if ( post_type_exists( 'page' ) && empty( $wp_post_types['page']->description ) ) {
			$wp_post_types['page']->description = __( 'Pages are ordered and organized hierarchcichally instead of being listed by date. The organization of pages generally corresponds to the primary nav menu.', 'site-editor' );
		}
	}

	/**
	 * Register post meta for a given post type.
	 *
	 * Please note that a sanitize_callback is intentionally excluded because the
	 * meta sanitization logic should be re-used with the global register_meta()
	 * function, which includes a `$sanitize_callback` param.
	 *
	 * @see register_meta()
	 *
	 * @param string $post_type    Post type.
	 * @param string $meta_key     Meta key.
	 * @param array  $setting_args Args.
	 */
	public function register_post_type_meta( $post_type, $meta_key, $setting_args = array() ) {
		$setting_args = array_merge(
			array(
				'capability' => null,
				'theme_supports' => null,
				'default' => null,
				'transport' => null,
				'sanitize_callback' => null,
				'sanitize_js_callback' => null,
				'validate_callback' => null,
				'setting_class' => 'SiteEditorPostmetaSetting',
			),
			$setting_args
		);

		if ( ! has_filter( "auth_post_meta_{$meta_key}", array( $this, 'auth_post_meta_callback' ) ) ) {
			add_filter( "auth_post_meta_{$meta_key}", array( $this, 'auth_post_meta_callback' ), 10, 4 );
		}

		// Filter out null values, aka array_filter with ! is_null.
		foreach ( array_keys( $setting_args ) as $key => $value ) {
			if ( is_null( $value ) ) {
				unset( $setting_args[ $key ] );
			}
		}

		if ( ! isset( $this->registered_post_meta[ $post_type ] ) ) {
			$this->registered_post_meta[ $post_type ] = array();
		}

		$this->registered_post_meta[ $post_type ][ $meta_key ] = $setting_args; //var_dump( $this->registered_post_meta );
	}

	/**
	 * Allow editing post meta in Customizer if user can edit_post for registered post meta.
	 *
	 * @param bool   $allowed  Whether the user can add the post meta. Default false.
	 * @param string $meta_key The meta key.
	 * @param int    $post_id  Post ID.
	 * @param int    $user_id  User ID.
	 * @return bool Allowed.
	 */
	public function auth_post_meta_callback( $allowed, $meta_key, $post_id, $user_id ) {
		global $wp_customize;
		if ( $allowed || empty( $wp_customize ) ) {
			return $allowed;
		}
		$post = get_post( $post_id );
		if ( ! $post ) {
			return $allowed;
		}
		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return $allowed;
		}
		if ( ! isset( $this->registered_post_meta[ $post->post_type ][ $meta_key ] ) ) {
			return $allowed;
		}
		$registered_post_meta = $this->registered_post_meta[ $post->post_type ][ $meta_key ];
		$allowed = (
			( empty( $registered_post_meta['capability'] ) || user_can( $user_id, $registered_post_meta['capability'] ) )
			&&
			user_can( $user_id, $post_type_object->cap->edit_post, $post_id )
		);
		return $allowed;
	}

	/**
	 * Register post meta for the post types.
	 *
	 * Note that this has to be after all post types are registered.
	 */
	public function register_meta() {

		/**
		 * Allow plugins to register meta.
		 *
		 * @param SiteEditorCustomizePosts $this
		 */
		do_action( 'sed_app_posts_register_meta', $this );
	}

	/**
	 * Determine the arguments for a dynamically-created setting.
	 *
	 * @access public
	 *
	 * @param false|array $args       The arguments to the SedAppSettings constructor.
	 * @param string      $setting_id ID for dynamic setting, usually coming from `$_POST['customized']`.
	 * @return false|array Setting arguments, false otherwise.
	 */
	public function filter_customize_dynamic_setting_args( $args, $setting_id ) {

		if ( preg_match( SiteEditorPostSetting::SETTING_ID_PATTERN, $setting_id, $matches ) ) {
			$post_type = get_post_type_object( $matches['post_type'] );
			if ( ! $post_type ) {
				return $args;
			}
			if ( false === $args ) {
				$args = array();
			}
			$args['option_type'] = 'post';
			$args['transport'] = 'postMessage';
		} elseif ( preg_match( SiteEditorPostmetaSetting::SETTING_ID_PATTERN, $setting_id, $matches ) ) {
			if ( ! post_type_exists( $matches['post_type'] ) ) {
				return $args;
			}
			if ( ! isset( $this->registered_post_meta[ $matches['post_type'] ][ $matches['meta_key'] ] ) ) {
				return $args;
			}
			$registered = $this->registered_post_meta[ $matches['post_type'] ][ $matches['meta_key'] ];
			if ( isset( $registered['theme_supports'] ) && ! current_theme_supports( $registered['theme_supports'] )  && ! sed_current_theme_supports( $registered['theme_supports'] ) ) {
				// We don't really need this because theme_supports will already filter it out of being exported.
				return $args;
			}
			if ( false === $args ) {
				$args = array();
			}
			$args = array_merge(
				$args,
				$registered
			);
			$args['option_type'] = 'postmeta';
		}

		return $args;
	}

	/**
	 * Filters customize_dynamic_setting_class.
	 *
	 * @param string $class      Setting class.
	 * @param string $setting_id Setting ID.
	 * @param array  $args       Setting args.
	 *
	 * @return string
	 */
	public function filter_customize_dynamic_setting_class( $class, $setting_id, $args ) {
		unset( $setting_id );
		if ( isset( $args['option_type'] ) ) {
			if ( 'post' === $args['option_type'] ) {
				$class = 'SiteEditorPostSetting';
			} elseif ( 'postmeta' === $args['option_type'] ) {
				if ( isset( $args['setting_class'] ) ) {
					$class = $args['setting_class'];
				} else {
					$class = 'SiteEditorPostmetaSetting';
				}
			}
		}
		return $class;
	}

	/**
	 * Add all postmeta settings for all registered postmeta for a given post type instance.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public function register_post_type_meta_settings( $post_id ) {
		$post = get_post( $post_id );
		$setting_ids = array();
		if ( ! empty( $post ) && isset( $this->registered_post_meta[ $post->post_type ] ) ) {
			foreach ( array_keys( $this->registered_post_meta[ $post->post_type ] ) as $key ) {
				$setting_ids[] = SiteEditorPostmetaSetting::get_post_meta_setting_id( $post, $key );
			}
		}
		$this->manager->add_dynamic_settings( $setting_ids );

		return $setting_ids;
	}

	/**
	 * When loading the customizer from a post, get the post.
	 *
	 * @return WP_Post|null
	 */
	public function get_previewed_post() {
		$post_id = url_to_postid( $this->manager->get_preview_url() );
		if ( 0 === $post_id ) {
			return null;
		}
		$post = get_post( $post_id );
		return $post;
	}

	/**
	 * Get the post status choices array.
	 *
	 * @return array
	 */
	public function get_post_status_choices() {
		$choices = array(
			array(
				'value' => 'draft',
				'text'  => __( 'Draft', 'site-editor' ),
			),
			array(
				'value' => 'pending',
				'text'  => __( 'Pending Review', 'site-editor' ),
			),
			array(
				'value' => 'private',
				'text'  => __( 'Private', 'site-editor' ),
			),
			array(
				'value' => 'publish',
				'text'  => __( 'Published', 'site-editor' ),
			),
			array(
				'value' => 'trash',
				'text'  => __( 'Trash', 'site-editor' ),
			),
		);

		return $choices;
	}

	/**
	 * Get the author choices array.
	 *
	 * @return array
	 */
	public function get_author_choices() {
		$choices = array();
		$query_args = array(
			'orderby' => 'display_name',
			'who' => 'authors',
			'fields' => array( 'ID', 'user_login', 'display_name' ),
		);
		$users = get_users( $query_args );

		if ( ! empty( $users ) ) {
			foreach ( (array) $users as $user ) {
				$choices[] = array(
					'value' => (int) $user->ID,
					'text'  => esc_html( sprintf( _x( '%1$s (%2$s)', 'user dropdown', 'site-editor' ), $user->display_name, $user->user_login ) ),
				);
			}
		}

		return $choices;
	}

	/**
	 * Return whether current user can edit supplied post.
	 *
	 * @param WP_Post|int $post Post.
	 * @return boolean
	 */
	public function current_user_can_edit_post( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}
		if ( ! $post ) {
			return false;
		}
		$post_type_obj = get_post_type_object( $post->post_type );
		if ( ! $post_type_obj ) {
			return false;
		}
		$can_edit = current_user_can( $post_type_obj->cap->edit_post, $post->ID );
		return $can_edit;
	}

	/**
	 * Return the latest setting data for conflicted posts.
	 *
	 * Note that this uses `SedAppSettings::value()` in a way that assumes
	 * that the `SedAppSettings::preview()` has not been called, as it not
	 * called when `SiteEditorManager::save()` happens.
	 *
	 * @param array $response Response.
	 * @return array
	 */
	public function filter_customize_save_response_for_conflicts( $response ) {
		if ( ! empty( $this->update_conflicted_settings ) ) {
			$response['update_conflicted_setting_values'] = array();
			foreach ( $this->update_conflicted_settings as $setting_id => $setting ) {
				$response['update_conflicted_setting_values'][ $setting_id ] = $setting->js_value();
			}
		}
		return $response;
	}

	/**
	 * Return the saved sanitized values for posts and postmeta to update in the client.
	 *
	 * This was originally in the Customize Setting Validation plugin.
	 *
	 * @link https://github.com/xwp/wp-customize-setting-validation/blob/2e5ddc66a870ad7b1aee5f8e414bad4b78e120d2/php/class-plugin.php#L283-L317
	 *
	 * @param array $response Response.
	 * @return array
	 */
	public function filter_customize_save_response_to_export_saved_values( $response ) {
		$response['saved_post_setting_values'] = array();
		foreach ( array_keys( $this->manager->unsanitized_post_values() ) as $setting_id ) {
			$setting = $this->manager->get_setting( $setting_id );
			if ( $setting instanceof SiteEditorPostSetting || $setting instanceof SiteEditorPostmetaSetting ) {
				$response['saved_post_setting_values'][ $setting->id ] = $setting->js_value();
			}
		}
		return $response;
	}

	/**
	 * Enqueue scripts and styles for Customize Posts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'sed-app-posts' );
		//wp_enqueue_style( 'sed-app-posts' );

		$post_types = array();
		foreach ( $this->get_post_types() as $post_type => $post_type_obj ) {
			if ( ! current_user_can( $post_type_obj->cap->edit_posts ) ) {
				continue;
			}

			$post_types[ $post_type ] = array_merge(
				wp_array_slice_assoc( (array) $post_type_obj, array(
					'name',
					'supports',
					'labels',
					'has_archive',
					'menu_icon',
					'description',
					'hierarchical',
					'show_in_customizer',
					'publicly_queryable',
					'public',
				) ),
				array(
					'current_user_can' => array(
						'create_posts' => isset( $post_type_obj->cap->create_posts ) && current_user_can( $post_type_obj->cap->create_posts ),
						'delete_posts' => isset( $post_type_obj->cap->delete_posts ) && current_user_can( $post_type_obj->cap->delete_posts ),
					),
				)
			);
		}

		$exports = array(
			'nonce' => wp_create_nonce( 'sed-app-posts' ),
			'postTypes' => $post_types,
			'postStatusChoices' => $this->get_post_status_choices(),
			'authorChoices' => $this->get_author_choices(),
			'l10n' => array(
				/* translators: &#9656; is the unicode right-pointing triangle, and %s is the section title in the Customizer */
				'sectionCustomizeActionTpl' => __( 'Customizing &#9656; %s', 'site-editor' ),
				'fieldTitleLabel' => __( 'Title', 'site-editor' ),
				'fieldSlugLabel' => __( 'Slug', 'site-editor' ),
				'fieldPostStatusLabel' => __( 'Post Status', 'site-editor' ),
				'fieldContentLabel' => __( 'Content', 'site-editor' ),
				'fieldExcerptLabel' => __( 'Excerpt', 'site-editor' ),
				'fieldDiscussionLabel' => __( 'Discussion', 'site-editor' ),
				'fieldAuthorLabel' => __( 'Author', 'site-editor' ),
				'noTitle' => __( '(no title)', 'site-editor' ),
				'theirChange' => __( 'Their change: %s', 'site-editor' ),
				'openEditor' => __( 'Open Editor', 'site-editor' ),
				'closeEditor' => __( 'Close Editor', 'site-editor' ),
			),
		);

		wp_scripts()->add_data( 'sed-app-posts' , 'data', sprintf( 'var _sedAppPostsDataExports = %s;', wp_json_encode( $exports ) ) );
	}

	/**
	 * Sanitize a value as a post ID.
	 *
	 * @param mixed $value Value.
	 * @return int Sanitized post ID.
	 */
	public function sanitize_post_id( $value ) {
		$value = intval( $value );
		return $value;
	}

}
