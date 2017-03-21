<script type="text/html" id="layouts-scope-settings-button-tpl" >
    <div class="row_settings">
        <div class="row_setting_inner sed-app-container-control sed-app-container-control-panel-button spacing_sm">
            <div id='sed-scope-settings-<?php echo $control_id; ?>'  class="clearfix">
                <button data-related-level-box="dialog_page_box_<?php echo $control_id; ?>" class="sed-btn-menu sed-btn-default sed_go_to_scope_settings"  name="sed_pb_<?php echo $control_id; ?>" id="sed_pb_<?php echo $control_id; ?>" >
                    <span class="sedico fa-lg sedico-site-custom-css"></span>
                    <?php echo __('Go To Scope Settings' , 'site-editor') ?><span class="sedico sedico-chevron-right sed-arrow-right sedico-lg"></span>
                </button>
                <div id="dialog_page_box_<?php echo $control_id; ?>" class=""  data-title="<?php echo __('Scope Settings' , 'site-editor') ?>" data-multi-level-box="true">

                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="layouts-scope-settings-content-tpl" >
    <div class="<?php echo $control_id; ?>_settings_container">

       <div class="sed-scope-mode-label bg-primary"><span><?php echo __('Scope' , 'site-editor') ?>: </span><span class="scope-mode"><?php echo __('Private' , 'site-editor') ?></span></div>
       <span class="field_desc flt-help sedico sedico-question sedico-lg " title=""></span>
       <fieldset class="row_setting_box">
       <legend id="sed_layout_scope_settings_panel_title"><?php echo __("Select Scope","site-editor");?></legend>
        <div class="row_settings">
          <div class="row_setting_inner">
            <div id="sed-app-control-<?php echo $control_id; ?>" class="clearfix sed-container-control-element">
                <ul>

                    <li>
                        <div id="sed_theme_custom_row_type_container">
                            <label for="sed_theme_custom_row_type"><?php echo __( "Select Row Type : " , "site-editor" );?></label>
                            <select name="sed_theme_custom_row_type" id="sed_theme_custom_row_type" >
                                <option value="after"><?php echo __( "After" , "site-editor" );?></option>
                                <option value="before"><?php echo __( "Before" , "site-editor" );?></option>
                                <option value="start"><?php echo __( "Start" , "site-editor" );?></option>
                                <option value="end"><?php echo __( "End" , "site-editor" );?></option>
                            </select>

                        </div>
                    </li>

                    <li class="scope-settings-action sed-bp-form-checkbox" >
                        <div class="sed-bp-form-radio-item">
                            <label>
                                <input type="checkbox" name="sed_layout_scope_public" class="sed-settings-theme-type sed-element-control sed-bp-input sed-bp-checkbox-input" value="public">
                                <?php echo __("Public","site-editor");?>
                            </label>
                        </div>

                        <ul class="select-pubic-scope hide">

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

<script type="text/html" id="manage-layout-theme-rows-page-box-tpl" >

    <?php $action_page_box_id = "manage_layout_theme_rows"; ?>
    <div id="dialog_page_box_<?php echo $action_page_box_id; ?>" class=""  data-title="<?php echo __('Manage Layout Rows' , 'site-editor') ?>" data-multi-level-box="true">
        <div class="sed-dialog-page-box-inner">

        </div>
    </div>

</script>

<script type="text/html" id="change-public-to-private-confirm-tpl" >
    <div class="sed_message_box">
         <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
         <p><?php echo __("if you continue this action , this module removed from all pages and layout it" , "site-editor");?></p>
    </div>
</script>

<script type="text/html" id="change-customize-to-public-confirm-tpl" >
    <div class="sed_message_box">
        <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
        <p><?php echo __("if you continue this action with first option, current row(module) back to public data mode and lost your customize data , but by select second option current customize data apply on all page include this row and lost allredy public data " , "site-editor");?></p>
        <div class="select-customize-to-public-data-mode">
            <label for="change-customize-to-public-using-public-data-mode">
                <input type="radio" name="change-customize-to-public-mode" id="change-customize-to-public-using-public-data-mode" value="using_public_data" checked="checked">
                <?php echo __("back current row(module) to public data mode" , "site-editor");?>
            </label>

            <label for="change-customize-to-public-using-customize-data-mode">
                <input type="radio" name="change-customize-to-public-mode" id="change-customize-to-public-using-customize-data-mode" value="using_customize_data">
                <?php echo __("using customize data as public data mode" , "site-editor");?>
            </label>

        </div>
    </div>
</script>

<script type="text/html" id="change-customize-to-hidden-confirm-tpl" >
    <div class="sed_message_box">
        <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
        <p><?php echo __("if you continue this action with first option, current row(module) back to public data mode and lost your customize data , but by select second option current customize data apply on all page include this row and lost allredy public data " , "site-editor");?></p>
        <div class="select-customize-to-public-data-mode">
            <label for="change-customize-to-public-using-public-data-mode">
                <input type="radio" name="change-customize-to-public-mode" id="change-customize-to-public-using-public-data-mode" value="using_public_data" checked="checked">
                <?php echo __("back current row(module) to public data mode" , "site-editor");?>
            </label>

            <label for="change-customize-to-public-using-customize-data-mode">
                <input type="radio" name="change-customize-to-public-mode" id="change-customize-to-public-using-customize-data-mode" value="using_customize_data">
                <?php echo __("using customize data as public data mode" , "site-editor");?>
            </label>

        </div>
    </div>
</script>


<script type="text/html" id="destroy-sort-theme-row-confirm-tpl" >
    <div class="sed_message_box">
        <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
        <p><?php echo __("if you continue this action current public module removed from all pages and lost data" , "site-editor");?></p>
    </div>
</script>



<script type="text/html" id="sed-remove-module-confirm-tpl" >
    <div class="sed_message_box">
        <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
        <p class="text-danger"><?php echo __("Do you want to delete this element?" , "site-editor");?></p>
        <p><strong><?php echo __("Note : " , "site-editor");?></strong> <span><?php echo __("if this Row is a public row it is remove from all releted layouts and pages" , "site-editor");?></span> </p>
    </div>
</script>


<script type="text/html" id="tmpl-sed-layout-edit-rows" >

    <div class="sed-layout-row-error-box sed-error">
        <p></p>
    </div>

    <ul class="layout-row-container">
        <#
        layoutRows = _.sortBy( data.layoutRows , 'order');

        _.each( layoutRows , function( row ){
            var title = row.title || data.noTitle ,
                id = row.theme_id ,
                className = ( data.currThemeId == row.theme_id ) ? "current-row" : "";
        #>
            <li data-row-id="{{id}}" class="sed-layout-row-box {{className}}">
                <label class="row-title-label" title="{{title}}">{{title}}</label>
                <div class="layout-row-actions">
                    <span data-action="edit" class="edit action"><span class="fa fa-pencil fa-lg"></span></span>
                    <span data-action="sort" class="sort action"><span class="fa fa-arrows fa-lg"></span></span>
                </div>
                <input class="layout-row-title-edit" type="text" value="{{title}}" />
            </li>
        <#
        });
        #>
    </ul>
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
        <?php $action_page_box_id = "manage_layout_theme_rows"; ?>
        <a href="javascript:void(0);" data-layout="{{layout}}" data-related-level-box="dialog_page_box_<?php echo $action_page_box_id; ?>" class="edit-layout-rows hide field_desc"  title="<?php echo __('Manage Layout Rows' , 'site-editor') ?>">
            <span class="icon icon-edit fa fa-edit"></span>
        </a>
    </li>
    <#
        num++;
    });
    #>
</script>



<script type="text/html" id="tmpl-sed-layouts-select-options" >
    <#
        if( data.hasEmpty === true ){ 
        #>
            <option value=""><?php echo __( "Using Default settings" , "site-editor" );?></option>
        <#
        }

        var num = 1;
        _.each( data.layoutsSettings , function( setting , layout ){
        var title = setting.title;
        #>
            <option value="{{layout}}">{{title}}</option>
        <#
        num++;
        });
    #>
</script>