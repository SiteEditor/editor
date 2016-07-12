window.sedApp = window.sedApp || {};

(function( exports, $ ){

    function siteEditorCss( ){
        var self = this;

        //self.id = id;
        //self.mediaItems = options.mediaItems;
        //self.errorBox = options.errorBox;
        //self.options = options;
    }

    siteEditorCss.prototype = {

        //constructor: siteEditorCss,

        init: function(){

        },

        hexToRgb: function(hex) {
            // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                return r + r + g + g + b + b;
            });

            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        },

        componentToHex: function(c) {
            var hex = c.toString(16);
            return hex.length == 1 ? "0" + hex : hex;
        },

        rgbToHex: function(rgbColor) {
            var self = this;
            rgbColor = rgbColor.replace('rgb(', '');
            rgbColor = rgbColor.replace(')', '');
            rgbColor = rgbColor.split(",");
            var r = parseInt(rgbColor[0]), g = parseInt(rgbColor[1]), b = parseInt(rgbColor[2]);
            return "#" + self.componentToHex(r) + self.componentToHex(g) + self.componentToHex(b);
        },

        addOpacityToRGB: function(rgbColor,opacity){
            rgbColor = rgbColor.replace('rgb(', '');
            rgbColor = rgbColor.replace(')', '');
            rgbColor  = 'rgba(' + rgbColor + ',' + opacity + ')';
            return rgbColor;
        },

        //startColor And endColor is RGB Color
        //element is Gradient selector
        gradient : function( startColor , endColor , gradient , imgSrc){
            var self = this;
            if( gradient.type ){
                var GType = gradient.type;
            }else{
                var GType = "linear";
            }

            startHexColor = self.rgbToHex( startColor );
            endHexColor = self.rgbToHex( endColor );

            if( gradient.opacity ){
                var GOpacity = gradient.opacity;
                GOpacity = GOpacity.split(",");
                startColor = self.addOpacityToRGB(startColor , GOpacity[0]);
                endColor = self.addOpacityToRGB(endColor , GOpacity[1]);
            }else{
                var GOpacity = "";
            }


            if( gradient.percent  ){
                var GPercent = gradient.percent;
            }else{
                var GPercent = "0,100";
            }

            GPercent = GPercent.split(",");
            GPercent[0] = GPercent[0] + "%";
            GPercent[1] = GPercent[1] + "%";

            switch ( gradient.orientation ) {
              case "horizontal":
                var GPosition = "left";
                var webkitGPosition = "left top, right top";
                var W3CGPosition = "to right";
                var GradientType = 1;
              break;
              case "vertical":
                var GPosition = "top";
                var webkitGPosition = "left top, right bottom";
                var W3CGPosition = "to bottom";
                var GradientType = 0;
              break;
              case "diagonal-rb":
                var GPosition = "-45deg";
                var webkitGPosition = "left top, left bottom";
                var W3CGPosition = "135deg";
                var GradientType = 1;
              break;
              case "diagonal-rt":
                var GPosition = "45deg";
                var webkitGPosition = "left bottom, right top";
                var W3CGPosition = "45deg";
                var GradientType = 1;
              break;
              case "radial":
                var GPosition = "center, ellipse cover";
                var webkitGPosition = "center center, 0px, center center, 100%";
                var W3CGPosition = "ellipse at center";
                var GradientType = 1;
              break;
              default:
                var GPosition = "top";
                var webkitGPosition = "left top, right bottom";
                var W3CGPosition = "to bottom";
                var GradientType = 1;
            }


            var mozGradient =  "-moz-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ") !important; /* FF3.6+ */";

            var webkitGradient =  "-webkit-gradient(" + GType + " , " + webkitGPosition + " , " + "color-stop(" + GPercent[0] + "," + startColor + "), "+ " color-stop(" + GPercent[1] + "," + endColor + ") ) !important; /* Chrome,Safari4+ */";

            var webkitNewGradient =  "-webkit-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ") !important; /* Chrome10+,Safari5.1+ */";

            var operaGradient =  "-o-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ") !important; /* Opera 12+ */";

            var msie10Gradient =  "-ms-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ") !important; /* IE10+ */";

            var w3cGradient =  GType + "-gradient(" + W3CGPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ") !important; /* W3C */";

            var msie8Gradient =  "progid:DXImageTransform.Microsoft.gradient( startColorstr='" + startHexColor + "', endColorstr='" + endHexColor + "',GradientType=" + GradientType + " ) !important; /* IE6-8 fallback on horizontal gradient */";

            if(imgSrc)
                imgSrc = 'url("' + imgSrc + '") , ';
            else
                imgSrc = '';

            var css  =  "background: " + startColor + " " + imgSrc + " !important; /* Old browsers */"
                      +  "background:" + imgSrc + mozGradient
                      +  "background:" + imgSrc + webkitGradient
                      +  "background:" + imgSrc + webkitNewGradient
                      +  "background:" + imgSrc + operaGradient
                      +  "background:" + imgSrc + msie10Gradient
                      +  "background:" + imgSrc + w3cGradient
                      +  "-pie-background:" + imgSrc + w3cGradient
                      +  "filter:" + msie8Gradient ;


            if(jQuery.browser.name == "firefox" && (jQuery.browser.versionNumber < 3 || (jQuery.browser.versionNumber == 3 && jQuery.browser.versionX < 6)) ){
                alert(siteEditor.I18n.GRADIENT_FIREFOX_NOT_SUPPORT);
            }

            if(jQuery.browser.name == "safari" && jQuery.browser.versionNumber < 4 ){
                alert(siteEditor.I18n.GRADIENT_SAFARI_NOT_SUPPORT);
            }

            if(jQuery.browser.name == "opera" && jQuery.browser.versionNumber < 12 ){
                alert(siteEditor.I18n.GRADIENT_OPERA_NOT_SUPPORT);
            }

            if(jQuery.browser.name == "msie" && (jQuery.browser.versionNumber == 9 || jQuery.browser.versionNumber < 6) ){
                alert(siteEditor.I18n.GRADIENT_MSIE_NOT_SUPPORT);
            }

           return css;

        },


        gradientEndColor: function(objColor){
            var stateC = 0;
            if(objColor.r <= 200){
              stateC += 1;
            }else{
              stateC -= 1;
            }

            if(objColor.g <= 200){
              stateC += 1;
            }else{
              stateC -= 1;
            }

            if(objColor.b <= 200){
              stateC += 1;
            }else{
              stateC -= 1;
            }

            if(stateC < 0){
                var num = -55;
            }else{
                var num = 55;
            }

            var r = ((objColor.r > 200 && stateC > 0) ?  255:objColor.r + num);
            var g = ((objColor.g > 200 && stateC > 0) ?  255:objColor.g + num);
            var b = ((objColor.b > 200 && stateC > 0) ?  255:objColor.b + num);

            var endColor = 'rgb(' + r + ',' + g + ',' + b + ')';
            return  endColor;
        },


        //Background is a object ,it is one object at Background Class In modules/background.js
        setGradient: function( color , gradient , imgSrc ){
            var self = this;
            var tColor = tinycolor( color ) ,
                endColor = self.gradientEndColor( tColor.toRgb() ) ,
                startColor = tColor.toHexString();

            startColor =  self.hexToRgb( startColor );
            startColor = 'rgb(' + startColor.r + ',' + startColor.g + ',' + startColor.b + ')';

            var css = self.gradient( startColor , endColor , gradient , imgSrc );

            return css;
        },


        backgroundSize: function(size){

            if(Modernizr.backgroundsize){
                var mozBackgroundSize = "-moz-background-size:" + size + " !important;";
                var operaBackgroundSize = "-o-background-size:" + size + " !important;";
                var webkitBackgroundSize = "-webkit-background-size:" + size + " !important;";
                var w3cBackgroundSize = "background-size:" + size + " !important;";
                var css = mozBackgroundSize + operaBackgroundSize + webkitBackgroundSize + w3cBackgroundSize;
            }else{
                if(jQuery.browser.name == "firefox" && (jQuery.browser.versionNumber < 3 || (jQuery.browser.versionNumber == 3 && jQuery.browser.versionX < 6)) ){
                    alert(siteEditor.I18n.BGSIZE_FIREFOX_NOT_SUPPORT);
                }

                if(jQuery.browser.name == "safari" && jQuery.browser.versionNumber < 3 ){
                    alert(siteEditor.I18n.BGSIZE_SAFARI_NOT_SUPPORT);
                }

                if(jQuery.browser.name == "opera" && (jQuery.browser.versionNumber < 9 || (jQuery.browser.versionNumber == 9 && jQuery.browser.versionX < 5)) ){
                    alert(siteEditor.I18n.BGSIZE_OPERA_NOT_SUPPORT);
                }

                if(jQuery.browser.name == "msie" && jQuery.browser.versionNumber < 9 ){
                    alert(siteEditor.I18n.BGSIZE_MSIE_NOT_SUPPORT);
                }

                if(jQuery.browser.name == "chrome" && jQuery.browser.versionNumber < 1 ){
                    alert(siteEditor.I18n.BGSIZE_MSIE_NOT_SUPPORT);
                }
                var css = "";
            }
            return css;
        },

        backgroundColor: function(size){

        },

        transparency: function( opacity ){
            opacity2 = opacity / 100;
            var css  =  "zoom: 1 !important;"
                      +  "-ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=" + opacity + ") !important;"
                      +  "filter: alpha(opacity=" + opacity + ") !important;"
                      +  "-moz-opacity: " + opacity2 + " !important;"
                      +  "-khtml-opacity: " + opacity2 + " !important;"
                      +  "opacity: " + opacity2 + " !important;";
            return css;
        },

        //options can string include none|initial|inherit;
        //else options is object accesse
        boxShadow: function( options ){
            var shadow;
            if(typeof options == "string"){
                shadow = options;
            }else if(typeof options == "object"){
                options = jQuery.extend({
                    HShadow : "1px",      //Required
                    VShadow : "1px",      //Required
                    blur: "",
                    spread: "",
                    color: "",
                    inset: ""
                }, options);


                shadow  = options.HShadow + " " + options.VShadow + " ";
                shadow += options.blur ? options.blur + " " : "";
                shadow += options.spread ? options.spread + " " : "";
                shadow += options.color ? options.color + " " : "";
                shadow += options.inset ? options.inset : "";
            }

            var css  =  "-webkit-box-shadow: " + shadow + " !important;"
                      +  "-moz-box-shadow: " + shadow + " !important;"
                      +  "box-shadow: " + shadow + " !important;"
                      +  "behavior: url(" + LIBBASE.url + "PIE/PIE.htc);";
            return css;
        },

        borderRadius: function( sizes ){

            //if (Modernizr.borderradius){
                var css  =  "-webkit-border-radius: " + sizes + " !important;"
                          +  "-moz-border-radius: " + sizes + " !important;"
                          +  "border-radius: " + sizes + " !important;"
                          +  "behavior: url(" + LIBBASE.url + "PIE/PIE.htc);";
                return css;
            //}else{
              //  return "";
            //}

        },

        sedBorderRadius: function( size , side , unit ){
            var cornerSide;
            switch ( side.toLowerCase() ) {
                case "tl":
                    cornerSide = "top-left";
                break;
                case "tr":
                    cornerSide = "top-right";
                break;
                case "bl":
                    cornerSide = "bottom-left";
                break;
                case "br":
                    cornerSide = "bottom-right";
                break;
            }

            unit = ( _.isUndefined( unit ) ) ? "px" : unit;

            //if (Modernizr.borderradius){
                var css  =  "-webkit-border-" + cornerSide + "-radius: " + size + unit + " !important;"
                          +  "-moz-border-" + cornerSide + "-radius: " + size + unit + " !important;"
                          +  "border-" + cornerSide + "-radius: " + size + unit + " !important;"
                          +  "behavior: url(" + LIBBASE.url + "PIE/PIE.htc);";
                return css;
            //}else{
              //  return "";
            //}

        }


    };


    exports.css = siteEditorCss;

})( sedApp, jQuery );
/*
mod-js mod-flexbox mod-flexboxlegacy mod-canvas mod-canvastext mod-webgl
mod-no-touch mod-geolocation mod-postmessage mod-no-websqldatabase
mod-indexeddb mod-hashchange mod-history mod-draganddrop mod-websockets
mod-rgba mod-hsla mod-multiplebgs mod-backgroundsize mod-borderimage
mod-borderradius mod-boxshadow mod-textshadow mod-opacity mod-cssanimations
mod-csscolumns mod-cssgradients mod-no-cssreflections mod-csstransforms
mod-csstransforms3d mod-csstransitions mod-fontface mod-generatedcontent
mod-video mod-audio mod-localstorage mod-sessionstorage mod-webworkers
mod-applicationcache mod-svg
mod-inlinesvg mod-smil mod-svgclippaths win firefox firefox2 gecko gecko2
*/