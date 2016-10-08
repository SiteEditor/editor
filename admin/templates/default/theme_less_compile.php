<?php
if( isset( $_REQUEST['theme_less_file'] ) ) {

    $theme_less_path = get_template_directory() . DS . "less/";

    $less_file_base = $_REQUEST['theme_less_file'];

    $css_file_base = substr($less_file_base, 0, -4) . 'css';

    $less_file = $theme_less_path . $less_file_base;

    $compiled_path = SED_UPLOAD_PATH . '/theme/' . $css_file_base;

    if( !class_exists( 'SEDAppLess' ) )
        require_once SED_INC_DIR . DS . 'sed_app_less.class.php';

    $result_compile = SEDAppLess::compile_file($less_file, $compiled_path);

    if( $result_compile === true ){
        sed_print_message( sprintf( __("less %s is compiled.","site-editor" ) , $less_file_base ) );
    }

    if( $result_compile !== true ) {
        sed_print_message(sprintf(__("Error LESS : %s", "site-editor"), $result_compile), 'error');
    }

}
?>

<div id="" class="sed_admin_pages_edit_links">
    <div id="" class="sed_admin_item_setting">

        <?php
        if( !class_exists( 'SEDFile' ) )
            require_once SED_INC_DIR . DS . 'app_file.class.php';

        $theme_less_path = get_template_directory() . DS . "less";

        $theme_less_path = str_replace( DS , '/' , $theme_less_path);

        $less_files     = SEDFile::list_files( $theme_less_path , '' , '' , array( 'less' ) );

        ?>

        <select name="theme_less_file" class="theme-less-files">
            <?php
            foreach ( $less_files as $file ) {

                $data_file = SEDFile::get_file_data( $file , "less_info" );

                if( $data_file !== false && $data_file['handle'] ) {

                    $rel_file = str_replace(DS, '/', $file);

                    $rel_file = str_replace($theme_less_path . '/', "", $rel_file);

                    ?>
                    <option value="<?php echo $rel_file; ?>"> <?php echo esc_html($rel_file); ?> </option>
                    <?php
                }

            }
            ?>
        </select>

        <?php
        ?>

    </div>
</div>