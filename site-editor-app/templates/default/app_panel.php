<?php
  $panel = $site_editor_app->panel;
  $panels = $panel->panels;
?>

<div class="left_palette">
    <div class="bs-example bs-example-tabs height_pl" >
        <div class="plate1 nav nav-tabs" id="zmind-platte">
            <ul>
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
                <li>
                    <div class="link-menu <?php echo $link_classes;?>" sed-dialog="sed-dialog-menu"   <?php echo $attr_string;?>><span class="fa icon-cloud-upload <?php echo $classes;?> fa-lg "></span><span class="el_txt"><?php echo $item->title;?></span></div>
                    <div id="sed-dialog-menu" class="sed-dialog-sp" title="<?php echo $item->content_title;  ?>">
                       <?php
                          echo $item->content;
                       ?>
                   </div>
                   <?php do_action( "app_panel_item_{$item->name}" , $item ); ?>
                </li>
                <?php
                  }
                ?>
            </ul>
        </div>
    </div>
</div>
