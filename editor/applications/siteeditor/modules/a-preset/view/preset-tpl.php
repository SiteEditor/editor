<style>
    input.preset-edit {
        display: none;
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    .editing input.preset-edit {
        display: block;
    }

    span.action{
        display: block;
    }

    span.sed-default.action{
        color: #f52e16;
        display: block;
    }
    .sed-presets-list-container {
        margin: 5px 0;
    }
    .sed-presets-list > li {
        position: relative;
    }
    .sed-preset-item {
        /*background-color: #fafafa !important;*/
        border: 1px solid #d5d5d5;
        border-radius: 2px;
        box-shadow: none;
        color: #666;
        font-size: 12px;
        margin: 0 0 5px;
        padding: 5px 10px;
        width: 100%;
    }
    .sed-preset-item > label {
        line-height: 20px;
        max-width: 50%;
        width: 50%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        white-space: nowrap;
    }
    .sed-preset-item:after,
    .sed-preset-item:before {
        display: table;
        content: "";
    }
    .sed-preset-item:after {
        clear: both;
    }
    .preset-actions {
        float: right;
    }
    .preset-actions > span.action {
        display: inline-block;
        margin: 0 2px;
    }
    .preset-actions > span.set-default.action > span:last-child {
        display: none;
    }
    .preset-actions > span.sed-default.set-default.action > span:first-child {
        display: none;
    }
    .preset-actions > span.sed-default.set-default.action > span:last-child {
        display: block;
    }
    .preset-actions > span.action .fa {
        color: #888;
        font-size: 1.15em;
        cursor: pointer;
    }
    .preset-actions > span.action:hover .fa {
        color: #00A9E8;
    }
    .preset-actions > span.sort .fa {
        cursor: move;
    }

</style>

<!-- Preset Templates -->

<script type="text/template" id="tmpl-sed-preset-item">
    <div>
        <div class="sed-preset-item">
            <label title="{{data.title}}">{{data.title}}</label>
            <div class="preset-actions">
                <span data-action="load" class="load-preset action"><span class="fa fa-eye fa-lg"></span></span>
                <span data-action="edit" class="edit action"><span class="fa fa-pencil fa-lg"></span></span>
                <span data-action="default" class="set-default action <# if(data.isDefault){ #>sed-default<# } #>"><span class="fa fa-star-o fa-lg"></span><span class="fa fa-star fa-lg"></span></span>
                <span data-action="remove" class="trash destroy action"><span class="fa fa-trash-o fa-lg"></span></span>
                <span data-action="sort" class="sort action"><span class="fa fa-arrows fa-lg"></span></span>
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