<div class="clearfix sed-container-control-element">
    <div class="row_setting_box">
        <div class="row_settings">
            <div class="row_setting_inner">

                <div class="sed-module-preset-settings">

                    <div class="sed-preset-error-box sed-error">
                        <p></p>
                    </div>

                    <div class="sed-add-preset">

                        <div class="row_field">
                            <label><?php echo esc_html__('Preset Title' , 'site-editor');?></label>
                            <input class="sed-new-preset" type="text" placeholder="<?php echo esc_attr__('Save As New Preset...' , 'site-editor');?>">
                            <button data-action="add" class="btn button-primary"><?php echo esc_html__('Add Preset' , 'site-editor');?></button>
                            <div class="sed-add-preset-loading sed-loading-small-continer" >
                                <div class="sed-loading"></div>
                            </div>
                        </div>

                        <div class="row_field">
                        <br>
                            <label><?php echo esc_html__('Save As Exist Preset' , 'site-editor');?></label>
                            <select class="sed-presets-list-select">
                                <option value=""><?php echo esc_html__('Select Preset' , 'site-editor');?></option>
                            </select>
                            <button data-action="override" class="btn button-primary"><?php echo esc_html__('Save Preset' , 'site-editor');?></button>
                            <div class="sed-override-preset-loading sed-loading-small-continer" >
                                <div class="sed-loading"></div>  
                            </div> 
                        </div>

                    </div>

                    <div class="sed-presets-list-container">
                        <ul class="sed-presets-list">

                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="sed-preset-settings-loading sed-loading-small-continer" >
        <div class="sed-loading" >

        </div>
    </div>
</div>    