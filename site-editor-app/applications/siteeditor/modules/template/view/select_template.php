<div id="sed-app-control-select_template" class="">
    <a href="javascript:void(0)" class="btn btn-default3 sed-select-template"  title="<?php echo __("Select Template" ,"site-editor");  ?>" role="button" >
        <span class="fa f-sed icon-selecttemplate fa-2x "></span>
        <span class="el_txt"><?php echo __("Select Template" ,"site-editor");  ?></span>
    </a>
    <div id="sed-dialog-template-library"  class="sed-dialog "  title="<?php echo __("Template Library" ,"site-editor");  ?>">
      <div class="template-library-toolbar">
          <div class="template-library-toolbar-secondary">
              <select id="template-group-filter">
                 <option value="all"><?php echo __("All template items" ,"site-editor");  ?></option>
                 <?php
                    global $site_editor_app;
                    $groups = $site_editor_app->template->groups;

                 if(!empty( $groups )){
                     foreach($groups AS $name => $group){
                 ?>
                 <option value="<?php echo $name; ?>" ><?php echo $group->title; ?></option>
                 <?php
                     }
                 }
                 ?>
              </select>
          </div>
          <div class="template-library-toolbar-primary">
              <input class="search" type="search" id="template-library-search" placeholder="Search">
          </div>
      </div>
      <div id="sed-template-library-container" >
          <ul id="site-editor-template-library">

           </ul>
       </div>
    </div>
</div>



    <div id="sed-dialog-confirm-change-template"  class="sed-dialog"  title="<?php echo __("Confirm Change Template" ,"site-editor");  ?>">
        <div id="change-template-alert-confirm" class="alert-confirm">
            <p><?php echo __("do you want apply this template on page?","site-editor");  ?></p>
            <div class=""><input class="btn btn-default" type="button" id="ok-change-template-btn" value="<?php echo __("Ok","site-editor");  ?>" /></div>
            <div class=""><input class="btn btn-default" type="button" id="cancel-change-template-btn" value="<?php echo __("Cancel","site-editor");  ?>" /></div>
        </div>

        <form id="change-template-settings-form" name="change-template-settings">
            <div id="apply-main-content-field">
                <span><?php echo __("Select how apply main content on page?","site-editor");  ?></span>
                <div><label>  <input type="radio" value="<?php echo __("merge","site-editor");  ?>" name="change-main-content" checked="checked" /> <?php echo __("append template main content to current main content","site-editor");  ?></label></div>
                <div><label>  <input type="radio" value="<?php echo __("override","site-editor");  ?>" name="change-main-content" /> <?php echo __("override all current main content","site-editor");  ?></label></div>
                <div><label>  <input type="radio" value="<?php echo __("no_action","site-editor");  ?>" name="change-main-content" /> <?php echo __("Not using template main contents.","site-editor");  ?></label></div>
            </div>

            <div id="remain-content-container-field">
                <label>
                    <input type="checkbox" value="true" name="remain-content-container" checked="checked" />
                    <span><?php echo __("Keep main content container and its changes.","site-editor");  ?> </span>
                </label> <br /><br />
            </div>

            <div>
               <input class="sed-btn-blue" type="button" id="confirm-change-template-btn" value="<?php echo __("Change Page Template","site-editor");  ?>" />
            </div>

        </form>

    </div>

 <script type="text/html" id="tmpl-sed-template-lib-item">
     <li class="preview-template">
        <a rel="sed-template" class="sed-template" data-value="{{ data.id }}" href="#">
          <span class="template-screenshot"><img src="{{ data.screenshot }}" alt=""></span>
          <span class="template-title">{{ data.title }}</span>
        </a>
    </li>
 </script>

