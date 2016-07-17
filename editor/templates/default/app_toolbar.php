<!-- Editor Toolbar Part --->
<?php
$toolbar = $site_editor_app->toolbar;
$current_type = $site_editor_app->current_type;
$tabs = $toolbar->tabs;
$tabs = current_type_elemans($current_type , $tabs);
?>
<div  class="toolbar">

    <ul class="preview-mode-toolbar">
        <li class="back-to-editor">
            <button value="<?php echo __("Back To Editor" ,"site-editor");  ?>" class="btn button-primary save" id="back-to-editor-btn" name="back-to-editor-btn">
            <span class="fa f-sed icon-chevron-left icon-back-to-editor fa-lg "></span>
            <span class="el_txt"><?php echo __("Back To Editor" ,"site-editor");  ?></span>
            </button>
        </li>
        <li class="preview-mode" data-preview-mode="desktop-mode">
          <a  href="#">
          <span class="fa f-sed icon-desktop  fa-lg "></span>
          </a>
        </li>
        <li class="preview-mode" data-preview-mode="tablets-landscape-mode">
          <a  href="#">
          <span class="fa f-sed icon-tablet2  fa-lg "></span>
          </a>
        </li>
        <li class="preview-mode" data-preview-mode="tablets-portrait-mode">
          <a  href="#">
          <span class="fa f-sed icon-tablet  fa-lg "></span>
          </a>
        </li>
        <li class="preview-mode" data-preview-mode="smartphones-landscape-mode">
          <a  href="#">
          <span class="fa f-sed icon-smartphone  fa-lg "></span>
          </a>
        </li>
        <li class="preview-mode" data-preview-mode="smartphones-portrait-mode">
          <a  href="#">
          <span class="fa f-sed icon-smartphone2  fa-lg "></span>
          </a>
        </li>
    </ul>

    <ul class="site-editor-app-tools-button">
        <li class="preview">
            <button value="<?php echo __("Preview" ,"site-editor");  ?>" class="btn button-primary" id="app-preview-mode-btn" name="app-preview-mode-btn">
            <span class="fa f-sed icon-eye  fa-lg "></span>
            <span class="el_txt"><?php echo __("Preview" ,"site-editor");  ?></span>
            </button>
        </li>
        <li class="save-publish">
            <button value="<?php echo __("Saved" ,"site-editor");  ?>" class="btn button-primary save" id="save" name="save">
            <span class="fa f-sed icon-spin f-sed-spin fa-lg "></span>
            <span class="fa f-sed icon-savepublish  fa-lg "></span>
            <span class="el_txt"><?php echo __("Saved & Publish" ,"site-editor");  ?></span>
            </button>
        </li>
        <li class="sed-module-gideline">
          <a  href="#">
          <span class="fa f-sed icon-gideline  fa-lg "></span>
          </a>
        </li>
        <li class=" help-editor">
          <a  href="#">
          <span class="fa f-sed icon-question  fa-lg "></span>
          </a>
        </li>
        <!--<li class="settings-site-editor">
          <a  href="#">
          <span class="fa f-sed icon-settings fa-lg "></span>
          </a>
        </li>  -->
        <li class="sed-logo">
          <a  href="#">
          <img src="<?php echo SED_EDITOR_FOLDER_URL;?>templates/default/images/logo.png" alt="logo" style="width: 25px; margin: 4px 0 0 0;" />
          </a>
        </li>
        <!--
        <li class=" redo-site-editor">
          <a  href="#">
          <span class="fa f-sed icon-action-redo  fa-lg "></span>
          </a>
        </li>
        <li class=" undo-site-editor">
          <a href="#" >
          <span class="fa f-sed icon-action-undo  fa-lg "></span>
          </a>
        </li>  -->
    </ul>
    <ul id="myTab" class="nav nav-tabs">
    <?php
    $ti = 0;
    foreach($tabs AS $tab){
        $attr_string = '';
        $classes = '';
        if(!empty($tab->attr)){
            foreach($tab->attr AS $attr => $value){
                if(strtolower($attr) != "class"){
                    $attr_string .= $attr . '="' . $value . '" ';
                }else{
                    $classes = $value;
                }
            }
        }


        if($tab->type == "tab"){
    ?>
          <li class="tab_b <?php if($ti == 0) echo "active"; ?> <?php echo $classes;?>" id="<?php echo $tab->name;?>">
              <a data-toggle="tab" href="#<?php echo $tab->name;?>-tab-content" <?php echo $attr_string;?>>
              <?php
               if(!empty($tab->icon)){
              ?>
               <span class="img_tab"><img src="<?php echo $tab->icon;?>" alt="<?php echo $tab->title;?>" /></span>
              <?php
                }
              ?>
              <span class="el_txt"><?php echo $tab->title;?></span>
              <span class="fa f-sed icon-settings2  fa-lg "></span>
              </a>
          </li>
    <?php
        $ti++;
        }elseif($tab->type == "menu"){
    ?>
          <li class="tab_b dropdown menu_item <?php echo $classes;?>" id="<?php echo $tab->name;?>">
              <a data-toggle="dropdown" href="javascript:void(0)" <?php echo $attr_string;?>>
              <?php
               if(!empty($tab->icon)){
              ?>
               <span class="img_tab"><img src="<?php echo $tab->icon;?>" alt="<?php echo $tab->title;?>" /></span>
              <?php
                }
              ?>
              <span class="el_txt"><?php echo $tab->title;?></span>
              <span class="caret"></span>
             </a>
             <ul class="dropdown-menu menu" role="menu" aria-labelledby="dropdownMenu2">
                <?php
                  if(!empty($tab->element_group)){
                      $count_el_group = count($tab->element_group);
                      $num_g = 1;
                      $element_group_arr = current_type_elemans($current_type , $tab->element_group);
                      foreach( $element_group_arr AS $group){

                         if(!empty($tab->elements)){
                           $elements_arr = current_type_elemans($current_type , $tab->elements);
                           foreach($elements_arr AS $element){
                             if($element->group == $group->name){
                                  $classes = '';
                                  if(!empty($element->attr)){
                                      foreach($element->attr AS $attr => $value){
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
                              <li role="presentation" class="">  <a role="menuitem" class="<?php echo $link_classes;?> " tabindex="-1" href="#"><span class="fa f-sed <?php echo $classes;?> fa-lg "></span><span><?php echo $element->title;?></span>  </a>
                              <?php
                                /*if(function_exists($element->def_content)){
                                    $function = $element->def_content;
                                    $function($site_editor);
                                } */
                              ?>
                              </li>
                              <?php
                             }
                           }
                         }
                         if($num_g != $count_el_group){
                        ?>
                        <li class="divider"></li>
                        <?php
                        }
                        $num_g++;
                      }
                  }
                ?>
             </ul>
          </li>
    <?php
        }
    ?>

    <?php

     }
    ?>
    </ul>
    <div id="myTabContent" class="tab-content">
    <?php
    $i = 0;
    foreach($tabs AS $tab){
        if($tab->type == "tab"){
          $i++;
          if($i == 1)
            $class_t = "active";
          else
            $class_t = "";
    ?>
              <div id="<?php echo $tab->name;?>-tab-content" class="tab-pane fade <?php echo $class_t;?> in">
                <div class="tab_inner">
                  <div class="tab_inner_content">
                <?php
                  if(!empty($tab->element_group)){
                      $element_group_arr = current_type_elemans($current_type ,$tab->element_group);
                      $ig = 1;
                      foreach( $element_group_arr AS $group){

                         if(!empty($tab->elements)){
                           $row1 = array();
                           $row2 = array();
                           $elements_arr = current_type_elemans($current_type ,$tab->elements);
                           foreach($elements_arr AS $element){
                             if($element->group == $group->name){
                               $extra = $element->extra;
                               if($extra['row'] == 2){
                                  $row2[]  = $element;
                               }else{
                                  $row1[]  = $element;
                               }
                             }
                           }
                           if(!empty($row1) || !empty($row2) ) {
                       ?>
                         <div class="element_group" data-group-label="<?php echo $group->title;?>" data-group-name="<?php echo $group->name;?>">
                         <div class="iconz">
                          <table class="iconz_table"  cellpadding="0" cellspacing="0">
                       <?php
                           $col_row1 = 0;
                           foreach($row1 AS $element){
                               if(!empty($element->sub)){
                                   $subelements_arr = current_type_elemans($current_type ,$element->sub);
                                   foreach($subelements_arr AS $sub){
                                       $col_row1++;
                                   }
                               }else{
                                   $col_row1++;
                               }
                           }

                           $col_row2 = 0;
                           foreach($row2 AS $element){
                               if(!empty($element->sub)){
                                   $subelements_arr = current_type_elemans($current_type ,$element->sub);
                                   foreach($subelements_arr AS $sub){
                                       $col_row2++;
                                   }
                               }else{
                                   $col_row2++;
                               }
                           }

                             $dif = $col_row2 - $col_row1;
                             //echo $dif;
                               ?>
                                <tr class="row1">
                                <?php
                                if(!empty($row1)){

                                 foreach($row1 AS $el1){

                                   $extra = $el1->extra;

                                   $rowspan =  (!empty($extra['rowspan']) && is_int($extra['rowspan'])) ? 'rowspan="'.$extra['rowspan'].'"': '';
                                   $colspan =  (!empty($extra['colspan']) && is_int($extra['colspan'])) ? 'colspan="'.$extra['colspan'].'"': '';
                                 ?>
                                 <td <?php echo $rowspan." ".$colspan;?>>
                                  <div class="icon">
                                    <?php
                                      echo $el1->content;
                                    ?>
                                  </div>
                                </td>
                                <?php
                                   }
                                 }
                                 if($dif > 0 || empty($row1)){
                                ?>
                                 <td colspan="<?php echo abs($dif);?>"></td>
                                <?php } ?>
                                </tr>

                                <tr class="row2">

                                <?php
                                if(!empty($row2)){
                                 foreach($row2 AS $el2){
                                  $extra = $el2->extra;

                                   $rowspan =  (!empty($extra['rowspan']) && is_int($extra['rowspan'])) ? 'rowspan="'.$extra['rowspan'].'"': '';
                                   $colspan =  (!empty($extra['colspan']) && is_int($extra['colspan'])) ? 'colspan="'.$extra['colspan'].'"': '';
                                 ?>
                                 <td <?php echo $rowspan." ".$colspan;?>>
                                  <div class="icon">
                                    <?php
                                      echo $el2->content;
                                    ?>
                                  </div>
                                </td>
                                <?php
                                  }
                                 }
                                 if($dif < 0 || empty($row2)){
                                ?>
                                 <td colspan="<?php echo abs($dif);?>"></td>
                                <?php } ?>
                                </tr>
                          </table>
                         </div>
                         <div class="title" id="<?php echo $group->name;?>-group"><span><?php echo $group->title;?></span></div>
                         </div>
                         <?php if($ig != count($element_group_arr)){?><div class="spr"></div> <?php } ?>
                        <?php
                        $ig++;
                            }
                         }

                      }
                  }
                ?>
                 </div>
                </div>
                <?php do_action("after_inner_tab_content_{$tab->name}"); ?>
              </div>
    <?php
        }
    }
    ?>
    </div>
</div>
<!-- End Editor Toolbar Part --->