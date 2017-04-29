<?php
/**
 * SiteEditor Uninstall
 *
 * Uninstalling SiteEditor deletes user roles, tables, and options.
 *
 * @author      SiteEditor Team
 * @category    Core
 * @package     SiteEditor/Uninstaller
 * @version     9.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

wp_clear_scheduled_hook( 'sed_tracker_send_event' );
