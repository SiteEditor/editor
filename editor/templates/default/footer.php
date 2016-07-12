<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package Zmind
 * @subpackage defualt
 */
?>

<?php
 do_action( 'customize_controls_print_footer_scripts' );

 do_action('sed_widgets_scripts', $site_editor_app);
 global $wp_scripts;
 //var_dump( $wp_scripts->registered  );
?>
<script>
var _wpRegisteredScripts  = <?php echo wp_json_encode( $wp_scripts->registered )?>;
var _sedAppWidgetScripts  = <?php echo wp_json_encode( $site_editor_app->widget->scripts )?>;
</script>
<?php
 do_action( 'sed_footer' );
 echo $site_editor_footer;
?>

</div>


<!--</form> -->
<?php

/**
 * Print templates, control scripts, and settings in the footer.
 *
 * @since 3.4.0
 */
do_action( 'sed_print_footer_scripts' );
?>
</body>
</html>