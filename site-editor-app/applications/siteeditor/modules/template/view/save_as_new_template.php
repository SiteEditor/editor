<div id="sed-app-save-new-template" class="">
    <a href="javascript:void(0)" class="btn btn-default3 "  title="<?php echo __("Save As New Template","site-editor");  ?>" id="save_as_new_template" role="button" >
        <span class="fa f-sed icon-eye fa-2x "></span>
        <span class="el_txt"><?php echo __("Save Template","site-editor");  ?></span>
    </a>
    <div id="sed-dialog-save-new-template"  class="sed-dialog"  title="<?php echo __("Save As New Template" ,"site-editor");  ?>">

    <div id="save-template-alert-box">

    </div>
              
    <form class="add-new-template row_setting_box" name="add-new-template">
        <div><label><?php echo __("Title","site-editor");  ?> : </label> <input type="text" value="" placeholder="<?php echo __("Title","site-editor");  ?>" name="title" /></div>
         <div><label><?php echo __("Tags","site-editor");  ?> : </label> <input type="text" value="" name="tags" /></div>
        <div>
            <label><?php echo __("Select Group","site-editor");  ?> : </label>
            <select name="group">
               <?php
                  global $site_editor_app;
                  $groups = $site_editor_app->template->groups;

               if(!empty( $groups )){
                   foreach($groups AS $name => $group){
                    $selected = ( $group == "general") ? 'selected="selected"': '';
               ?>
               <option value="<?php echo $name; ?>" <?php echo $selected;?>><?php echo $group->title; ?></option>
               <?php
                   }
               }
               ?>
           </select>
        </div>
        <div>
            <label><?php echo __("description","site-editor");  ?> : </label>
            <textarea name="description" >

            </textarea>
        </div>
        <div>
           <div id="sed-template-screenshot-preview"></div>
           <input type="hidden" value="" name="screenshot"/>
           <input class="sed-btn-primary" id="select-sed-template-screenshot" type="button" value="<?php echo __("Select Screenshot","site-editor");  ?>" />
        </div>
        <div>
           <input class="sed-btn-primary" type="button" id="add-new-template-btn" value="<?php echo __("Add Template","site-editor");  ?>" />
        </div>
       </form>
    </div>
</div>
 <script type="text/html" id="tmpl-sed-save-template-alert">
    <#
    if( _.isArray(data.output) ){
        _.each( data.output , function(msg){
    #>
        <div class="alert {{data.alertType}}"><span>{{msg}}</span></div>
    <#
        });
    }else{
    #>
        <div class="alert {{data.alertType}}"><span>{{data.output}}</span></div>
    <# } #>
 </script>
 <script type="text/html" id="tmpl-sed-template-screenshot">
    <div class="screenshot-attachment">
        <a href="javascript:void(0)">
			<img class="screenshot" src="{{ data.imgUrl }}"  title="{{ data.caption }}" />
        </a>
    </div>
 </script>
