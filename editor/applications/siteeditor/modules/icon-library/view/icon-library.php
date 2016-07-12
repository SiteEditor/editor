<script type="text/html" data-dialog-title="<?php echo __( "Icon Manager" , "site-editor" );?>" id="tmpl-dialog-icon-library">
<div>
<div class="bs-example fd bs-example-tabs">
<div id="icon-library-tab" class="Tapt nav nav-tabs">
<ul>
<li class="Tapcircle1 active">
<a id="sed_icon_library_panel_tab" href="#sed_icon_library_panel" data-toggle="tab">
<span class="el_txt"><?php echo __("Library" ,"site-editor");  ?></span>
</a>
</li>
<li class="Tapcircle1">
<a id="sed_icon_library_upload_tab" href="#sed_icon_library_upload" data-toggle="tab">
<span class="el_txt"><?php echo __("Upload" ,"site-editor");  ?></span>
</a>
</li>
</ul>
<div class="icon-toolbar">
  <div class="icon-toolbar-primary">
  <input class="search" type="search" id="sed-icon-search" placeholder="Search">
  </div>
  </div>
</div>
<div id="icon_tab_content" class="tab-contentf tab-content">
<div id="sed_icon_library_panel" class="tab-pane1 tab-pane fade active in">
  <div id="sed-icon-library-container">

  <div id="sed-icon-lib-uploader-errors" class="error-box">
    <div class="upload-error error">
      <span class="upload-error-filename texterror">{{err-code}}</span>
      <span class="upload-error-label ui-button-error ui-buttonf"><?php echo __("Error" ,"site-editor");  ?></span>
      <span class="upload-error-message texterror1">{{err-message}}</span>
    </div>
  </div>

  <div id="sed-icon-lib-uploader-items" class="icon-items" >
      <ul class="media-item-progress">
        <li class="icon-item" id="icon-item-{{file-id}}">
            <div class="progress">
              <div class="progressbar bar">
                  <div class="percent"></div>
              </div>
            </div>
        </li>
      </ul>
      <div class="media-item-uploaded">

      </div>
  </div>
  <div class="sed-loading-small-continer icon-library-loading" ><div class="sed-loading" ></div></div>
     <div class="success-font-icon-load" ><p><?php echo __("Font icon added successfully!" , "site-editor");?></p></div>
    <div class="error-font-icon-load" ><p><?php echo sprintf(__("Couldn't add the font.<br/>The script returned the following error: %s" , "site-editor") , "{{response}}");?></p></div>
     <div class="success-font-icon-remove" ></div>
    <div class="error-font-icon-remove" ></div>
    <div class="empty-icon-items">
    <span><?php echo __("There are no any icon items" , "site-editor")?></span>
    </div>
    <div class="error-icon-items-lib">
    <span></span>
    </div>
  <div id="site-editor-icon-library">
  <?php
     echo $this->get_icons_fonts();
  ?>
</div>

</div>
</div>
<div id="sed_icon_library_upload" class="tab-pane tab-pane1 fade">

  <div id="sed-icon-lib-uploader"  class="sed-uploader" sed-uploader-tmpl="tmpl-sed-icon-lib-uploader">
      <p><?php echo __("Your browser doesn`t have Flash, Silverlight or HTML5 support." ,"site-editor");  ?></p>
  </div>

</div>
</div>
</div>
</div>
</script>

<script type="text/html" id="tmpl-sed-icon-lib-uploader">
<div id="sed-icon-lib-uploader-drop-area">
  <div id="sed-icon-lib-uploader-container" class="uploader-inline-content no-upload-message">
    <div class="upload-ui">
      <h3 class="upload-instructions drop-instructions"><?php echo __("Drop files anywhere to upload" ,"site-editor");?></h3>
       <a id="sed-icon-lib-uploader-browse" href="javascript:void(0)" class="btn btn-default iconf"  title="<?php echo __("Select Files" ,"site-editor");  ?>"  role="button">
       <span class="el_txt"><?php echo __("Select Files" ,"site-editor");  ?></span>
       </a>
    </div>
    <div class="post-upload-ui">
          <p class="max-upload-size"><?php echo __("Maximum upload file size : " ,"site-editor") . $sed_apps->sed_max_upload_size();?> mb</p>
          <p class="upload-help"><?php echo __("Upload icons downloaded only from icomoon" ,"site-editor");?></p>
          <p class="upload-help"><?php echo __("please upload one or more zip file include yourfont.eot , yourfont.svg , <br /> yourfont.ttf , yourfont.woff and selection.json and too style.css. " ,"site-editor");?></p>
    </div>
  </div>
</div>

</script>

<div id="sed-dialog-icon-library" title="<?php echo __("Library" ,"site-editor");  ?>">
</div>
