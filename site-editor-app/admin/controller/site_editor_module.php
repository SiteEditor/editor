<?php

Class site_editor_moduleController Extends baseController {

    public function index() {
        global $sed_error, $sed_pb_modules ;

        //ini_set('xdebug.var_display_max_children',1000 );
        //ini_set('xdebug.var_display_max_depth',20 );
        //ini_set('xdebug.var_display_max_data' , 100000 );
        //var_dump( get_option('site-editor-settings') );
        //var_dump( get_option('active_plugins') );

        do_action( "load-sed-module-page" );

        if(!class_exists('SiteEditorModuleListTable')){
            require_once( SED_ADMIN_INC_PATH . DS . 'sed-module-list-table.class.php' );
        }

        $wp_list_table = new SiteEditorModuleListTable( array( 'screen' => get_current_screen() ) );
        $pagenum = $wp_list_table->get_pagenum();
        $s = isset($_REQUEST['s']) ? urlencode($_REQUEST['s']) : '';

        global $page , $status;
        $action = $wp_list_table->current_action();
        $live_module = sed_get_setting( "live_module" );
        //var_dump( $live_module );

        if( $action ){

            $module = isset( $_REQUEST['module'] ) ? $_REQUEST['module'] : "";

            switch ( $action ) {
                case 'activate':
        			if ( ! current_user_can('activate_modules') )
        				wp_die(__('You do not have sufficient permissions to activate modules for this site.' , 'site-editor'));

        			check_admin_referer('sed-activate-module_' . $module);

          			$result = $sed_pb_modules->activate_module($module, self_admin_url('admin.php?page=site_editor_module&error=true&module=' . $module) );
          			if ( is_wp_error( $result ) ) {
          			    wp_die($result);
          			}

				    wp_redirect( self_admin_url("admin.php?page=site_editor_module&activate=true&show_modules=$status&paged=$page&s=$s") ); // overrides the ?error=true one above
			        exit;

                break;

                case 'activate-selected':
        			if ( ! current_user_can('activate_modules') )
        				wp_die(__('You do not have sufficient permissions to activate modules for this site.'));

        			//check_admin_referer('bulk-plugins');

        			$modules = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

                    foreach ( $modules as $i => $module ) {
    					// Only activate plugins which are not already active and are not network-only when on Multisite.
    					if ( $sed_pb_modules->is_module_active( $module ) ) {
    						unset( $modules[ $i ] );
    					}
    				}

                    foreach ( $modules as $module ){
                        $result = $sed_pb_modules->activate_module( $module );
            			if ( is_wp_error( $result ) ) {
            			    wp_die($result);
            			}
                    }

				    wp_redirect( self_admin_url("admin.php?page=site_editor_module&activate=true&show_modules=$status&paged=$page&s=$s") ); // overrides the ?error=true one above
			        exit;

                break;

                case 'deactivate':

        			if ( ! current_user_can('deactivate_modules') )
        				wp_die(__('You do not have sufficient permissions to deactivate modules for this site.' , 'site-editor'));

        			check_admin_referer('sed-deactivate-module_' . $module);

          			$result = $sed_pb_modules->deactivate_module($module, self_admin_url('admin.php?page=site_editor_module&error=true&module=' . $module) );
          			if ( is_wp_error( $result ) ) {
          			    wp_die($result);
          			}

				    wp_redirect( self_admin_url("admin.php?page=site_editor_module&deactivate=true&show_modules=$status&paged=$page&s=$s") ); // overrides the ?error=true one above
			        exit;

                break;

                case 'deactivate-selected':

        			if ( ! current_user_can('deactivate_modules') )
        				wp_die(__('You do not have sufficient permissions to deactivate modules for this site.' , 'site-editor'));

        			$modules = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

                    foreach ( $modules as $i => $module ) {
    					// Only activate plugins which are not already active and are not network-only when on Multisite.
    					if ( !$sed_pb_modules->is_module_active( $module ) ) {
    						unset( $modules[ $i ] );
    					}
    				}

                    foreach ( $modules as $module ){
                        $result = $sed_pb_modules->deactivate_module( $module );
            			if ( is_wp_error( $result ) ) {
            			    wp_die($result);
            			}
                    }

				    wp_redirect( self_admin_url("admin.php?page=site_editor_module&deactivate=true&show_modules=$status&paged=$page&s=$s") ); // overrides the ?error=true one above
			        exit;

                break;

                case 'install':

                    if ( !current_user_can('install_modules') )
                        wp_die( __('You do not have sufficient permissions to install modules for this site.'),'site-editor');

                    $modules = ( isset( $_REQUEST['module'] ) && !empty( $_REQUEST['module'] ) ) ? array( $_REQUEST['module'] ) : array();

                    $this->registry->template->modules   = $modules;
                    $this->registry->template->sed_pb_modules   = $sed_pb_modules;
                    $this->registry->template->show('module/install');

                    exit;
                    //$install_url = add_query_arg( array("action" => "install" , "module" => $modules) , $this->url_module_home ) ;
                    //wp_redirect( self_admin_url( $install_url ) );
                break;

                case 'install-selected':


                    if ( !current_user_can('install_modules') )
                        wp_die( __('You do not have sufficient permissions to install modules for this site.'),'site-editor');

                    $modules = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

                    $this->registry->template->modules   = $modules;
                    $this->registry->template->sed_pb_modules   = $sed_pb_modules;
                    $this->registry->template->show('module/install');

                    exit;

                break;
            }
        }

        $this->registry->template->status   = $status;
        $this->registry->template->s        = $s;
        $this->registry->template->paged    = $page;
        $this->registry->template->table    = $wp_list_table;
        $this->registry->template->show('module/index');
    }

}