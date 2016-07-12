<?php

$shortcode_pattern = '[sed_text_icon contextmenu_disabled = "disabled" settings_disabled = "disabled" skin="skin4"]

    [sed_text_icon_item contextmenu_disabled = "disabled" settings_disabled = "disabled" parent_module="text-icon" parent_skin = "skin4"]
      [sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" src="{{@sed_module_url}}/images/1-1.png"  parent_module="text-icon"][/sed_image]
      [sed_text_title contextmenu_disabled = "disabled" settings_disabled = "disabled" tag="div" toolbar1="normal-text" toolbar2="normal-text" parent_module="text-icon"]<h5>'.__("7 day return guarantee","site-editor").'</h5>[/sed_text_title]
    [/sed_text_icon_item]

    [sed_text_icon_item contextmenu_disabled = "disabled" settings_disabled = "disabled"  parent_module="text-icon" parent_skin = "skin4"]
      [sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" src="{{@sed_module_url}}/images/2-2.png"  parent_module="text-icon"][/sed_image]
      [sed_text_title contextmenu_disabled = "disabled" settings_disabled = "disabled" tag="div" toolbar1="normal-text" toolbar2="normal-text" parent_module="text-icon"]<h5>'.__("Cash on delivery","site-editor").'</h5>[/sed_text_title]
    [/sed_text_icon_item]

    [sed_text_icon_item contextmenu_disabled = "disabled" settings_disabled = "disabled"  parent_module="text-icon" parent_skin = "skin4"]
      [sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" src="{{@sed_module_url}}/images/3-3.png"  parent_module="text-icon"][/sed_image]
      [sed_text_title contextmenu_disabled = "disabled" settings_disabled = "disabled" tag="div" toolbar1="normal-text" toolbar2="normal-text" parent_module="text-icon"]<h5>'.__("Express delivery","site-editor").'</h5>[/sed_text_title]
    [/sed_text_icon_item]

    [sed_text_icon_item contextmenu_disabled = "disabled" settings_disabled = "disabled"  parent_module="text-icon" parent_skin = "skin4"]
      [sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" src="{{@sed_module_url}}/images/4-4.png"  parent_module="text-icon"][/sed_image]
      [sed_text_title contextmenu_disabled = "disabled" settings_disabled = "disabled" tag="div" toolbar1="normal-text" toolbar2="normal-text" parent_module="text-icon"]<h5>'.__("Guarantee the authenticity of the product","site-editor").'</h5>[/sed_text_title]
    [/sed_text_icon_item]

    [sed_text_icon_item contextmenu_disabled = "disabled" settings_disabled = "disabled"  parent_module="text-icon" parent_skin = "skin4"]
      [sed_image contextmenu_disabled = "disabled" settings_disabled = "disabled" src="{{@sed_module_url}}/images/5-5.png"  parent_module="text-icon"][/sed_image]
      [sed_text_title contextmenu_disabled = "disabled" settings_disabled = "disabled" tag="div" toolbar1="normal-text" toolbar2="normal-text" parent_module="text-icon"]<h5>'.__("Guaranteed best price","site-editor").'</h5>[/sed_text_title]
    [/sed_text_icon_item]

[/sed_text_icon]';

$shortcode_pattern = str_replace("{{@sed_module_url}}/" , SED_PB_MODULES_URL . "text-icon/" , $shortcode_pattern );

echo do_shortcode( $shortcode_pattern );