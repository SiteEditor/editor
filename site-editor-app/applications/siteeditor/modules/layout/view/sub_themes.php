<div class="layout-sub-themes-settings">
    <a href="javascript:void(0)" class="btn btn-default3"  title="<?php echo __("Select Sub Theme","site-editor");  ?>" id="page_layout" role="button" >
        <span class="fa f-sed icon-subtheme fa-2x "></span>
        <span class="el_txt"><?php echo __("Sub Themes","site-editor");  ?></span>
    </a>
    <div id="sed-dialog-sub-themes-settings"  class="sed-dialog"  title="<?php echo __("Sub Themes" ,"site-editor");  ?>">

<?php
$sed_sub_themes = array();
$sed_sub_themes = apply_filters( "sed_sub_themes" , $sed_sub_themes );
$sub_themes = array();
foreach( $sed_sub_themes AS $name => $options ){
    $sub_themes[$name] = $options['title'];
}

$settings = array(
    'page_layout' => array(
        'type'      => 'select',
        'value'     => 'default' ,
        'label'     => __("Select Sub Theme" ,"site-editor"),
        'desc'      => '',
        'options'   => $sub_themes,
        'priority'  => 15
    ),
    'changed_sub_theme_override' => array(
        'type'      => 'checkbox',
        'value'     => true ,
        'label'     => __("Main Content Row Data Override With Main Content Row in New Sub Theme" ,"site-editor"),
        'desc'      => '',
        'options'   => $sub_themes,
        'priority'  => 15
    ),
);

$cr_settings = ModuleSettings::create_settings($settings , array());

echo $cr_settings;

?>

    </div>
</div>