<?php
require_once SED_PLUGIN_DIR . DS . 'framework' . DS . 'framework' . DS . 'typography.class.php';

$typography = new SiteeditorTypography();

$typography->set_fonts();

$google_fonts   = $typography->google_fonts;

$standard_fonts = $typography->standard_fonts;

$custom_fonts   = $typography->custom_fonts;

$selected_empty = "";
if( $value == "" )
    $selected_empty = "selected='selected'";

$options = "<option {$selected_empty} value=''>".__("Select Font Family")."</option>";

if( !empty( $custom_fonts ) ){
    $options .='<optgroup label="'.__("Custom Fonts" , "site-editor").'">';
    foreach($custom_fonts as $key => $val) {
        if( $value == $key )
            $selected =  "selected='selected'";
        else
            $selected = "";

        $options .= "<option {$selected} value='{$key}'>{$val}</option>";
    }
    $options .='</optgroup>';
}

$options .='<optgroup label="'.__("Standard Fonts" , "site-editor").'">';
foreach($standard_fonts as $key => $val) {
    if( $value == $key )
        $selected =  "selected='selected'";
    else
        $selected = "";

    $options .= "<option {$selected} value='{$key}'>{$val}</option>";
}
$options .='</optgroup>';

$options .='<optgroup label="'.__("Google Fonts" , "site-editor").'">';
foreach($google_fonts as $key => $val) {
    if( $value == $key )
        $selected =  "selected='selected'";
    else
        $selected = "";

    $options .= "<option {$selected} value='{$key}'>{$val}</option>";
}
$options .='</optgroup>';

?>
<select name="<?php echo $id;?>" id="<?php echo $id;?>">
<?php echo $options; ?>
</select>