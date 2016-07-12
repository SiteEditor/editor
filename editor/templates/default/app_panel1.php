
<div class="left_palette">
<div class="link-menu" sed-dialog="sed-dialog-menu" >
<span class="fa icon-cloud-upload fa-lg "></span>
<span class="el_txt"><?php echo __("Pages" ,"site-editor");  ?></span>
</div>

<div id="sed-dialog-menu" class="sed-dialog-sp" title="<?php echo __("Pages" ,"site-editor");  ?>">
  <div class="menu_item"> <?php echo __("Pages" ,"site-editor");  ?></div>
      <div class="link-pages">

<?php
  foreach($panels AS $item){
 ?>

  <div class="tab-pane fade plate" id="<?php echo $item->name;?>">
       <?php
           echo $item->content;
      ?>
  </div>
 <?php
 }
 ?>
    </div>
   </div>
</div>

