<?php
class SiteEditorCss{
    public $properties = array();

    public $settings_properties = array();

    public $lib_base_url;

    function __construct( ) {
        $this->set_default_properties();
        $this->lib_base_url = SED_EDITOR_FOLDER_URL . "libraries/";
    }

    function set_default_properties( ) {
        $this->properties = array(
            //Color Properties
            'color', 	//Sets the color of text 	//CSS1
            'opacity',    //	Sets the opacity level for an element 	//CSS3

            //Background and Border Properties
            'background',	//Sets all the background properties in one declaration 	//CSS1
            'background-attachment',	//Sets whether a background image is fixed or scrolls with the rest of the page 	//CSS1
            'background-color',       //Sets the background color of an element'       //CSS1
            'background-image',       //Sets the background image for an element',       //CSS1
            'background-position',       //Sets the starting position of a background image',       //CSS1
            'background-repeat',       //Sets how a background image will be repeated',       //CSS1
            'background-clip',       //Specifies the painting area of the background',       //CSS3
            'background-origin',       //Specifies the positioning area of the background images',       //CSS3
            'background-size',       //Specifies the size of the background images',       //CSS3
            'border',       //Sets all the border properties in one declaration',       //CSS1
            'border-bottom',       //Sets all the bottom border properties in one declaration',       //CSS1
            'border-bottom-color',       //Sets the color of the bottom border',       //CSS1
            'border-bottom-left-radius',       //Defines the shape of the border of the bottom-left corner',       //CSS3
            'border-bottom-right-radius',       //Defines the shape of the border of the bottom-right corner',       //CSS3
            'border-bottom-style',       //Sets the style of the bottom border',       //CSS1
            'border-bottom-width',       //Sets the width of the bottom border',       //CSS1
            'border-color',       //Sets the color of the four borders',       //CSS1
            'border-image',       //A shorthand property for setting all the border-image-* properties',       //CSS3
            'border-image-outset',       //Specifies the amount by which the border image area extends beyond the border box',       //CSS3
            'border-image-repeat',       //Specifies whether the image-border should be repeated, rounded or stretched',       //CSS3
            'border-image-slice',       //Specifies the inward offsets of the image-border',       //CSS3
            'border-image-source',       //Specifies an image to be used as a border',       //CSS3
            'border-image-width',       //Specifies the widths of the image-border',       //CSS3
            'border-left',       //Sets all the left border properties in one declaration',       //CSS1
            'border-left-color',       //Sets the color of the left border',       //CSS1
            'border-left-style',       //Sets the style of the left border',       //CSS1
            'border-left-width',       //Sets the width of the left border',       //CSS1
            'border-radius',       //A shorthand property for setting all the four border-*-radius properties',       //CSS3
            'border-right',       //Sets all the right border properties in one declaration',       //CSS1
            'border-right-color',       //Sets the color of the right border',       //CSS1
            'border-right-style',       //Sets the style of the right border',       //CSS1
            'border-right-width',       //Sets the width of the right border',       //CSS1
            'border-style',       //Sets the style of the four borders',       //CSS1
            'border-top',       //Sets all the top border properties in one declaration',       //CSS1
            'border-top-color',       //Sets the color of the top border',       //CSS1
            'border-top-left-radius',       //Defines the shape of the border of the top-left corner',       //CSS3
            'border-top-right-radius',       //Defines the shape of the border of the top-right corner',       //CSS3
            'border-top-style',       //Sets the style of the top border',       //CSS1
            'border-top-width',       //Sets the width of the top border',       //CSS1
            'border-width',       //Sets the width of the four borders',       //CSS1
            'box-decoration-break',       //Sets the behaviour of the background and border of an element at page-break, or, for in-line elements, at line-break.',       //CSS3
            'box-shadow',       //Attaches one or more drop-shadows to the box',       //CSS3

            //Basic Box Properties
            'bottom',       //Specifies the bottom position of a positioned element',       //CSS2
            'clear',       //Specifies which sides of an element where other floating elements are not allowed',       //CSS1
            'clip',       //Clips an absolutely positioned element',       //CSS2
            'display',       //Specifies how a certain HTML element should be displayed',       //CSS1
            'float',       //Specifies whether or not a box should float',       //CSS1
            'height',       //Sets the height of an element',       //CSS1
            'left',       //Specifies the left position of a positioned element',       //CSS2
            'overflow',     //Specifies what happens if content overflows an element's box',       //CSS2
            'overflow-x',       //Specifies whether or not to clip the left/right edges of the content, if it overflows the element's content area',       //CSS3
            'overflow-y',       //Specifies whether or not to clip the top/bottom edges of the content, if it overflows the element's content area',       //CSS3
            'padding',       //Sets all the padding properties in one declaration',       //CSS1
            'padding-bottom',       //Sets the bottom padding of an element',       //CSS1
            'padding-left',       //Sets the left padding of an element',       //CSS1
            'padding-right',       //Sets the right padding of an element',       //CSS1
            'padding-top',       //Sets the top padding of an element',       //CSS1
            'position',       //Specifies the type of positioning method used for an element (static, relative, absolute or fixed)',       //CSS2
            'right',       //Specifies the right position of a positioned element',       //CSS2
            'top',       //Specifies the top position of a positioned element',       //CSS2
            'visibility',       //Specifies whether or not an element is visible',       //CSS2
            'width',       //Sets the width of an element',       //CSS1
            'vertical-align',       //Sets the vertical alignment of an element',       //CSS1
            'z-index',       //Sets the stack order of a positioned element',       //CSS2

            //Flexible Box Layout
            'align-content',       //Specifies the alignment between the lines inside a flexible container when the items do not use all available space.',       //CSS3
            'align-items',       //Specifies the alignment for items inside a flexible container.',       //CSS3
            'align-self',       //Specifies the alignment for selected items inside a flexible container.',       //CSS3
            'display',       //Specifies how a certain HTML element should be displayed',       //CSS1
            'flex',       //Specifies the length of the item, relative to the rest',       //CSS3
            'flex-basis',       //Specifies the initial length of a flexible item',       //CSS3
            'flex-direction',       //Specifies the direction of the flexible items',       //CSS3
            'flex-flow',       //A shorthand property for the flex-direction and the flex-wrap properties',       //CSS3
            'flex-grow',       //Specifies how much the item will grow relative to the rest',       //CSS3
            'flex-shrink',       //Specifies how the item will shrink relative to the rest',       //CSS3
            'flex-wrap',       //Specifies whether the flexible items should wrap or not',       //CSS3
            'justify-content',       //Specifies the alignment between the items inside a flexible container when the items do not use all available space.',       //CSS3
            'margin',       //Sets all the margin properties in one declaration',       //CSS1
            'margin-bottom',       //Sets the bottom margin of an element',       //CSS1
            'margin-left',       //Sets the left margin of an element',       //CSS1
            'margin-right',       //Sets the right margin of an element',       //CSS1
            'margin-top',       //Sets the top margin of an element',       //CSS1
            'max-height',       //Sets the maximum height of an element',       //CSS2
            'max-width',       //Sets the maximum width of an element',       //CSS2
            'min-height',       //Sets the minimum height of an element',       //CSS2
            'min-width',       //Sets the minimum width of an element',       //CSS2
            'order',       //Sets the order of the flexible item, relative to the rest',       //CSS3

            //Text Properties
            'hanging-punctuation',       //Specifies whether a punctuation character may be placed outside the line box',       //CSS3
            'hyphens',       //Sets how to split words to improve the layout of paragraphs',       //CSS3
            'letter-spacing',       //Increases or decreases the space between characters in a text',       //CSS1
            'line-break',       //	3
            'line-height',       //Sets the line height',       //CSS1
            'overflow-wrap',       //	3
            'tab-size',       //Specifies the length of the tab-character',       //CSS3
            'text-align',       //Specifies the horizontal alignment of text',       //CSS1
            'text-align-last',       //Describes how the last line of a block or a line right before a forced line break is aligned when text-align is "justify"',       //CSS3
            'text-indent',       //Specifies the indentation of the first line in a text-block',       //CSS1
            'text-justify',       //Specifies the justification method used when text-align is "justify"',       //CSS3
            'text-transform',       //Controls the capitalization of text',       //CSS1
            'white-space',       //Specifies how white-space inside an element is handled',       //CSS1
            'word-break',       //Specifies line breaking rules for non-CJK scripts',       //CSS3
            'word-spacing',       //Increases or decreases the space between words in a text',       //CSS1
            'word-wrap',       //Allows long, unbreakable words to be broken and wrap to the next line',       //CSS3

            //Text Decoration Properties
            'text-decoration',       //Specifies the decoration added to text',       //CSS1
            'text-decoration-color',       //Specifies the color of the text-decoration',       //CSS3
            'text-decoration-line',       //Specifies the type of line in a text-decoration',       //CSS3
            'text-decoration-style',       //Specifies the style of the line in a text decoration',       //CSS3
            'text-shadow',       //Adds shadow to text',       //CSS3
            'text-underline-position',       //	3

            //Font Properties
            'font',       //Sets all the font properties in one declaration',       //CSS1
            'font-family',       //Specifies the font family for text',       //CSS1
            'font-feature-setting',       //	3
            '@font-feature-values',       //	3
            'font-kerning',       //	3
            'font-language-override',       //	3
            'font-synthesis',       //	3
            'font-variant-alternates',       //	3
            'font-variant-caps',       //	3
            'font-variant-east-asian',       //	3
            'font-variant-ligatures',       //	3
            'font-variant-numeric',       //	3
            'font-variant-position',       //	3
            'font-size',       //Specifies the font size of text',       //CSS1
            'font-style',       //Specifies the font style for text',       //CSS1
            'font-variant',       //Specifies whether or not a text should be displayed in a small-caps font',       //CSS1
            'font-weight',       //Specifies the weight of a font',       //CSS1
            '@font-face',       //A rule that allows websites to download and use fonts other than the "web-safe" fonts',       //CSS3
            'font-size-adjust',       //Preserves the readability of text when font fallback occurs',       //CSS3
            'font-stretch',       //Selects a normal, condensed, or expanded face from a font family',       //CSS3

            //Writing Modes Properties
            'direction',       //Specifies the text direction/writing direction',       //CSS2
            'text-orientation',       //	3
            'text-combine-horizontal',       //	3
            'unicode-bidi',       //Used together with the direction property to set or return whether the text should be overridden to support multiple languages in the same document',       //CSS2
            'writing-mode',       //	3

            //Table Properties
            'border-collapse',       //Specifies whether or not table borders should be collapsed',       //CSS2
            'border-spacing',       //Specifies the distance between the borders of adjacent cells',       //CSS2
            'caption-side',       //Specifies the placement of a table caption',       //CSS2
            'empty-cells',       //Specifies whether or not to display borders and background on empty cells in a table',       //CSS2
            'table-layout',       //Sets the layout algorithm to be used for a table',       //CSS2

            //Lists and Counters Properties
            'counter-increment',       //Increments one or more counters',       //CSS2
            'counter-reset',       //Creates or resets one or more counters',       //CSS2
            'list-style',       //Sets all the properties for a list in one declaration',       //CSS1
            'list-style-image',       //Specifies an image as the list-item marker',       //CSS1
            'list-style-position',       //Specifies if the list-item markers should appear inside or outside the content flow',       //CSS1
            'list-style-type',       //Specifies the type of list-item marker',       //CSS1

            //Animation Properties
            '@keyframes',       //Specifies the animation',       //CSS3
            'animation',       //A shorthand property for all the animation properties below, except the animation-play-state property',       //CSS3
            'animation-delay',       //Specifies when the animation will start',       //CSS3
            'animation-direction',       //Specifies whether or not the animation should play in reverse on alternate cycles',       //CSS3
            'animation-duration',       //Specifies how many seconds or milliseconds an animation takes to complete one cycle',       //CSS3
            'animation-fill-mode',       //Specifies what values are applied by the animation outside the time it is executing',       //CSS3
            'animation-iteration-count',       //Specifies the number of times an animation should be played',       //CSS3
            'animation-name',       //Specifies a name for the @keyframes animation',       //CSS3
            'animation-timing-function',       //Specifies the speed curve of the animation',       //CSS3
            'animation-play-state',       //Specifies whether the animation is running or paused',       //CSS3

            //Transform Properties
            'backface-visibility',       //Defines whether or not an element should be visible when not facing the screen',       //CSS3
            'perspective',       //Specifies the perspective on how 3D elements are viewed',       //CSS3
            'perspective-origin',       //Specifies the bottom position of 3D elements',       //CSS3
            'transform',       //Applies a 2D or 3D transformation to an element',       //CSS3
            'transform-origin',       //Allows you to change the position on transformed elements',       //CSS3
            'transform-style',       //Specifies how nested elements are rendered in 3D space',       //CSS3

            //Transitions Properties
            'transition',       //A shorthand property for setting the four transition properties',       //CSS3
            'transition-property',       //Specifies the name of the CSS property the transition effect is for',       //CSS3
            'transition-duration',       //Specifies how many seconds or milliseconds a transition effect takes to complete',       //CSS3
            'transition-timing-function',       //Specifies the speed curve of the transition effect',       //CSS3
            'transition-delay',       //Specifies when the transition effect will start',       //CSS3

            //Basic User Interface Properties
            'box-sizing',       //Allows you to define certain elements to fit an area in a certain way',       //CSS3
            'content',       //Used with the :before and :after pseudo-elements, to insert generated content',       //CSS2
            'cursor',       //Specifies the type of cursor to be displayed',       //CSS2
            'icon',       //Provides the author the ability to style an element with an iconic equivalent',       //CSS3
            'ime-mode',       //	3
            'nav-down',       //Specifies where to navigate when using the arrow-down navigation key',       //CSS3
            'nav-index',       //Specifies the tabbing order for an element',       //CSS3
            'nav-left',       //Specifies where to navigate when using the arrow-left navigation key',       //CSS3
            'nav-right',       //Specifies where to navigate when using the arrow-right navigation key',       //CSS3
            'nav-up',       //Specifies where to navigate when using the arrow-up navigation key',       //CSS3
            'outline',       //Sets all the outline properties in one declaration',       //CSS2
            'outline-color',       //Sets the color of an outline',       //CSS2
            'outline-offset',       //Offsets an outline, and draws it beyond the border edge',       //CSS3
            'outline-style',       //Sets the style of an outline',       //CSS2
            'outline-width',       //Sets the width of an outline',       //CSS2
            'resize',       //Specifies whether or not an element is resizable by the user',       //CSS3
            'text-overflow',       //Specifies what should happen when text overflows the containing element',       //CSS3

            //Multi-column Layout Properties
            'break-after',       //	3
            'break-before',       //	3
            'break-inside',       //	3
            'column-count',       //Specifies the number of columns an element should be divided into',       //CSS3
            'column-fill',       //Specifies how to fill columns',       //CSS3
            'column-gap',       //Specifies the gap between the columns',       //CSS3
            'column-rule',       //A shorthand property for setting all the column-rule-* properties',       //CSS3
            'column-rule-color',       //Specifies the color of the rule between columns',       //CSS3
            'column-rule-style',       //Specifies the style of the rule between columns',       //CSS3
            'column-rule-width',       //Specifies the width of the rule between columns',       //CSS3
            'column-span',       //Specifies how many columns an element should span across',       //CSS3
            'column-width',       //Specifies the width of the columns',       //CSS3
            'columns',       //A shorthand property for setting column-width and column-count',       //CSS3
            'widows',       //Sets the minimum number of lines that must be left at the top of a page when a page break occurs inside an element',       //CSS2

            //Paged Media
            'orphans',       //Sets the minimum number of lines that must be left at the bottom of a page when a page break occurs inside an element',       //CSS2
            'page-break-after',       //Sets the page-breaking behavior after an element',       //CSS2
            'page-break-before',       //Sets the page-breaking behavior before an element',       //CSS2
            'page-break-inside',       //Sets the page-breaking behavior inside an element',       //CSS2

            //Generated Content for Paged Media
            'marks',       //Adds crop and/or cross marks to the document',       //CSS3
            'quotes',       //Sets the type of quotation marks for embedded quotations',       //CSS2

            //Filter Effects Properties
            'filter',       //	3

            //Image Values and Replaced Content
            'image-orientation',       //Specifies a rotation in the right or clockwise direction that a user agent applies to an image',       //CSS3
            'image-rendering',       //	3
            'image-resolution',       //	3
            'object-fit',       //	3
            'object-position',       //	3

            //Masking Properties
            'mask',       //	3
            'mask-type',       //	3

            //Speech Properties
            'mark',       //A shorthand property for setting the mark-before and mark-after properties',       //CSS3
            'mark-after',       //Allows named markers to be attached to the audio stream',       //CSS3
            'mark-before',       //Allows named markers to be attached to the audio stream',       //CSS3
            'phonemes',       //Specifies a phonetic pronunciation for the text contained by the corresponding element',       //CSS3
            'rest',       //A shorthand property for setting the rest-before and rest-after properties',       //CSS3
            'rest-after',       //Specifies a rest or prosodic boundary to be observed after speaking an element's content',       //CSS3
            'rest-before',       //Specifies a rest or prosodic boundary to be observed before speaking an element's content',       //CSS3
            'voice-balance',       //Specifies the balance between left and right channels',       //CSS3
            'voice-duration',       //Specifies how long it should take to render the selected element's content',       //CSS3
            'voice-pitch',       //Specifies the average pitch (a frequency) of the speaking voice',       //CSS3
            'voice-pitch-range',       //Specifies variation in average pitch',       //CSS3
            'voice-rate',       //Controls the speaking rate',       //CSS3
            'voice-stress',       //Indicates the strength of emphasis to be applied',       //CSS3
            'voice-volume',       //Refers to the amplitude of the waveform output by the speech synthesises',       //CSS3

            //Marquee Properties
            'marquee-direction',       //Sets the direction of the moving content',       //CSS3
            'marquee-play-count',       //Sets how many times the content move',       //CSS3
            'marquee-speed',       //Sets how fast the content scrolls',       //CSS3
            'marquee-style'        //Sets the style of the moving content      //CSS3
        );
    }

    function chvv( $property , $element_properties , $type = 1){  //check_valid_value
        switch ($type) {
          case 0:
            return array_key_exists( $property , $element_properties);
          break;
          case 1:
            return array_key_exists( $property , $element_properties) && !empty($element_properties[$property]);
          break;
        }
    }

    function set_background( $element_properties ){
        $output_css = '';
        $bg_repeat = 'no-repeat';
        $bg_size = 'auto';

        if ( $this->chvv('background_color' , $element_properties) )
            $output_css .= 'background-color: ' . $element_properties['background_color'] . ' !important;';

        $bg_image = $this->chvv('background_image' , $element_properties) && $element_properties['background_image'] != "none";
        $gradient = $this->chvv('background_gradient' , $element_properties);

  		if ( $this->chvv('background_image' , $element_properties , 0) && !$gradient ){

            if( empty( $element_properties['background_image'] ) || $element_properties['background_image'] == "none" )
  			    $output_css .= 'background-image: none !important;';
            else
                $output_css .= 'background-image: url("' . $element_properties['background_image'] . '") !important;';

        }else if( !$bg_image && $gradient )
            $output_css .= $this->gradient( $element_properties['background_gradient'] );
        else if( $bg_image && $gradient  )
            $output_css .= $this->gradient( $element_properties['background_gradient'] , $element_properties['background_image'] );
        else if( !$this->chvv('background_image' , $element_properties , 0) && $this->chvv('background_gradient' , $element_properties , 0) && !$element_properties['background_gradient'] )
            $output_css .= 'background-image: none !important;';


  		if ( $bg_image ) {
                                            
            if ( $this->chvv('background_position' , $element_properties) )
  			    $output_css .= 'background-position: ' . $element_properties['background_position'] . ';';

            if ( $this->chvv('background_attachment' , $element_properties) )
                $output_css .= 'background-attachment: ' . $element_properties['background_attachment'] . ' !important;';

            if( $this->chvv('background_image_scaling' , $element_properties) ) {
                  switch ( $element_properties['background_image_scaling'] ) {
                       case "fullscreen":
                            $bg_size = "100% 100%";
                       break;
                       case "fit":
                            $bg_size = "100% auto";
                            $bg_repeat = "repeat-y";
                       break;
                       case "tile":
                            $bg_size = "auto";
                            $bg_repeat = "repeat";
                       break;
                       case "tile-horizontally":
                            $bg_size = "auto";
                            $bg_repeat = "repeat-x";
                       break;
                       case "tile-vertically":
                            $bg_size = "auto";
                            $bg_repeat = "repeat-y";
                       break;
                       case "normal":
                            $bg_size = "auto";
                            $bg_repeat = "no-repeat";
                       break;
                       case "cover":
                            $bg_size = "cover";
                       break;
                  }

                  $output_css .= 'background-repeat: ' . $bg_repeat . ' !important;';
                  $output_css .= $this->background_size( $bg_size );
            }

  		}

        if($this->chvv('background_gradient' , $element_properties) || $this->chvv('background_image_scaling' , $element_properties))
            $output_css .= "behavior: url(" . $this->lib_base_url . "PIE/PIE.htc" . ");";

        return $output_css;
    }

    function set_border( $element_properties ){
        $output_css = '';
        $border_sides = array("top" , "bottom" , "left" , "right");

        foreach($border_sides AS $val){
            if( $val == "right" && is_rtl() )
                $new_val = "left";
            elseif( $val == "left" && is_rtl() )
                $new_val = "right";
            else
                $new_val = $val;

            if( $this->chvv('border_'.$val.'_style' , $element_properties) ){
                $output_css .= "border-" . $new_val . "-style :" . $element_properties['border_'.$val.'_style'] . " !important;";
            }

            if( $this->chvv('border_'.$val.'_width' , $element_properties , 0) ){
                $output_css .= "border-" . $new_val . "-width :" . $element_properties['border_'.$val.'_width'] . "px !important;";
            }

            if( $this->chvv('border_'.$val.'_color' , $element_properties) ){
                $output_css .= "border-" . $new_val . "-color :" . $element_properties['border_'.$val.'_color'] . " !important;";
            }

        }
        return $output_css;
    }

    function box_shadow( $shadow , $behavior = true){
        $output_css  =  "-webkit-box-shadow: " . $shadow . " !important;"
                        ."-moz-box-shadow: " . $shadow . " !important;"
                        ."box-shadow: " . $shadow . " !important;";
        if($behavior === true)
            $output_css .= "behavior: url(" . $this->lib_base_url . "PIE/PIE.htc);";
        return $output_css;
    }

    function text_shadow( $element_properties ){
        $output_css  =  "";
        $text_shadow = "";

		if (  $this->chvv('text_shadow' , $element_properties) )
            $text_shadow = $element_properties['text_shadow'];

        if ( $this->chvv('text_shadow_color' , $element_properties) && $this->chvv('text_shadow' , $element_properties) )
            $text_shadow .= " " . $element_properties['text_shadow_color'];

        if ( !empty( $text_shadow ) )
            $output_css = 'text-shadow: ' . $text_shadow . ' !important;';

        return $output_css;
    }

    function trancparency( $opacity ){
        $opacity2 = $opacity / 100;
        $output_css = "zoom: 1 !important;"
                      ."-ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=" . $opacity . ") !important;"
                      ."filter: alpha(opacity=" . $opacity . ") !important;"
                      ."-moz-opacity: " . $opacity2 . " !important;"
                      ."-khtml-opacity: " . $opacity2 . " !important;"
                      ."opacity: " . $opacity2 . " !important;";
        return $output_css;
    }

    function border_radius( $sizes , $behavior = true ){
        $output_css  =  "-webkit-border-radius: " . $sizes . " !important;"
                        ."-moz-border-radius: " . $sizes . " !important;"
                        ."border-radius: " . $sizes . " !important;";
        if($behavior === true)
            $output_css .= "behavior: url(" . $this->lib_base_url . "PIE/PIE.htc);";
        return $output_css;
    }

    function sed_border_radius( $size , $side , $unit = "px" , $behavior = true ){

        switch ( strtolower( $side ) ) {
            case "tl":
                $cornerSide = ( is_rtl() ) ? "top-right" : "top-left";
            break;
            case "tr":
                $cornerSide = ( is_rtl() ) ? "top-left" : "top-right";
            break;
            case "bl":
                $cornerSide = ( is_rtl() ) ? "bottom-right" : "bottom-left";
            break;
            case "br":
                $cornerSide = ( is_rtl() ) ? "bottom-left" : "bottom-right";
            break;
        }

        $output_css  =  "-webkit-border-". $cornerSide ."-radius: " . $size . $unit . " !important;"
                        ."-moz-border-". $cornerSide ."-radius: " . $size . $unit . " !important;"
                        ."border-". $cornerSide ."-radius: " . $size . $unit . " !important;";
        if($behavior === true)
            $output_css .= "behavior: url(" . $this->lib_base_url . "PIE/PIE.htc);";
        return $output_css;
    }

    function length( $length ){
        $output_css = '';
        return $output_css;
    }

    function background_size( $size ){
        $output_css  = "-moz-background-size:" . $size . " !important;";
        $output_css .= "-o-background-size:" . $size . " !important;";
        $output_css .= "-webkit-background-size:" . $size . " !important;";
        $output_css .= "background-size:" . $size . " !important;";
        return $output_css;
    }

    function add_opacity_to_rgb( $color , $opacity){
        $rgbColor = str_replace('rgb(','', $color);
        $rgbColor = str_replace(')','', $rgbColor);
        $rgbColor  = 'rgba(' . $rgbColor . ',' . $opacity . ')';
        return $rgbColor;
    }

    function rgb2hex( $color ){
        $rgbColor = str_replace('rgb(','', $color);
        $rgbColor = str_replace(')','', $rgbColor);
        $rgbColor = explode(',' , $rgbColor);
        $r = (int)$rgbColor[0];
        $g = (int)$rgbColor[1];
        $b = (int)$rgbColor[2];
        return "#" . $this->number2hex($r) . $this->number2hex($g) . $this->number2hex($b);
   }

    function number2hex($number) {
        $hex = dechex( $number );
        return strlen($hex) == 1 ? "0" . $hex : $hex;
    }

    function gradient( $gradient , $img_src = '' ){

        if(!is_array( $gradient ) || !$this->chvv( 'type' , $gradient , 0) || !$this->chvv( 'opacity' , $gradient , 0) || !$this->chvv( 'percent' , $gradient , 0)
            || !$this->chvv( 'orientation' , $gradient , 0) || !$this->chvv( 'start' , $gradient , 0) || !$this->chvv( 'end' , $gradient , 0)){
            return false;
        }

        $start_color =  $gradient['start'];
        $end_color = $gradient['end'];
        $type = $gradient['type'];
        $opacity = $gradient['opacity'];
        $percent = $gradient['percent'];
        $orientation = $gradient['orientation'];

        if( !empty($type) ){
            $gtype = $type;
        }else{
            $gtype = "linear";
        }

        $start_hex_color = $this->rgb2hex( $start_color );
        $end_hex_color = $this->rgb2hex( $end_color );

        if( $opacity ){
            $gopacity = $opacity;
            $gopacity = explode("," , $gopacity);
            $start_color = $this->add_opacity_to_rgb($start_color , $gopacity[0]);
            $end_color = $this->add_opacity_to_rgb($end_color , $gopacity[1]);
        }else{
            $gopacity = "";
        }


        if( $percent  ){
            $gpercent = $percent;
        }else{
            $gpercent = "0,100";
        }

        $gpercent = explode("," , $gpercent);
        $gpercent[0] = $gpercent[0] . "%";
        $gpercent[1] = $gpercent[1] . "%";

        switch ( $orientation ) {
          case "horizontal":
            $gposition = "left";
            $webkit_gposition = "left top, right top";
            $w3c_gposition = "to right";
            $gradient_type = 1;
          break;
          case "vertical":
            $gposition = "top";
            $webkit_gposition = "left top, right bottom";
            $w3c_gposition = "to bottom";
            $gradient_type = 0;
          break;
          case "diagonal-rb":
            $gposition = "-45deg";
            $webkit_gposition = "left top, left bottom";
            $w3c_gposition = "135deg";
            $gradient_type = 1;
          break;
          case "diagonal-rt":
            $gposition = "45deg";
            $webkit_gposition = "left bottom, right top";
            $w3c_gposition = "45deg";
            $gradient_type = 1;
          break;
          case "radial":
            $gposition = "center, ellipse cover";
            $webkit_gposition = "center center, 0px, center center, 100%";
            $w3c_gposition = "ellipse at center";
            $gradient_type = 1;
          break;
          default:
            $gposition = "top";
            $webkit_gposition = "left top, right bottom";
            $w3c_gposition = "to bottom";
            $gradient_type = 1;
        }


        $moz_gradient =  "-moz-" . $gtype . "-gradient(" . $gposition . " , " . $start_color . " " . $gpercent[0] . " , " . $end_color . " " . $gpercent[1] . ") !important; /* FF3.6. */";

        $webkit_gradient =  "-webkit-gradient(" . $gtype . " , " . $webkit_gposition . " , " . "color-stop(" . $gpercent[0] . "," . $start_color . "), ". " color-stop(" . $gpercent[1] . "," . $end_color . ") ) !important; /* Chrome,Safari4. */";

        $webkit_new_gradient =  "-webkit-" . $gtype . "-gradient(" . $gposition . " , " . $start_color . " " . $gpercent[0] . " , " . $end_color . " " . $gpercent[1] . ") !important; /* Chrome10.,Safari5.1. */";

        $opera_gradient =  "-o-" . $gtype . "-gradient(" . $gposition . " , " . $start_color . " " . $gpercent[0] . " , " . $end_color . " " . $gpercent[1] . ") !important; /* Opera 12. */";

        $msie10_gradient =  "-ms-" . $gtype . "-gradient(" . $gposition . " , " . $start_color . " " . $gpercent[0] . " , " . $end_color . " " . $gpercent[1] . ") !important; /* IE10. */";

        $w3c_gradient =  $gtype . "-gradient(" . $w3c_gposition . " , " . $start_color . " " . $gpercent[0] . " , " . $end_color . " " . $gpercent[1] . ") !important; /* W3C */";

        $msie8_gradient =  "progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $start_hex_color . "', endColorstr='" . $end_hex_color . "',GradientType=" . $gradient_type . " ) !important; /* IE6-8 fallback on horizontal gradient */";

        if($img_src)
            $img_src = 'url("' . $img_src . '") , ';
        else
            $img_src = '';

        $output_css  =  "background: " . $start_color . " " . $img_src . " !important; /* Old browsers */"
                        .  "background:" . $img_src . $moz_gradient
                        .  "background:" . $img_src . $webkit_gradient
                        .  "background:" . $img_src . $webkit_new_gradient
                        .  "background:" . $img_src . $opera_gradient
                        .  "background:" . $img_src . $msie10_gradient
                        .  "background:" . $img_src . $w3c_gradient
                        .  "-pie-background:" . $img_src . $w3c_gradient
                        .  "filter:" . $msie8_gradient ;
        return $output_css;
    }

    function output_standard_css( $element_styles ){

        $element_properties = array();
        foreach( $element_styles AS $style){
            $element_properties[ $style['property'] ] = $style['value'];
        }

        $output_css = '';

        if( $this->chvv( 'shadow' , $element_properties , 0) ){
            if( !isset( $element_properties['shadow']['values'] ) || empty($element_properties['shadow']['values']) || $element_properties['shadow']['values'] == "none" ){
                $shadow = "none";
            }else{
                $shadow = $element_properties['shadow']['values'];
                if( array_key_exists( 'shadow_color' , $element_properties) ){
                    $shadow .= " ". $element_properties['shadow_color'];
                }
                if( $element_properties['shadow']['inset'] ){
                    $shadow .= " inset";
                }
            }
            $output_css .= $this->box_shadow( $shadow );
        }

        $output_css .= $this->set_background( $element_properties );
        $output_css .= $this->set_border( $element_properties );

        if( $this->chvv( 'trancparency' , $element_properties , 0) ){
            $output_css .= $this->trancparency( $element_properties['trancparency'] );
        }


        if( $this->chvv( 'border_radius_tl' , $element_properties , 0) ){
            $output_css .= $this->sed_border_radius( $element_properties['border_radius_tl'] , "tl" );
        }

        if( $this->chvv( 'border_radius_tr' , $element_properties , 0) ){
            $output_css .= $this->sed_border_radius( $element_properties['border_radius_tr'] , "tr" );
        }

        if( $this->chvv( 'border_radius_br' , $element_properties , 0) ){
            $output_css .= $this->sed_border_radius( $element_properties['border_radius_br'] , "br" );
        }

        if( $this->chvv( 'border_radius_bl' , $element_properties , 0) ){
            $output_css .= $this->sed_border_radius( $element_properties['border_radius_bl'] , "bl" );
        }


        if( $this->chvv( 'margin_top' , $element_properties , 0) ){
            $output_css .= "margin-top : ". $element_properties['margin_top'] ."px !important;";
        }

        if( $this->chvv( 'margin_right' , $element_properties , 0) ){
            $prop_str = ( !is_rtl() ) ? "margin-right : " : "margin-left : ";
            $output_css .= $prop_str. $element_properties['margin_right'] ."px !important;";
        }

        if( $this->chvv( 'margin_bottom' , $element_properties , 0) ){
            $output_css .= "margin-bottom : ". $element_properties['margin_bottom'] ."px !important;";
        }

        if( $this->chvv( 'margin_left' , $element_properties , 0) ){
            $prop_str = ( is_rtl() ) ? "margin-right : " : "margin-left : ";
            $output_css .= $prop_str. $element_properties['margin_left'] ."px !important;";
        }


        if( $this->chvv( 'padding_top' , $element_properties , 0) ){
            $output_css .= "padding-top : ". $element_properties['padding_top'] ."px !important;";
        }

        if( $this->chvv( 'padding_right' , $element_properties , 0) ){
            $prop_str = ( !is_rtl() ) ? "padding-right : " : "padding-left : ";
            $output_css .= $prop_str. $element_properties['padding_right'] ."px !important;";
        }

        if( $this->chvv( 'padding_bottom' , $element_properties , 0) ){
            $output_css .= "padding-bottom : ". $element_properties['padding_bottom'] ."px !important;";
        }

        if( $this->chvv( 'padding_left' , $element_properties , 0) ){
            $prop_str = ( is_rtl() ) ? "padding-right : " : "padding-left : ";
            $output_css .= $prop_str. $element_properties['padding_left'] ."px !important;";
        }

        if( $this->chvv( 'length' , $element_properties) ){
            $output_css .= $this->length( $element_properties['length'] );
        }

        if( $this->chvv( 'position' , $element_properties) ){
            $output_css .= 'position : ' .$element_properties['position'] . ' !important' ;
        }

        if( $this->chvv( 'font_color' , $element_properties) ){
            $output_css .= 'color : ' .$element_properties['font_color'] . ' !important;' ;
        }

        if( $this->chvv( 'font_family' , $element_properties) ){
            $output_css .= 'font-family : ' .$element_properties['font_family'] . ' ;' ;
        }

        if( $this->chvv( 'font_size' , $element_properties , 0) ){
            $output_css .= 'font-size : ' .$element_properties['font_size'] . 'px !important;' ;
        }

        if( $this->chvv( 'font_weight' , $element_properties) ){
            $output_css .= 'font-weight : ' .$element_properties['font_weight'] . ' !important;' ;
        }

        if( $this->chvv( 'font_style' , $element_properties) ){
            $output_css .= 'font-style : ' .$element_properties['font_style'] . ' !important;' ;
        }

        if( $this->chvv( 'text_decoration' , $element_properties) ){
            $output_css .= 'text-decoration : ' .$element_properties['text_decoration'] . ' !important;' ;
        }

        if( $this->chvv( 'text_align' , $element_properties) ){
            if( $element_properties['text_align'] == "right" && is_rtl() )
                $prop_val = "left";
            elseif( $element_properties['text_align'] == "left" && is_rtl() )
                $prop_val = "right";
            else
               $prop_val = $element_properties['text_align'];

            $output_css .= 'text-align : ' .$prop_val . ' !important;' ;
        }

        if( $this->chvv( 'line_height' , $element_properties , 0) ){
            $output_css .= 'line-height : ' .$element_properties['line_height'] . 'px !important;' ;
        }

        $output_css .= $this->text_shadow( $element_properties );

        return $output_css;
    }

    function add_property( $property ){
        if(is_array($property))
            $this->properties = array_merge( $this->properties , $property);
        else
            array_push( $this->properties , $property);
    }

    function add_settings_property( $property ){
        if(is_array($property))
            $this->settings_properties = array_merge( $this->settings_properties , $property);
        else
            array_push( $this->settings_properties , $property);
    }

    function get_settings_properties(){
        return apply_filters( 'css_settings_properties', $this->settings_properties );
    }
}