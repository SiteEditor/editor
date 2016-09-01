<?php
  $panel = $site_editor_app->panel;
  $panels = $panel->panels;
?>

 <div class="left_palette">
 <div class="bs-example bs-example-tabs height_pl" >
 <div class="plate1 nav nav-tabs" id="zmind-platte">
 <ul>
 <li class="icona"><a  id="button" class="btn112 btn-default112"><span class="fa111 right-open fa-lg111 "></span></a></li>
<?php
  foreach($panels AS $item){
      $attr_string = '';
      $classes = '';
      if(!empty($item->attr)){
          foreach($item->attr AS $attr => $value){
              if(strtolower($attr) != "class"){
                  $attr_string .= $attr . '="' . $value . '" ';
              }else{
                  $classes = $value;
              }
          }
      }
        $link_classes = "";
        $arr_classes = explode(" ",$classes);

        if(!empty($arr_classes)){
            foreach( $arr_classes AS $class){
               $link_classes .= "link_".$class." ";
            }
        }
?>
 <li class="icona"><a  href="#<?php echo $item->name;?>"  data-toggle="tab" class="btn111 btn-default111 iconf2 <?php echo $link_classes;?>"  title="<?php echo $item->title;?>"  <?php echo $attr_string;?>><span class="arrow_pl"></span><span class="fa111 <?php echo $classes;?> fa-lg111 "></span></a> </li>
<?php
  }
?>
  </ul>

  <div id="effect" class="plate2">
   <div class="plate">
  <div id="myTabContent3" class="tab-content">
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
  </div></div>
</div></div></div>
