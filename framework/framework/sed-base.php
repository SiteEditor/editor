<?php
/**
 * Basic page handler
 *
 * @version   5.0.0
 * @author    PageLines
 */
if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}
sed_get_header();

do_action( 'sed_start_template' );

include sed_template_path();

do_action( 'sed_after_template' );


sed_get_footer();
