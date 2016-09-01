<?php
/*
Module Name: Links
Module URI: http://www.siteeditor.org/modules/links
Description: Module Links For Site Editor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
add_action( 'sed_footer' , 'add_tmpls_link_to' );
function add_tmpls_link_to(){
?>
     <div class="sed-dialog-type3" id="sed-dialog-web-address" title=""></div>
     <script type="text/html" data-dialog-title="<?php echo __( "Link to Web Address" , "site-editor" );?>" id="tmpl-dialog-web-address">
        <div class="link_item"><?php echo __( "Web Address" , "site-editor" );?></div>
        <div class="row_setting_box">
        <label><?php echo __( "Insert a web address" , "site-editor" );?> : </label>
          <input class="" placeholder="E.g www.stars-ideas.com" type="url" value="" name="web_links">
           <label>
          <input class="radio_btn open_window" type="radio" value="new_window" name="open_window">
          <?php echo __( "Open in new window" , "site-editor" );?></label>
          <label>
          <input class="radio_btn open_window" type="radio" value="same_window" name="open_window">
          <?php echo __( "Open in same window" , "site-editor" );?></label>
         </div>
     </script>

     <div class="sed-dialog-type3" id="sed-dialog-page-link" title="<?php echo __("Link to Page" ,"site-editor");  ?>"> </div>
     <script type="text/html" data-dialog-title="<?php echo __("Link to Pages" ,"site-editor");  ?>" id="tmpl-dialog-page-link">
        <div class="link_item">Page</div>
        <div class="row_setting_box">
        <label>select Page:</label>
          <select id="">
          <option value="menu area"><?php echo __("menu area" ,"site-editor");  ?></option>
          <option value="submenu area"><?php echo __("submenu area" ,"site-editor");  ?></option>
          <option value="sub menu item hover"><?php echo __("sub menu item hover" ,"site-editor");  ?></option>
          <option value="sub menu item passive"><?php echo __("sub menu item passive" ,"site-editor");  ?></option>
          </select>
        </div>
     </script>

     <div class="sed-dialog-type3" id="sed-dialog-page-top" title="<?php echo __("Link to Page Top" ,"site-editor");  ?>"></div>
     <script type="text/html" data-dialog-title="<?php echo __("Link to Page Top" ,"site-editor");  ?>" id="tmpl-dialog-page-top">
        <div class="link_item"> Page Top</div>
        <div class="row_setting_box">
        <label>When visitors click this link they'll be taken to the top of the page. Click OK to accept, or go Back to link options. </label>
        </div>
     </script>

     <div class="sed-dialog-type3" id="sed-dialog-page-bottom" title="<?php echo __("Link to Page Bottom" ,"site-editor");  ?>"></div>
     <script type="text/html" data-dialog-title="<?php echo __("Link to Page Bottom" ,"site-editor");  ?>" id="tmpl-dialog-page-bottom">
      <div class="link_item"> Page Bottom</div>
      <div class="row_setting_box">
      <label>When visitors click this link they'll be taken to the bottom of the page. Click OK to accept, or go Back to link options. </label>
      </div>
     </script>

     <div class="sed-dialog-type3" id="sed-dialog-email-link" title="<?php echo __("Link to Email" ,"site-editor");  ?>"></div>
     <script type="text/html" data-dialog-title="<?php echo __("Link to Email" ,"site-editor");  ?>" id="tmpl-dialog-email-link">
        <div class="link_item">Email</div>
        <div class="row_setting_box">
         <label>Insert an email address</label>
          <input class="" placeholder="E.g info@stars-ideas.com" type="email" value="" name="email">
          <label>Insert a subject</label>
          <input class="" placeholder="E.g Request for information" type="text" value="" name="subject">
        </div>
     </script>
     <div class="sed-dialog-type3" id="sed-dialog-document-link" title="<?php echo __("Link to Document" ,"site-editor");  ?>"></div>
     <script type="text/html" data-dialog-title="<?php echo __("Link to Document" ,"site-editor");  ?>" id="tmpl-dialog-document-link">
        <div class="link_item"> Document</div>
         <div class="row_setting_box">
         <label>No Document Selected</label>
         <button class="uplod_document"><?php echo __("upload Document" ,"site-editor");  ?></button>
        </div>
     </script>

     <div class="sed-dialog-type3" id="sed-dialog-anchor-link" title="<?php echo __("Link to Anchor" ,"site-editor");  ?>"></div>
     <script type="text/html" data-dialog-title="<?php echo __("Link to Anchor" ,"site-editor");  ?>" id="tmpl-dialog-anchor-link">
        <div class="link_item">Anchor</div>
        <div class="row_setting_box">
        <label>Which page is the anchor on?</label>
          <select class="anchor-select" id="">
          <option value="#mainPage"><?php echo __("HOME (You're here)" ,"site-editor");  ?></option>
          <option value="#cnec"><?php echo __("BOOKS" ,"site-editor");  ?></option>
          <option value="#c1ktj"><?php echo __("BIO" ,"site-editor");  ?></option>
          <option value="#c1pz"><?php echo __("NEWS & EVENTS" ,"site-editor");  ?></option>
          <option value="#c1kcz"><?php echo __("CONTACT" ,"site-editor");  ?></option>
          <option value="#c112v"><?php echo __("BLOG" ,"site-editor");  ?></option>
          </select>
          <label>Select an anchor to link to:</label>
          <select class="anchor-select" id="">
          <option value="XXX"><?php echo __("You have no anchors on this page" ,"site-editor");  ?></option>
          </select>
          <a>How to create an anchor?</a>
        </div>
     </script>

<?php
}
