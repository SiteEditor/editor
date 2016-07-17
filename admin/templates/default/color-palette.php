<div class="sed-color-palettes">
    <ul>
        <li>
            <label for="<?php echo $id."_custom_palette";?>">
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="custom" id="<?php echo $id."_custom_palette";?>" <?php if( $value == "custom" ) echo "checked='checked'" ?> />
                    <span>custom palette</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_custom_palette">
                        <li class="sed-palette1"></li>
                        <li class="sed-palette2"></li>
                        <li class="sed-palette3"></li>
                        <li class="sed-palette4"></li>
                        <li class="sed-palette5"></li>
                    </ul>
                </div>
            </label>
        </li>

        <li>
            <label for="<?php echo $id."_palette1";?>" >
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="#B94D2D,#AED429,#FFFFFF,#CCCCCC,#000000" id="<?php echo $id."_palette1";?>" <?php if( $value == "palette1" ) echo "checked='checked'" ?> />
                    <span>palette 1</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_color_palette1">
                        <li class="sed-palette1" style="background-color:#B94D2D;"></li>
                        <li class="sed-palette2" style="background-color:#AED429;"></li>
                        <li class="sed-palette3" style="background-color:#FFFFFF;"></li>
                        <li class="sed-palette4" style="background-color:#CCCCCC;"></li>
                        <li class="sed-palette5" style="background-color:#000000;"></li>
                    </ul>
                </div>
            </label>
        </li>

        <li>
            <label for="<?php echo $id."_palette2";?>" >
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="#FF6600,#66CC33,#FFFFFF,#CCCCCC,#000000" id="<?php echo $id."_palette2";?>" <?php if( $value == "palette2" ) echo "checked='checked'" ?> />
                    <span>palette 2</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_color_palette2">
                        <li class="sed-palette1" style="background-color:#FF6600;"></li>
                        <li class="sed-palette2" style="background-color:#66CC33;"></li>
                        <li class="sed-palette3" style="background-color:#FFFFFF;"></li>
                        <li class="sed-palette4" style="background-color:#CCCCCC;"></li>
                        <li class="sed-palette5" style="background-color:#000000;"></li>
                    </ul>
                </div>
            </label>
        </li>

        <li>
            <label for="<?php echo $id."_palette3";?>" >
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="#a0ce4e,#EB2D1C,#FFFFFF,#CCCCCC,#000000" id="<?php echo $id."_palette3";?>" <?php if( $value == "#a0ce4e,#F77818,#FFFFFF,#CCCCCC,#000000" ) echo "checked='checked'" ?> />
                    <span>palette 3</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_color_palette3">
                        <li class="sed-palette1" style="background-color:#a0ce4e;"></li>
                        <li class="sed-palette2" style="background-color:#EB2D1C;"></li>
                        <li class="sed-palette3" style="background-color:#FFFFFF;"></li>
                        <li class="sed-palette4" style="background-color:#CCCCCC;"></li>
                        <li class="sed-palette5" style="background-color:#000000;"></li>
                    </ul>
                </div>
            </label>                                               
        </li>

        <li>
            <label for="<?php echo $id."_palette4";?>" >
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="#009BF5,#f6c113,#FFFFFF,#CCCCCC,#000000" id="<?php echo $id."_palette4";?>" <?php if( $value == "#F77818,#00aaff,#000000,#282828,#ffffff" ) echo "checked='checked'" ?> />
                    <span>palette 4</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_color_palette4">
                        <li class="sed-palette1" style="background-color:#009BF5;"></li>
                        <li class="sed-palette2" style="background-color:#f6c113;"></li>
                        <li class="sed-palette3" style="background-color:#FFFFFF;"></li>
                        <li class="sed-palette4" style="background-color:#CCCCCC;"></li>
                        <li class="sed-palette5" style="background-color:#000000;"></li>
                    </ul>
                </div>
            </label>
        </li>

        <li>
            <label for="<?php echo $id."_palette5";?>" >
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="#C0029A,#00C5CE,#FFFFFF,#CCCCCC,#000000" id="<?php echo $id."_palette5";?>" <?php if( $value == "palette5" ) echo "checked='checked'" ?> />
                    <span>palette 5</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_color_palette5">
                        <li class="sed-palette1" style="background-color:#C0029A;"></li>
                        <li class="sed-palette2" style="background-color:#00C5CE;"></li>
                        <li class="sed-palette3" style="background-color:#FFFFFF;"></li>
                        <li class="sed-palette4" style="background-color:#CCCCCC;"></li>
                        <li class="sed-palette5" style="background-color:#000000;"></li>
                    </ul>
                </div>
            </label>
        </li>

        <li>
            <label for="<?php echo $id."_palette6";?>" >
                <div class="sed-palette-text">
                    <input type="radio" name="<?php echo $id;?>" value="#6683A3,#B94D2D,#FFFFFF,#CCCCCC,#000000" id="<?php echo $id."_palette6";?>" <?php if( $value == "palette6" ) echo "checked='checked'" ?> />
                    <span>palette 6</span>
                </div>
                <div class="sed_color_palette">
                    <ul id="sed_color_palette6">
                        <li class="sed-palette1" style="background-color:#6683A3;"></li>
                        <li class="sed-palette2" style="background-color:#B94D2D;"></li>
                        <li class="sed-palette3" style="background-color:#FFFFFF;"></li>
                        <li class="sed-palette4" style="background-color:#CCCCCC;"></li>
                        <li class="sed-palette5" style="background-color:#000000;"></li>
                    </ul>
                </div>
            </label>
        </li>
    </ul>
</div>