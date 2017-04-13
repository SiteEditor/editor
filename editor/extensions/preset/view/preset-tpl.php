
<!-- Preset Templates -->

<script type="text/template" id="tmpl-sed-preset-item">
    <div>
        <div class="sed-preset-item">
            <label title="{{data.title}}">{{data.title}}</label>
            <div class="preset-actions">
                <span data-action="load" class="load-preset action"><span class="sedico sedico-eye sedico-lg"></span></span>
                <span data-action="edit" class="edit action"><span class="sedico sedico-pencil sedico-lg"></span></span>
                <span data-action="default" class="set-default action <# if(data.isDefault){ #>sed-default<# } #>"><span class="sedico sedico-star-sign sedico-lg"></span><span class="sedico sedico-little-black-star sedico-lg"></span></span>
                <span data-action="remove" class="trash destroy action"><span class="sedico sedico-trash-container sedico-lg"></span></span>
                <span data-action="sort" class="sort action"><span class="sedico sedico-cursor-move-a sedico-lg"></span></span>
            </div> 
        </div>
        <input class="preset-edit" type="text" value="{{data.title}}" />
        <div class="sed-edit-preset-loading sed-loading-small-continer" >
            <div class="sed-loading" >

            </div>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-sed-module-preset-message">
    <div class="sed_message_box">
        {{data.message}}
    </div>
</script>

<script type="text/template" id="tmpl-sed-preset-select-item">
    <option <# if( data.id == 0 || !data.id ){ #> selected="selected" <# } #> value="{{data.id}}">
        {{data.title}}
    </option>
</script>

<script type="text/template" id="sed-remove-preset-confirm-tpl">
    <div class="sed_message_box">
        <h3><?php echo __("Are You Sure?" , "site-editor");?></h3>
        <p><?php echo __("Do you want to delete this preset?" , "site-editor");?></p>
    </div>
</script>