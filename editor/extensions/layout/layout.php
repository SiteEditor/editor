<?php
/*
Module Name: Content
Module URI: http://www.siteeditor.org/modules/content
Description: Module Content For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

require_once SED_EXT_PATH . "/layout/includes/site-editor-layout.php";

global $site_editor_app;

$layout = new SiteEditorLayoutManager();

$site_editor_app->layout = $layout;

$site_editor_app->layout_patterns = array(

    //sed_main_content_row && sed_main_content attr for sub_theme module
    "default" => '[sed_row_outer_outer sed_main_content_row="true" shortcode_tag="sed_row" shortcode_tag="sed_row" type="static-element" length="boxed"]
            [sed_module_outer_outer shortcode_tag="sed_module"]
                [sed_columns_outer have_helper_id="true" pb_columns="2" shortcode_tag="sed_columns" class="" title="columns"]
                    [sed_column_outer  width="71%" shortcode_tag="sed_column" parent_module="columns"]
                       [sed_row_outer shortcode_tag="sed_row" type="static-element" sed_main_content = "true" ]
                          [sed_module_outer shortcode_tag="sed_module"]
                            {{content}}
                          [/sed_module_outer]
                       [/sed_row_outer]
                    [/sed_column_outer]
                    [sed_column width="29%" parent_module="columns"]

                    [/sed_column]
                [/sed_columns_outer]

                [sed_add_item_pattern is_helper_id="true" parent_module="columns"]
                    [sed_column parent_module="columns"][/sed_column]
                [/sed_add_item_pattern]
            [/sed_module_outer_outer]
        [/sed_row_outer_outer]'
);


//add_action( "sed_footer" , "print_layout_patterns" );
function print_layout_patterns(){
  ?>
    <script type="text/javascript">
        var _sedAppLayoutPatterns = <?php echo wp_json_encode( $site_editor_app->layout_patterns )?>;
    </script>
  <?php
}