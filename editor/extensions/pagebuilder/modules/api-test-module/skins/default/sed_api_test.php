<div <?php echo $sed_attrs; ?> class="sed-api-test module module-api-test-module api-test-module-skin-default sed-sas-md <?php echo esc_attr( $class ); ?> <?php echo esc_attr( $length_class );?>" length_element="sed-row-wide" >
    <div>
    <h3>Attribute test</h3>
    <div>
        <br>
        <div><h4 class="attr">Text Box Settings</h4></div>
        <div><span class="attr">Text Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $text_field_attr; ?></span></div>
        <div><span class="attr">Tel Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $tel_field_attr; ?></span></div>
        <div><span class="attr">Password Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $pass_field_attr; ?></span></div>
        <div><span class="attr">Search Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $search_field_attr; ?></span></div>
        <div><span class="attr">Url Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $url_field_attr; ?></span></div>
        <div><span class="attr">Email Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $email_field_attr; ?></span></div>
        <div><span class="attr">Date Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $date_field_attr; ?></span></div>
        <div><span class="attr">Dimension Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $dimension_field_attr; ?></span></div>
        <div><span class="attr">Textarea Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $textarea_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Select Settings</h4></div>
        <div><span class="attr">Single Select Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $single_select_field_attr; ?></span></div>
        <div><span class="attr">Multiple Select Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $multi_select_field_attr; ?></span></div>
        <div><span class="attr">optgroup Single Select Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $og_single_select_field_attr; ?></span></div>
        <div><span class="attr">optgroup multi Select Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $og_multi_select_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Check Box Settings</h4></div>
        <div><span class="attr">Checkbox Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $checkbox_field_attr; ?></span></div>
        <div><span class="attr">Multi Checkbox Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $multi_check_field_attr; ?></span></div>
        <div><span class="attr">Toggle Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $toggle_field_id; ?></span></div>
        <div><span class="attr">Sortable Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $sortable_field_id; ?></span></div>
        <div><span class="attr">Switch Control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $switch_field_id; ?></span></div> 

        <br>
        <div><h4 class="attr">Radio Settings</h4></div>
        <div><span class="attr">Radio Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $radio_field_attr; ?></span></div> 
        <div><span class="attr">Radio Buttonset control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $radio_buttonset_field_id; ?></span></div> 
        <div><span class="attr">Radio Image control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $radio_image_field_id; ?></span></div>

        <br>
        <div><h4 class="attr">Color Settings</h4></div>
        <div><span class="attr">Color Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $color_field_attr; ?></span></div>
        <div><span class="attr">Style Editor Color Box:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style-color-test">this is style editor settings</span></div>
        <?php /*<!--<div><span class="attr">Multicolor control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php //var_dump($multi_color_field_id); ?></span></div> -->
        <!--<div><span class="attr">Multicolor control:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="value">
                <#
                    if( !_.isEmpty( multi_color_field_id ) && _.isObject( multi_color_field_id ) ){
                        _.each( multi_color_field_id ,function( value , prop ){ #>

                        <br/><span> Property : {{prop}} </span> <span> Value : {{value}} </span>

                    <#   });
                    }

                #>
            </span>
        </div>--> */ ?>

        <br>
        <div><h4 class="attr">Media Settings</h4></div>

        <div>
	        <div><span class="attr">SED Image Field:</span></div>
	        <br>
	        <div><span class="value">
                    <?php
                    $img = false;

                    switch ( $image_source ) {
                        case "attachment":
                            $img = get_sed_attachment_image_html( $attachment_id , $default_image_size , $custom_image_size );
                            break;
                        case "external":
                            $img = get_sed_external_image_html( $image_url , $external_image_size );
                            break;

                    }

                    if ( ! $img ) {
                        $img = array();
                        $img['thumbnail'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
                        $img['large_img'] = '<img class="sed-image-placeholder sed-image" src="' . sed_placeholder_img_src() . '" />';
                    }

                    echo $img['thumbnail'];

                    ?>
                </span>
            </div>
	        <br>
        </div>

        <div>
            <div>
                <div><span class="attr">Single Image Field:</span></div>
                <br>
                <?php
                $img = get_sed_attachment_image_html( $image_field_attr , $image_size_field_attr );
                echo $img['thumbnail'];
                ?>
            </div>
            <br>
        </div>

        <div>
            <div><span class="attr">Select Images Field:</span></div>
            <br>
            <div class="images-group">
            <?php

            foreach( $gallery AS $attachment_id ){

                    ?> <span>
                        <?php
                        $img = get_sed_attachment_image_html( $attachment_id , 'thumbnail' );
                        echo $img['thumbnail'];
                        ?>
                    </span> <?php

            }
            ?>
            </div>
            <br>
        </div>

        <div>
            <span class="attr">Video Field (MP4):</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="value">ID : <?php echo $video_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url :
                <?php

                if( $video_field_attr > 0 ){
                    if( get_post( $video_field_attr ) ) {
                        echo wp_get_attachment_url( $video_field_attr );
                    }
                }

                ?>
            </span>
        </div>

        <div><span class="attr">Audio Field (MP3):</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $audio_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;<span class="value">Url :
                <?php

                if( $audio_field_attr > 0 ){
                    if( get_post( $audio_field_attr ) ) {
                        echo wp_get_attachment_url( $audio_field_attr );
                    }
                }

                ?>
            </span>
        </div>
        <div><span class="attr">File Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value">ID : <?php echo $file_field_attr; ?> </span>&nbsp;&nbsp;&nbsp;
            <span class="value">
                Url :
                <?php

                if( $file_field_attr > 0 ){
                    if( get_post( $file_field_attr ) ) {
                        echo wp_get_attachment_url( $file_field_attr );
                    }
                }

                ?>
            </span>
        </div>

        <br>
        <div><h4 class="attr">Number Settings</h4></div>
        <div><span class="attr">Spinner Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner_field_attr; ?></span></div>
        <div><span class="attr">Spinner1 with lock:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner1_with_lock_attr; ?></span></div>
        <div><span class="attr">Spinner2 with lock:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner2_with_lock_attr; ?></span></div>
        <div><span class="attr">Spinner3 with lock:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner3_with_lock_attr; ?></span></div>
        <div><span class="attr">Spinner Lock Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $spinner_lock_attr; ?></span></div>
        <div><span class="attr">Range Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $range_field_attr; ?></span></div>

        <br>
        <div><h4 class="attr">Icon Settings</h4></div>
        <div><span class="attr">Icon Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><span class="my-icon-single <?php echo $icon_field_attr; ?>"></span></span></div>
        <div>
            <div><span class="attr">Select Icons Field</span></div>
            <br>
            <div class="icons-group">
                <?php

                $iconsGroup = is_string( $multi_icon_field_attr ) ? explode( "," , $multi_icon_field_attr ) : $multi_icon_field_attr;

                $iconsGroup = is_array( $iconsGroup ) ? $iconsGroup : array();

                foreach( $iconsGroup AS $gIcon ){

                    ?><span><span class="icon-group-single <?php echo $gIcon; ?>"></span></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php

                }

                ?>
            </div>
            <br>
        </div>

        <br>
        <div><h4 class="attr">Custom Settings</h4></div>
        <div><span class="attr">Custom Dropdown Field:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="value"><?php echo $custom_attr; ?></span></div>
    </div>

    </div>
      <?php echo $content; ?>
</div>