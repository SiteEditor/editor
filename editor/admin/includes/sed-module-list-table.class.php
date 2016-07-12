<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SiteEditorModuleListTable extends WP_List_Table {

	function __construct( $args = array() ){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'module',     //singular name of the listed records
            'plural'    => 'modules',    //plural name of the listed records
            'ajax'      => false ,       //does this table support ajax?
            'screen'    => isset( $args['screen'] ) ? $args['screen'] : null,
        ) );

        $all_statuses = array( 'active', 'inactive', 'base' , 'incomplete', 'search' );

		$status = 'all';
		if ( isset( $_REQUEST['show_modules'] ) && in_array( $_REQUEST['show_modules'], $all_statuses ) )
			$status = $_REQUEST['show_modules'];

		if ( isset($_REQUEST['s']) )
			$_SERVER['REQUEST_URI'] = add_query_arg('s', wp_unslash($_REQUEST['s']) );

		$page = $this->get_pagenum();

	}

	protected function get_table_classes() {
		return array( 'widefat', 'plugins' ); //$this->_args['plural']
	}

	public function ajax_user_can() {
		return current_user_can('activate_modules');
	}

    function prepare_items() {
        global $status, $modules, $totals, $page, $orderby, $order, $s ,$sed_pb_modules;

        wp_reset_vars( array( 'orderby', 'order', 's' ) );

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
		/**
		 * Filter the full array of plugins to list in the Plugins list table.
		 *
		 * @since 3.0.0
		 *
		 * @see get_plugins()
		 *
		 * @param array $plugins An array of plugins to display in the list table.
		 */

		$modules = array(
			'all' => $sed_pb_modules->get_modules(),
			'search' => array(),
			'active' => array(),
			'inactive' => array(),
			'base' => array(),
			'incomplete' => array()
		);

        $screen = $this->screen;

        set_transient( 'sed_modules_slugs', array_keys( $modules['all'] ), DAY_IN_SECONDS );

        $module_info = get_site_transient( 'sed_pb_update_modules' );

		foreach ( (array) $modules['all'] as $module_file => $module_data ) {

            $modules['all'][ $module_file ] = $module_data;

            if( $sed_pb_modules->is_module_base( $module_file ) ){
                $modules['base'][ $module_file ] = $module_data;
            }

            if( !$sed_pb_modules->is_install( $module_file ) ){
                $modules['incomplete'][ $module_file ] = $module_data;
            }

			// Filter into individual sections
			if ( $sed_pb_modules->is_module_active( $module_file ) ) {
				// On the non-network screen, populate the active list with plugins that are individually activated
				// On the network-admin screen, populate the active list with plugins that are network activated
				$modules['active'][ $module_file ] = $module_data;
			} else if( $sed_pb_modules->is_install( $module_file ) ) {
				// Populate the inactive list with plugins that aren't activated
				$modules['inactive'][ $module_file ] = $module_data;
			}
		}

		if ( $s ) {
			$status = 'search';
			$modules['search'] = array_filter( $modules['all'], array( $this, '_search_callback' ) );
		}

		$totals = array();
		foreach ( $modules as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $modules[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = array();
		foreach ( $modules[ $status ] as $module_file => $module_data ) {
			// Translate, Don't Apply Markup, Sanitize HTML
			$this->items[$module_file] = $sed_pb_modules->get_module_data_markup_translate( $module_file, $module_data, false, true );
		}

		$total_this_page = $totals[ $status ];

		if ( $orderby ) {
			$orderby = ucfirst( $orderby );
			$order = strtoupper( $order );

			uasort( $this->items, array( $this, '_order_callback' ) );
		}

        // get the current user ID
        $user = get_current_user_id();

        // retrieve the "per_page" option
        $screen_option = $screen->get_option('per_page', 'option');
        // retrieve the value of the option stored for the current user
        $modules_per_page = get_user_meta($user, $screen_option, true);
        if ( empty ( $modules_per_page) || $modules_per_page < 1 ) {
        	// get the default value if none is set
        	$modules_per_page = $screen->get_option( 'per_page', 'default' );
        }

		$start = ( $page - 1 ) * $modules_per_page;

		if ( $total_this_page > $modules_per_page )
			$this->items = array_slice( $this->items, $start, $modules_per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $modules_per_page,
		) );

    }
	/**
	 * @staticvar string $term
	 * @param array $plugin
	 * @return boolean
	 */
    function _search_callback( $module ) {
        static $term;
        if ( is_null( $term ) )
            $term = wp_unslash( $_REQUEST['s'] );

        foreach ( $module as $value ) {
            if ( false !== stripos( strip_tags( $value ), $term ) ) {
                return true;
            }
        }

        return false;
    }

	/**
	 * @global string $orderby
	 * @global string $order
	 * @param array $plugin_a
	 * @param array $plugin_b
	 * @return int
	 */
	public function _order_callback( $module_a, $module_b ) {
		global $orderby, $order;

		$a = $module_a[$orderby];
		$b = $module_b[$orderby];

		if ( $a == $b )
			return 0;

		if ( 'DESC' == $order )
			return ( $a < $b ) ? 1 : -1;
		else
			return ( $a < $b ) ? -1 : 1;
	}

    function no_items() {
        global $modules;

        if ( !empty( $modules['all'] ) )
            _e( 'No Modules found.' , 'site-editor' );
        else
            _e( 'You do not appear to have any Modules available at this time.' , 'site-editor' );
    }

    function get_columns(){

        $columns = array(
                'cb'             => '<input type="checkbox" />' ,
                "name"           => __( "Module Name" , "site-editor" ) ,
                "description"    => __( "Description" , "site-editor" ) ,
        );
        return $columns;

    }

    function get_sortable_columns() {

        $sortable_columns = array(
                "name"        => array('name', false ),
        );
        return $sortable_columns;
    }


    function get_views(){
        global $totals, $status;

        $url_module = 'admin.php?page=site_editor_module';

		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( !$count )
				continue;

			switch ( $type ) {
				case 'all':
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'modules' );
					break;
				case 'base':
					$text = _n( 'Base <span class="count">(%s)</span>', 'Base <span class="count">(%s)</span>', $count );
					break;
				case 'active':
					$text = _n( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', $count );
					break;
				case 'inactive':
					$text = _n( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', $count );
					break;
				case 'incomplete':
					$text = _n( 'Incomplete <span class="count">(%s)</span>', 'Incomplete <span class="count">(%s)</span>', $count );
					break;
			}

			if ( 'search' != $type ) {
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					self_admin_url( add_query_arg( array( "show_modules" => $type ) , $url_module) ),
					( $type == $status ) ? ' class="current"' : '',
					sprintf( $text, number_format_i18n( $count ) )
					);
			}
		}

        return $status_links;
    }


    function get_bulk_actions() {
        $actions = array(
            'activate-selected'    => __( 'Activate' , 'site-editor') ,
            'deactivate-selected'    => __('Deactivate' , 'site-editor' ) ,
            'install-selected'    => __('Install' , 'site-editor' ) ,

        );
        return $actions;
    }


    function process_bulk_action() {

    }

	public function display_rows() {
		global $status , $sed_pb_modules;

		foreach ( $this->items as $module_file => $module_data )
			$this->single_row( array( $module_file, $module_data ) );
	}

    function single_row( $item ) {
		global $status, $page, $s, $totals, $sed_pb_modules;

		list( $module_file, $module_data ) = $item;
		$context = $status;
		$screen = $this->screen;

		// Pre-order.
		$actions = array(
			'deactivate' => '',
			'activate' => '',
			'details' => '',
			'edit' => '',
            'skins'  => ''
		);

        $is_complete_install = $sed_pb_modules->is_install( $module_file );
        $is_active = $sed_pb_modules->is_module_active( $module_file );

        if( $is_complete_install ){
            if ( $is_active ) {
            	$actions['deactivate'] = '<a href="' . wp_nonce_url('admin.php?page=site_editor_module&amp;action=deactivate&amp;module=' . $module_file . '&amp;show_modules=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'sed-deactivate-module_' . $module_file) . '" title="' . esc_attr__('Deactivate this module') . '">' . __('Deactivate') . '</a>';
            } else {
            	$actions['activate'] = '<a href="' . wp_nonce_url('admin.php?page=site_editor_module&amp;action=activate&amp;module=' . $module_file . '&amp;show_modules=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'sed-activate-module_' . $module_file) . '" title="' . esc_attr__('Activate this module') . '" class="edit">' . __('Activate') . '</a>';

            } // end if $is_active
        }else{
            	$actions['activate'] = '<a href="' . wp_nonce_url('admin.php?page=site_editor_module&amp;action=install&amp;module=' . $module_file . '&amp;show_modules=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'sed-install-module_' . $module_file) . '" title="' . esc_attr__('Complete Install this module') . '" class="edit">' . __('Complete Install') . '</a>';
        }

    	if ( ( ! is_multisite() || $screen->in_admin( 'network' ) ) && current_user_can('sed_edit_less') && is_writable( WP_CONTENT_DIR . '/' . $module_file) ){
            $href = wp_nonce_url('admin.php?page=site_editor_edit_module&amp;module=' . $module_file  , 'sed-edit-module_' . $module_file);
            $actions ['edit'] = sprintf('<a href="%1$s">%2$s</a>', $href ,__( 'Less Edit' , 'site-editor')) ;
        }

        $url_skin = 'admin.php?page=site_editor_skin&module=' . $module_file;
        $actions ['skins'] =  sprintf('<a href="%1$s">%2$s</a>', wp_nonce_url( self_admin_url(  $url_skin ) , 'sed-module-skin_' . $module_file) ,__( 'Skins' , 'site-editor') );

		$actions = apply_filters( 'sed_module_action_links', array_filter( $actions ), $module_file, $module_data, $context );

        $actions = apply_filters( "sed_module_action_links_$module_file", $actions, $module_file, $module_data, $context );

        $class = 'module-list-item alternate ';

		$class .= $is_active ? 'active' : 'inactive';
		$checkbox_id =  "checkbox_" . md5($module_data['Name']);

    	$checkbox = "<label class='screen-reader-text' for='" . $checkbox_id . "' >" . sprintf( __( 'Select %s' ), $module_data['Name'] ) . "</label>"
    		. "<input type='checkbox' name='checked[]' value='" . esc_attr( $module_file ) . "' id='" . $checkbox_id . "' />";


    	$description = '<p>' . ( $module_data['Description'] ? $module_data['Description'] : '&nbsp;' ) . '</p>';
    	$module_name = $module_data['Name'];

		$id = sanitize_title( $module_name );

		$module_slug = ( isset( $module_data['slug'] ) ) ? $module_data['slug'] : '';
		printf( "<tr id='%s' class='%s' data-slug='%s'>",
			$id,
			$class,
			$module_slug
		);

		list( $columns, $hidden , $sortable ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			switch ( $column_name ) {
				case 'cb':
					echo "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'name':
					echo "<td class='module-title'$style><strong>$module_name</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					echo "<td class='column-description desc'$style>
						<div class='module-description'>$description</div>
						<div class='$class second module-version-author-uri'>";

					$module_meta = array();

					if ( !empty( $module_data['Author'] ) ) {
						$author = $module_data['Author'];
						if ( !empty( $module_data['AuthorURI'] ) )
							$author = '<a href="' . $module_data['AuthorURI'] . '">' . $module_data['Author'] . '</a>';
						$module_meta[] = sprintf( __( 'By %s' ), $author );
					}

					// Details link using API info, if available
					if ( isset( $module_data['slug'] ) && current_user_can( 'install_modules' ) ) {
						$module_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
							esc_url( network_admin_url( 'module-install.php?tab=module-information&module=' . $module_data['slug'] .
								'&TB_iframe=true&width=600&height=550' ) ),
							esc_attr( sprintf( __( 'More information about %s' ), $module_name ) ),
							esc_attr( $module_name ),
							__( 'View details' )
						);
					} elseif ( ! empty( $module_data['ModuleURI'] ) ) {
						$module_meta[] = sprintf( '<a href="%s">%s</a>',
							esc_url( $module_data['ModuleURI'] ),
							__( 'Visit module site' )
						);
					}

					/**
					 * Filter the array of row meta for each module in the Plugins list table.
					 *
					 * @since 2.8.0
					 *
					 * @param array  $module_meta An array of the module's metadata,
					 *                            including the version, author,
					 *                            author URI, and module URI.
					 * @param string $module_file Path to the module file, relative to the modules directory.
					 * @param array  $module_data An array of module data.
					 * @param string $status      Status of the module. Defaults are 'All', 'Active',
					 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
					 *                            'Drop-ins', 'Search'.
					 */
					$module_meta = apply_filters( 'module_row_meta', $module_meta, $module_file, $module_data, $status );
					echo implode( ' | ', $module_meta );

					echo "</div></td>";
					break;
				default:
					echo "<td class='$column_name column-$column_name'$style>";

					/**
					 * Fires inside each custom column of the Plugins list table.
					 *
					 * @since 3.1.0
					 *
					 * @param string $column_name Name of the column.
					 * @param string $module_file Path to the module file.
					 * @param array  $module_data An array of module data.
					 */
					do_action( 'manage_modules_custom_column', $column_name, $module_file, $module_data );
					echo "</td>";
			}
		}

		echo "</tr>";

		/**
		 * Fires after each row in the Plugins list table.
		 *
		 * @since 2.3.0
		 *
		 * @param string $module_file Path to the module file, relative to the modules directory.
		 * @param array  $module_data An array of module data.
		 * @param string $status      Status of the module. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( 'sed_after_module_row', $module_file, $module_data, $status );

		/**
		 * Fires after each specific row in the Plugins list table.
		 *
		 * The dynamic portion of the hook name, `$module_file`, refers to the path
		 * to the module file, relative to the modules directory.
		 *
		 * @since 2.7.0
		 *
		 * @param string $module_file Path to the module file, relative to the modules directory.
		 * @param array  $module_data An array of module data.
		 * @param string $status      Status of the module. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */

        if( !$is_complete_install ){
            printf('<tr class="module-incomplete-tr">
                <td colspan="3" class="module-incomplete colspanchange">
                <div class="incomplete-message">%s</div></td></tr>', __("Modules is incompletely installed." ) );
        }

		do_action( "sed_after_module_row_$module_file", $module_file, $module_data, $status );

    }

    /*function single_row_columns($item){
        return array();

    }*/


}