  <div class="dropdown">
     <a  href="javascript:void(0)" class="btn btn-default"  title="<?php echo __("box shadow" ,"site-editor");  ?>" data-toggle="dropdown" id="box-shadow" role="button">
            <span class="fa icon-boxshadow fa-lg "></span>
            <span class="el_txt"><?php echo __("box shadow" ,"site-editor");  ?></span>
            <span class=""><i class="caret"></i></span>
            </a>

  <form role="menu" class="dropdown-menu dropdown-common sed-dropdown" aria-labelledby="box-shadow" sed-shadow-cp-el="#shadow-colorpicker" sed-style-element="">
    <div id="sed-app-control-sted_shadow" class="dropdown-content sed-dropdown content">

        <div>
          <ul>
              <li>
              <a class="heading-item first-heading-item" data-position="topLeft"  href="#"><?php echo __("no shadow" ,"site-editor");  ?></a>
              </li>
              <li>
               <ul class="itme-box-shadow">
                 <li class="no-box-shadow "><a><span  class="style-box-shadow"></span></a></li>
                 <li class="clr"></li>
               </ul>
              </li>
          </ul>
        </div>
        <div>
          <ul>
              <li>
              <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("shadow" ,"site-editor");  ?></a>
              </li>
              <li>
               <ul class="itme-box-shadow">
                  <li class="border-box-type1"><a data-box-shadow="0px 0px 5px -1px" href="#"><span  class="style-box-shadow1 "></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="0 0 14px -6px" href="#"><span  class="style-box-shadow2"></span></a></li>
                  <li class="border-box-type1"><a data-box-shadow="2px 2px 5px -1px" href="#"><span  class="style-box-shadow3"></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="2px -2px 5px -1px" href="#"><span  class="style-box-shadow4"></span></a></li>
                  <li class="border-box-type1"><a data-box-shadow="-2px 2px 5px -1px" href="#"><span  class="style-box-shadow5"></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="-2px -2px 5px -1px" href="#"><span  class="style-box-shadow6"></span></a></li>
                  <li class="border-box-type1"><a data-box-shadow="0px 2px 5px -1px" href="#"><span  class="style-box-shadow7"></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="0px -2px 5px -1px" href="#"><span  class="style-box-shadow8"></span></a></li>
                  <li class="border-box-type3"><a data-box-shadow="2px 0px 5px -1px" href="#"><span  class="style-box-shadow9"></span></a></li>
                  <li class="border-box-type4"><a data-box-shadow="-2px 0px 5px -1px" href="#"><span  class="style-box-shadow10"></span></a></li>
                  <li class="clr"></li>
               </ul>
              </li>
          </ul>
        </div>
        <div>
          <ul>
              <li>
              <a class="heading-item" data-position="topLeft"  href="#"><?php echo __("no" ,"site-editor");  ?></a>
              </li>
              <li>
               <ul class="itme-box-shadow">
                  <li class="border-box-type1"><a data-box-shadow="0px 0px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow11 "></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="0 0 14px -6px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow12"></span></a></li>
                  <li class="border-box-type1"><a data-box-shadow="2px 2px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow13"></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="2px -2px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow14"></span></a></li>
                  <li class="border-box-type1"><a data-box-shadow="-2px 2px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow15"></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="-2px -2px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow16"></span></a></li>
                  <li class="border-box-type1"><a data-box-shadow="0px 2px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow17"></span></a></li>
                  <li class="border-box-type2"><a data-box-shadow="0px -2px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow18"></span></a></li>
                  <li class="border-box-type3"><a data-box-shadow="2px 0px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow19"></span></a></li>
                  <li class="border-box-type4"><a data-box-shadow="-2px 0px 5px -1px" data-box-shadow-inset="true" href="#"><span  class="style-box-shadow20"></span></a></li>
                  <li class="clr"></li>
               </ul>
              </li>
          </ul>
        </div>
    </div>
    <div class="customize_item">
     <span class="el_txt box-shadow-dialog"><?php echo __("customize box shadow ..." ,"site-editor");  ?></span>
 <div id="box-shadow-dialog" title="<?php echo __("Box Shadow" ,"site-editor");  ?>">

  <div><span class="el_txt"><?php echo __("Box Shadow Style" ,"site-editor");  ?></span> </div>
    <ul class="common-styleeditor-dialog">
      <li>
        <span class="el_txt"><?php echo __("Inset" ,"site-editor");  ?></span>
        <div class="">
        <select id="" class="" name="">
          <option  selected="selected" value="no"><?php echo __("No" ,"site-editor");  ?></option>
          <option value="yes"><?php echo __("Yes" ,"site-editor");  ?></option>
        </select>
          </div>
      </li>
      <li>
        <span class="el_txt"><?php echo __("Horizontal Length " ,"site-editor");  ?></span>
        <div class="slider"></div>
      </li>
      <li>
        <span class="el_txt"><?php echo __("Vertical Length" ,"site-editor");  ?></span>
        <div class="slider"></div>
      </li>
      <li>
        <span class="el_txt"><?php echo __("Blur Radius" ,"site-editor");  ?></span>
        <div class="slider"></div>
      </li>
      <li class="colorpicker">
           <span class="el_txt"><?php echo __("Shadow Color" ,"site-editor");  ?></span>
           <span class="colorselector" ><input  class="input-colorpicker sed-colorpicker " sed-style-element="" name="shadow-colorpicker" type="text">&nbsp;&nbsp;</span>
      </li>

    </ul>
  </div>
    </div>
  </form>
</div>


 <!--    <ul><li><a class="heading-item" data-position="topLeft"  href="#"><?php echo __("no itme-box-shadow" ,"site-editor");  ?></a></li>
      <li>
           <a class="colorpicker" id="sed-app-control-sted_shadow_color" >
               <span class="el_txt"><?php echo __("Shadow Color" ,"site-editor");  ?></span>
               <span class="spinner1 "><input  class="input-colorpicker sed-colorpicker" sed-style-element="" name="shadow-colorpicker" type="text">&nbsp;&nbsp;</span>
           </a>
       </li>
             <li>
             <ul class="sed-dropdown">
             <li><a class="heading-item" data-position="topLeft"  href="#"><?php echo __("no itme-box-shadow" ,"site-editor");  ?></a></li>
              <li class="no_shadow itme-box-shadow"><a><span  class="style-box-shadow"></span></a></li>

              <li><a class="heading-item"  href="#"><?php echo __("Top Left" ,"site-editor");  ?></a></li>

       </ul></li></ul>
-->
