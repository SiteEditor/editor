<div id="sed-app-control-sed_add_layout_layouts_manager" class="clearfix sed-container-control-element">
    <fieldset class="row_setting_box">
        <legend><?php echo __("Add New Layout","site-editor");?></legend>
        <div class="row_settings">
            <div class="row_setting_inner">

                <div>

                    <div class="sed-add-layout">
                        <div class="row_field">
                            <label><?php echo __("Title" , "site-editor");?></label>
                            <input name="add-new-layout-title" class="add-layout-input">
                        </div>

                        <div class="row_field">
                            <label><?php echo __("Slug" , "site-editor");?></label>
                            <input name="add-new-layout-slug" class="add-layout-input">
                        </div>

                        <div class="row_field">
                            <button data-action="add" class="btn button-primary"><?php echo __("Add" , "site-editor");?></button>
                        </div>
                    </div>

                    <div class="sed-layout-edit hide">
                        <input data-layout="" class="edit-title-input" name="edit-layout-title" value="">
                        <button data-action="save" class="btn button-primary"><?php echo __("Save" , "site-editor");?></button>
                        <span data-action="save-close" class="fa fa-close sed-layout-edit-close"> </span>
                    </div>

                    <div class="sed-layout-error-box sed-error">
                        <p></p>
                    </div>

                </div>

            </div>
        </div>
    </fieldset>

    <div class="sed-layout-lists-container">

        <div class="sed-layout-lists">
            <ul>

            </ul>
        </div>

    </div>
</div>

<?php $action_page_box_id = "manage_layout_theme_rows"; ?>
<div id="dialog_page_box_<?php echo $action_page_box_id; ?>" class=""  data-title="<?php echo __('Manage Layout Rows' , 'site-editor') ?>" data-multi-level-box="true">
    <div class="sed-dialog-page-box-inner">

    </div>
</div>

<?php
/*
 * && layout != data.currentLayout
 */
?>
<script type="text/html" id="tmpl-sed-layouts-manager" >
    <#
        var num = 1;
        var layoutsSettings = data.layoutsSettings ,
            layouts = _.keys( layoutsSettings );

        layouts.reverse();

        _.each( layouts , function( layout ){
            var layoutTitle = layoutsSettings[layout].title;
        #>
        <li>

            <div class="sed-view-mode">
                <span class="sed-view-mode-title" data-action="edit" data-layout-title="{{layoutTitle}}" data-layout="{{layout}}" >{{layoutTitle}} </span>

                <div class="sed-view-mode-actions">
                    <# if( layout != "default" ){ #>
                    <span data-action="delete" data-layout="{{layout}}" class="fa fa-trash" title="<?php echo __("Remove" , "site-editor");?>"></span>
                    <# } #>
                    <span data-action="edit" data-layout-title="{{layoutTitle}}" data-layout="{{layout}}"  class="fa fa-edit" title="<?php echo __("Edit" , "site-editor");?>"></span>

                    <span data-action="manage" data-layout="{{layout}}" data-related-level-box="dialog_page_box_<?php echo $action_page_box_id; ?>" class="fa fa-share sed_go_to_manage_layout_rows"  title="<?php echo __('Manage Layout Rows' , 'site-editor') ?>" ></span>
                </div>
            </div>

            <div class="sed-edit-mode">

            </div>

        </li>
        <#
            num++;
            });
            #>
</script>


<script type="text/html" id="sed-remove-layout-confirm-tpl" >
    <div class="sed_message_box">
        <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
        <p><?php echo __("Do you want to delete this layout?" , "site-editor");?></p>
        <p><span><?php echo __("Note : " , "site-editor");?></span> <span><?php echo __("all this data related to layout like module that only using in this layout removed && not recovery" , "site-editor");?></span> </p>
    </div>
</script>