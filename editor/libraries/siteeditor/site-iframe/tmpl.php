<?php
add_action( 'wp_footer', 'load_site_iframe_tmpl' );

function load_site_iframe_tmpl(){
?>
<script type="text/html" id="tmpl-hd-dpa">
    <div id="sed-header-drop-area" class="sed-drop-area-layout"> </div>
</script>
<script type="text/html" id="tmpl-ft-dpa">
    <div id="sed-footer-drop-area" class="sed-drop-area-layout"> </div>
</script>
<script type="text/html" id="tmpl-top-mc-dpa">
    <div id="sed-top-mcontent-drop-area" class="sed-drop-area-layout"> </div>
</script>
<script type="text/html" id="tmpl-bot-mc-dpa">
    <div id="sed-bot-mcontent-drop-area" class="sed-drop-area-layout"> </div>
</script>

<script type="text/html" id="tmpl-mid-mc-col">
    <div id="sed-mcontent-column" class=""> </div>
</script>

<script type="text/html" id="tmpl-drag-n-drop">
  <div class="sed-handle-sort-row sed-handle-sort-row-btm " handle-dnp="sed-handle-sort-row" >
     <div class="sed-handle-sort-row-inner">
      <div class="drag-sty-part2 drag-sty-part2-active">
      <div class="edit_btn"><div class=" btn_part2" id="">
      <span class="f-sed link fa-lg "></span>
      <span class="el_txt">edit</span>
      </div></div>
      <div class="drag_btn"><div class=" btn_part2" id="">
      <span class="f-sed link fa-lg "></span>
      <span class="el_txt">drag</span>
      </div></div>
      <div class="remove_btn"><div class=" btn_part2" id="">
      <span class="f-sed link fa-lg "></span>
      <span class="el_txt">remove</span>
      </div></div>
      </div>
     </div>
</div>
    <div class="sed-highlight-row-top"  ></div>
     <div class="sed-highlight-row-right "  ></div>
     <div class="sed-highlight-row-bottom "  ></div>
     <div class="sed-highlight-row-left "  > </div>


</script>

<script type="text/html" id="tmpl-without-drag-n-drop">
  <div class="sed-handle-sort-row sed-handle-sort-row-btm " handle-dnp="sed-handle-sort-row" >
     <div class="sed-handle-sort-row-inner sed-handle-woutdnp-row-inner">
      <div class="drag-sty-part2 drag-sty-part2-active">
      <div class="edit_btn"><div class=" btn_part2" id="">
      <span class="f-sed link fa-lg "></span>
      <span class="el_txt">edit</span>
      </div></div>
      <div class="remove_btn"><div class=" btn_part2" id="">
      <span class="f-sed link fa-lg "></span>
      <span class="el_txt">remove</span>
      </div></div>
      </div>
     </div>
</div>
    <div class="sed-highlight-row-top"  ></div>
     <div class="sed-highlight-row-right "  ></div>
     <div class="sed-highlight-row-bottom "  ></div>
     <div class="sed-highlight-row-left "  > </div>
</script>

<script type="text/html" id="tmpl-drag-n-drop-helper">
    <div class="helper-drag">
    <div class="helper-drag-content">
    <div class="drag_btn_crt">
    <button class="btn_helper" id="">
      <span class="f-sed icon-cursor-move link"></span>
      <span class="el_txt">drag</span>
      </button>
      </div>
      <div class="drag_content">
       <div><img src="<?php echo(SED_EDITOR_FOLDER_URL . 'libraries/siteeditor/site-iframe/images/browser_64px.png'); ?>" /></div>
       <div><h5>Drop and drop Module</h5></div>
      </div>
      </div>
      </div>
</script>
<script type="text/html" id="tmpl-dnp-el-hdl">
    <div class="sed-dnp-el-handle" handle-dnp="sed-dnp-el-handle" >


    </div>
</script>

<script type="text/html" id="tmpl-row-resizable-handle">
<div class="resizable-handle-spci">
 <div class="resizable-btm resizable-btn">
  <span class="f-sed link fa_lg2"></span>
 </div>
 </div>
</script>
 <script type="text/html" id="sed-bp-element-handle-tmpl">
 <div class="sed-handle-sort-row sed-handle-sort-row-btm ">
<div class="drag-content-sty drag-content-sty-active">
      <div class="setting_btn sed_setting_btn_cmp"><div class="sed_setting_btn_cmp drag-content" id="">
      <span class="f-sed icon-redo link fa-lg sed_setting_btn_cmp"></span>
      <span class="el_txt sed_setting_btn_cmp">setting</span>
      </div></div>
      <div class="drag_pb_btn"><div class=" drag-content" id="">
      <span class="f-sed icon-cursor-move link fa-lg "></span>
      <span class="el_txt">drag</span>
      </div></div>
      <div class="remove_pb_btn" title = "<?php echo __( "Module Remove" , "site-editor");?>"><div class="drag-content" >
      <span class="f-sed icon-trash link fa-lg "></span>
      <span class="el_txt">remove</span>
      </div></div>
      </div>
      </div>
    <div class="sed-pb-handle-row-top"  ></div>
     <div class="sed-pb-handle-row-right "  ></div>
     <div class="sed-pb-handle-row-bottom "  ></div>
     <div class="sed-pb-handle-row-left "  > </div>
</script>

<script type="text/html" id="sed-static-module-handle-tmpl">
  <div class="sed-static-module-action-bar">
      <ul>
          <li class="edit-action-item"><a href="#"><span class="f-sed icon-edit fa-lg "></span><span> <?php echo __("Edit Content" , "site-editor");?> </span></a> </li>
          <li><a href="#"><span class="f-sed icon-trash fa-lg "></span><span> <?php echo __("remove" , "site-editor");?> </span></a> </li>
      </ul>
  </div>
</script>

<script type="text/html" id="sed-layout-main-row-tmpl">                      //sed-draggable-element = 0 || 1
<div class="sed-main-row"  sed-layout="row" sed-type-row="dynamic" sed-row-area="" sed-draggable-element="">

</div>
</script>


<script type="text/html" id="sed-remove-alert-tmpl">
<p>
  <?php echo __( "Do you want to delete this element?" , "site-editor");?>
</p>
<div class="button-popover" >
 <button class="sed-btn-red sed-module-remove-btn" > <?php echo __( "Delete" , "site-editor");?> </button>
 <button class="sed-btn-default close-popover" > <?php echo __( "Cancel" , "site-editor");?> </button>
</div>

</script>


<?php
}


?>