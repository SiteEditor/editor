<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package SiteEditor
 * @subpackage defualt
 */


global $wp_scripts;
?>

</div>

<script>
 var _wpRegisteredScripts  = <?php echo wp_json_encode( $wp_scripts->registered )?>;
</script>

<!--</form> -->
<?php

/**
 * Print templates, control scripts, and settings in the footer.
 *
 * @since 3.4.0
 */

do_action( 'sed_footer' );
echo $site_editor_footer;

do_action( 'sed_print_footer_scripts' );
?>
</body>
</html>