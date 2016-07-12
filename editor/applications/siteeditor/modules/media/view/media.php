 <script type="text/html" data-dialog-title="<?php echo __( "Library" , "site-editor" );?>" id="tmpl-dialog-media-library">
  <div>
    <div class="bs-example fd bs-example-tabs">
        <div id="media-library-tab" class="Tapt nav nav-tabs">
            <ul>
                <li class="Tapcircle1 active">
                    <a id="sed_media_library_panel_tab" href="#sed_media_library_panel" data-toggle="tab" data-name="library">
                    <span class="el_txt"><?php echo __("Library" ,"site-editor");  ?></span>
                    </a>
                </li>
                <li class="Tapcircle1">
                    <a id="sed_media_library_upload_tab" href="#sed_media_library_upload" data-toggle="tab" data-name="upload">
                        <span class="el_txt"><?php echo __("Upload" ,"site-editor");  ?></span>
                    </a>
                </li>
                <li class="Tapcircle1">
                    <a id="sed_media_library_organize_tab" href="#sed_media_library_organize" data-toggle="tab" data-name="organize">
                        <span class="el_txt"><?php echo __("Organize" ,"site-editor");  ?></span>
                    </a>
                </li>
            </ul>
        </div>
        <div id="myTabContent4" class="tab-contentf tab-content">
            <div id="sed_media_library_panel" class="tab-pane1 tab-pane fade active in">
                <div class="media-toolbar">
                    <div class="media-toolbar-secondary">
                        <select  id="attachment-type-filter" class="attachment-filters">
                            <option value="all"><?php echo __("All media items" ,"site-editor");?></option>
                            <?php
                                $sedmediatypes = $sed_apps->media_types();
                                foreach($sedmediatypes AS $type => $type_option){
                            ?>
                                <option value="<?php echo $type;?>"><?php echo $type_option['caption'];?></option>
                            <?php
                              }
                            ?>
                        </select>
                    </div>
                    <div class="media-toolbar-primary">
                        <input class="search" type="search" id="attachment-search" placeholder="Search">
                    </div>
                </div>
                <div id="sed-media-library-container">

                  <div id="sed-media-lib-uploader-errors" class="error-box">

                  </div>

                  <ul id="site-editor-media-library">
                  <?php
                     //echo $this->media_library_load('image' , 0 , 28);
                  ?>
                  </ul>
                  <div class="media-loading" ></div>
                  <div class="empty-media-items">
                      <span><?php echo __("There are no any media items" , "site-editor")?></span>
                  </div>
                  <div class="error-media-items-lib">
                      <span></span>
                  </div>
              </div>
          </div>

          <div id="sed_media_library_upload" class="tab-pane tab-pane1 fade">

            <div id="sed-media-lib-uploader"  class="sed-uploader" sed-uploader-tmpl="tmpl-sed-media-lib-uploader">
                <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
            </div>

          </div>

          <div id="sed_media_library_organize" class="tab-pane tab-pane1 fade">
              <div id="sed-media-gallery-container">
                  <ul id="site-editor-media-gallery">
                  <?php
                     //echo $this->media_library_load('image' , 0 , 28);
                  ?>
                  </ul>
              </div>
          </div>


        </div>
    </div>
  </div>
</script>

 <script type="text/html" id="tmpl-sed-media-lib-item">
    <li tabindex="0" role="checkbox" aria-label="{{ data.title }}" aria-checked="false" data-id="{{ data.id }}" class="attachment">
        <a data-media-type="image" data-post-id="{{ data.id }}" class="sed-media-item" href="#">
        <span class="sed-media-item-selected-icon"></span>
        <span class="sed-media-item-remove-icon"><i class="fa f-sed icon-trash fa-lg"></i></span>

        <# if ( jQuery.inArray( data.type, [ 'audio', 'video' ] ) > -1 ) { #>
        <span class="sed-media-item-filename">
        	<div>{{data.filename}}</div>
        </span>
        <# } #>

        <span>
		<# if ( data.uploading ) { #>
			<div class="progress "><div class="media-progress-bar progressbar"><div class="percent"></div></div></div>
		<# } else if ( 'image' === data.type && data.sizes ) {
		      data.imgUrl = (data.sizes.thumbnail) ? data.sizes.thumbnail.url  : data.url;
        #>
			<img class="img-library bttrlazyloading" src="{{ data.imgUrl }}" data-bttrlazyloading-md-width="99" data-bttrlazyloading-md-height="99"  title="{{ data.caption }}" draggable="false" />
		<# } else if ( jQuery.inArray( data.type, [ 'audio', 'video' ] ) > -1 ) {
              data.avThumb = !_.isUndefined( data.thumb ) ? data.thumb.src : data.icon;
        #>
            <img class="img-library media-library bttrlazyloading" src="{{ data.avThumb }}" data-bttrlazyloading-md-width="99" data-bttrlazyloading-md-height="99"  title="{{ data.caption }}" draggable="false" />
		<# }else{ #>
            <img class="img-library media-library bttrlazyloading" src="{{ data.icon }}" data-bttrlazyloading-md-width="99" data-bttrlazyloading-md-height="99"  title="{{ data.caption }}" draggable="false" />
        <# } #>
        </span>
        </a>
    </li>
 </script>



 <script type="text/html" id="tmpl-sed-media-lib-uploader-errors">
    <div class="upload-error error">
      <span class="upload-error-filename texterror"><i class="error-item icon-alert-error"></i><?php echo __("Error" ,"site-editor");  ?>{{data.title}}</span>
      <span class="upload-error-message">{{data.message}}</span>
    </div>
 </script>

 <script type="text/html" id="tmpl-sed-media-lib-uploader">
  <div id="sed-media-lib-uploader-drop-area">
    <div id="sed-media-lib-uploader-container" class="uploader-inline-content no-upload-message">
      <div class="upload-ui">
        <h3 class="upload-instructions drop-instructions"><?php echo __("Drop files anywhere to upload" ,"site-editor");?></h3>
         <a id="sed-media-lib-uploader-browse" href="javascript:void(0)" class="btn btn-default iconf"  title="<?php echo __("Select Files" ,"site-editor");  ?>"  role="button">
         <span class="el_txt"><?php echo __("Select Files" ,"site-editor");  ?></span>
         </a>
      </div>
      <div class="post-upload-ui">
            <p class="max-upload-size"><?php echo __("Maximum upload file size : " ,"site-editor") . $sed_apps->sed_max_upload_size();?> mb</p>
      </div>
    </div>
  </div>

</script>


<div class="sed-dialog-type4" id="sed-dialog-media-library" title="<?php echo __("Library" ,"site-editor");  ?>">
</div>