<script type="text/html" id="layouts-scope-settings-button-tpl" >
    <div class="row_settings">
        <div class="row_setting_inner row_setting_box">
            <div id='sed-scope-settings-<?php echo $control_id; ?>'  class="clearfix">
                <div class="scope-mode-box"><span><?php echo __('Scope' , 'site-editor') ?> :</span><span><?php echo __('Private' , 'site-editor') ?></span></div>
                <span class="field_desc flt-help fa f-sed icon-question  fa-lg " title=""></span>
                <button data-related-level-box="dialog_page_box_<?php echo $control_id; ?>" class="sed-btn-default sed_go_to_scope_settings"  name="sed_pb_<?php echo $control_id; ?>" id="sed_pb_<?php echo $control_id; ?>" >
                    <?php echo __('Go To Scope Settings' , 'site-editor') ?><span class="fa f-sed icon-chevron-right sed-arrow-right fa-lg"></span>
                </button>
                <div id="dialog_page_box_<?php echo $control_id; ?>" class=""  data-title="<?php echo __('Scope Settings' , 'site-editor') ?>" data-multi-level-box="true">

                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="layouts-scope-settings-content-tpl" >
    <div class="<?php echo $control_id; ?>_settings_container">

       <fieldset class="row_setting_box">
       <legend id="sed_layout_scope_settings_panel_title"><?php echo __("Select Scope","site-editor");?></legend>
        <div class="row_settings">
          <div class="row_setting_inner">
            <div id="sed-app-control-<?php echo $control_id; ?>" class="clearfix sed-container-control-element">
                <ul>
                    <li class="scope-settings-action sed-bp-form-checkbox" >
                        <div class="sed-bp-form-radio-item">
                            <label>
                                <input type="checkbox" name="sed_layout_scope_public" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-checkbox-input" value="public">
                                <?php echo __("Public","site-editor");?>
                            </label>
                        </div>

                        <ul class="select-pubic-scope hide">

                            <li class="sed-bp-form-text" >
                                <div class="sed-bp-form-text-item">
                                    <label><?php echo __("Row Name","site-editor");?>  </label>
                                    <input type="text" name="sed_layout_row_title" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-text-input" value="">
                                </div>
                            </li>

                            <li class="scope-settings-action sed-bp-form-radio" >
                                <div class="sed-bp-form-radio-item">
                                <label>
                                    <input type="radio" name="sed_layout_public_type" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-radio-input" value="normal">
                                    <?php echo __("select layout","site-editor");?>
                                </label>
                                </div>

                                <ul class="select-layouts-custom hide">
                                    <li class="sed-all-sub-themes item">
                                        <label for="sed_sub_theme_check_0" class="sed-all-sub-themes-check-box" >
                                            <input type="checkbox" id="sed_sub_theme_check_0" name="sed_scope_all_layout" value="all" class="">
                                            <span class="sub_theme_title"><?php echo __("Show On All Layout","site-editor");?></span>
                                        </label>
                                    </li>

                                </ul>

                            </li>


                            <li class="scope-settings-action sed-bp-form-radio" >
                                <div class="sed-bp-form-radio-item">
                                <label>
                                    <input type="radio" name="sed_layout_public_type" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-radio-input" value="customize">
                                    <?php echo __("Public but customize in current page","site-editor");?>
                                </label>
                                </div>
                            </li>

                            <li class="scope-settings-action sed-bp-form-radio" >
                                <div class="sed-bp-form-radio-item">
                                <label>
                                    <input type="radio" name="sed_layout_public_type" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-radio-input" value="hidden">
                                    <?php echo __("Public but hidden in current page","site-editor");?>
                                </label>
                                </div>
                            </li>

                        </ul>
                    </li>

                </ul>

            </div>
          </div>
        </div>
       </fieldset>

    </div>
</script>

<div id="sed-edit-layout-rows-dialog" title="<?php echo __("Edit Layout Rows" , "site-editor");?>"></div>

<script type="text/html" id="change-public-to-private-confirm-tpl" >
    <div class="sed_message_box">
         <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
         <p><?php echo __("if you continue this action , this module removed from all pages and layout it" , "site-editor");?></p>
    </div>
</script>

<script type="text/html" id="tmpl-sed-layout-edit-rows" >
    <div class="layout-row-container">
        <#
        layoutRows = _.sortBy( data.layoutRows , 'order');

        _.each( layoutRows , function( row ){
            var title = row.title || data.noTitle ,
                id = layout.theme_id ,
                className = ( data.currThemeId == row.theme_id ) ? "current-row" : "";
        #>
            <div data-row-id="{{id}}" class="sed-layout-row-box {{className}}">{{title}}</div>
        <#
        });
        #>
    <div>
</script>

<div id="sed-confirm-message-dialog" title="<?php echo __("Confirm Message" , "site-editor");?>">

</div>

<script type="text/html" id="tmpl-sed-all-layouts-checkbox-scope" >
    <#
    var num = 1;
    _.each( data.layoutsSettings , function( setting , layout ){
        var title = setting.title;
    #>
    <li class="sub-theme-item item">
        <label for="sed_sub_theme_check_{{num}}" class="sed-sub-themes-check-box" data-sub-theme-name="{{layout}}">
            <input type="checkbox" id="sed_sub_theme_check_{{num}}" name="sed_scope_layout" value="{{layout}}" class="">
            <span class="sub_theme_title">{{title}}</span>
        </label>
        <a href="javascript:void(0);" data-layout-name="{{layout}}" class="edit-layout-rows hide">
            <span class="icon icon-edit"></span>
        </a>
    </li>
    <#
        num++;
    });
    #>
</script>