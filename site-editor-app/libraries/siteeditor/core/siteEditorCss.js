/**
 * siteEditorCss.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
define("siteEditor/siteEditorCss",
["siteEditor/siteEditorCore"],
function( siteEditor ) {

    function siteEditorCss( ){
        var self = this;

        self.behaviorUrl = LIBBASE.url + "PIE/PIE.htc";
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
            var rgbColor = rgbColor.replace('rgb(', '');
            rgbColor = rgbColor.replace(')', '');
            rgbColor = rgbColor.split(",");
            var r = parseInt(rgbColor[0]), g = parseInt(rgbColor[1]), b = parseInt(rgbColor[2]);
            return "#" + self.componentToHex(r) + self.componentToHex(g) + self.componentToHex(b);
        },

        addOpacityToRGB: function(rgbColor,opacity){
            var rgbColor = rgbColor.replace('rgb(', '');
            rgbColor = rgbColor.replace(')', '');
            rgbColor  = 'rgba(' + rgbColor + ',' + opacity + ')';
            return rgbColor;
        },

        //startColor And endColor is RGB Color
        //element is Gradient selector
        gradient : function( startColor , endColor , element){
            var self = this;
            if( jQuery( element ).attr("data-gradient-type") ){
                var GType = jQuery( element ).attr("data-gradient-type");
            }else{
                var GType = "linear";
            }

            startHexColor = self.rgbToHex( startColor );
            endHexColor = self.rgbToHex( endColor );

            if( jQuery( element ).attr("data-gradient-opacity") ){
                var GOpacity = jQuery( element ).attr("data-gradient-opacity");
                GOpacity = GOpacity.split(",");
                startColor = self.addOpacityToRGB(startColor , GOpacity[0]);
                endColor = self.addOpacityToRGB(endColor , GOpacity[1]);
            }else{
                var GOpacity = "";
            }


            if( jQuery( element ).attr("data-gradient-percent") ){
                var GPercent = jQuery( element ).attr("data-gradient-percent");
            }else{
                var GPercent = "0,100";
            }

            GPercent = GPercent.split(",");
            GPercent[0] = GPercent[0] + "%";
            GPercent[1] = GPercent[1] + "%";

            switch (jQuery( element ).attr("data-gradient-Orientation")) {
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


            var mozGradient =  "-moz-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ")"; /* FF3.6+ */

            var webkitGradient =  "-webkit-gradient(" + GType + " , " + webkitGPosition + " , " + "color-stop(" + GPercent[0] + "," + startColor + "), "+ " color-stop(" + GPercent[1] + "," + endColor + ") )";/* Chrome,Safari4+ */

            var webkitNewGradient =  "-webkit-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ")"; /* Chrome10+,Safari5.1+ */

            var operaGradient =  "-o-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ")";  /* Opera 12+ */

            var msie10Gradient =  "-ms-" + GType + "-gradient(" + GPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ")"; /* IE10+ */

            var w3cGradient =  GType + "-gradient(" + W3CGPosition + " , " + startColor + " " + GPercent[0] + " , " + endColor + " " + GPercent[1] + ")";  /* W3C */

            var msie8Gradient =  "progid:DXImageTransform.Microsoft.gradient( startColorstr='" + startHexColor + "', endColorstr='" + endHexColor + "',GradientType=" + GradientType + " )"; /* IE6-8 fallback on horizontal gradient */

            return [startColor, mozGradient , webkitGradient, webkitNewGradient, operaGradient, msie10Gradient, w3cGradient , msie8Gradient];

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
        setGradient: function( element ,Background, gradientElement ){
            var self = this;
            var startColor = self.hexToRgb( Background.color );
            var endColor = self.gradientEndColor( startColor );
            startColor =  'rgb(' + startColor.r + ',' + startColor.g + ',' + startColor.b + ')';

            var backgrounds = self.gradient( startColor , endColor , gradientElement );

            var styleElement = jQuery(element);
            //if (Modernizr.cssgradients){
                //styleElement.css("background" , backgrounds[6]);
            //}else{
                var css = "";
                var styles = self.css2json(styleElement.attr('style'));//self.styles(styleElement);
                styles["background"] = backgrounds;
                styles["-pie-background"] = [];
                styles["-pie-background"][0] = backgrounds[6];
                styles["behavior"] = [];
                styles["behavior"][0] = "url(" + self.behaviorUrl + ")";

                //styleElement[0].addBehavior( behaviorUrl );

                if(styles){
                    jQuery.each(styles,function(index,values){
                        jQuery.each(values,function(i,val){
                            css += index + ": " + val + "; ";
                        });
                    });
                }
                styleElement.attr("style" , css);
            //}

        },


        backgroundSize: function(element , size){
            var self = this;
            var styleElement = jQuery(element);
            if (Modernizr.backgroundsize){
                styleElement.css("background-size" , size);
            }else{
                var css = "";
                var styles = self.css2json(styleElement.attr('style'));//self.styles(styleElement);
                styles["-moz-background-size"] = [];
                styles["-o-background-size"] = [];
                styles["-webkit-background-size"] = [];
                styles["background-size"] = [];
                styles["-moz-background-size"][0] = size;
                styles["-o-background-size"][0] = size;
                styles["-webkit-background-size"][0] = size;
                styles["background-size"][0] = size;

                if(styles){
                    jQuery.each(styles,function(index,values){
                        jQuery.each(values,function(i,val){
                            css += index + ": " + val + "; ";
                        });
                    });
                }
                styleElement.attr("style" , css);
            }

        },

        backgroundColor: function(size){

        },

        transparency: function( element ,opacity ){
            var self = this;
            opacity2 = opacity / 100;

            var styleElement = jQuery(element);
            if (Modernizr.opacity){
                styleElement.css("opacity" , opacity2);
            }else{
                var css = "";
                var styles = self.css2json(styleElement.attr('style'));//self.styles(styleElement);
                styles["-khtml-opacity"] = [];
                styles["-moz-opacity"] = [];
                styles["opacity"] = [];
                styles["zoom"] = [];
                styles["-ms-filter"] = [];
                styles["filter"] = [];
                styles["-khtml-opacity"][0] = opacity2;
                styles["-moz-opacity"][0] = opacity2;
                styles["opacity"][0] = opacity2;
                styles["zoom"][0] = 1;
                styles["-ms-filter"][0] = "progid:DXImageTransform.Microsoft.Alpha(Opacity=" + opacity + ")";
                styles["filter"][0] = "alpha(opacity=" + opacity + ")";

                if(styles){
                    jQuery.each(styles,function(index,values){
                        jQuery.each(values,function(i,val){
                            css += index + ": " + val + "; ";
                        });
                    });
                }
                styleElement.attr("style" , css);
            }

        },

        //options can string include none|initial|inherit;
        //else options is object accesse
        boxShadow: function( element ,options ){
            var shadow,self = this;
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

            var styleElement = jQuery(element);
            if (Modernizr.boxshadow){
                styleElement.css("box-shadow" , shadow);
            }else{
                var css = "";
                var styles = self.css2json(styleElement.attr('style'));//self.styles(styleElement);
                styles["-webkit-box-shadow"] = [];
                styles["-moz-box-shadow"] = [];
                styles["box-shadow"] = [];
                styles["behavior"] = [];
                styles["-webkit-box-shadow"][0] = shadow;
                styles["-moz-box-shadow"][0] = shadow;
                styles["box-shadow"][0] = shadow;
                styles["behavior"][0] = "url(" + self.behaviorUrl + ")";

                //styleElement[0].addBehavior( behaviorUrl );

                if(styles){
                    jQuery.each(styles,function(index,values){
                        jQuery.each(values,function(i,val){
                            css += index + ": " + val + "; ";
                        });
                    });
                }
                styleElement.attr("style" , css);
            }

        },

        borderRadius: function( element , sizes){
            var self = this;

            var styleElement = jQuery(element);
            if (Modernizr.borderradius){
                styleElement.css("border-radius" , sizes);
            }else{
                var css = "";
                var styles = self.css2json(styleElement.attr('style'));//self.styles(styleElement);
                styles["-webkit-border-radius"] = [];
                styles["-webkit-border-radius"][0] = sizes;
                styles["-moz-border-radius"] = [];
                styles["-moz-border-radius"][0] = sizes;
                styles["border-radius"] = [];
                styles["border-radius"][0] = sizes;
                styles["behavior"] = [];
                styles["behavior"][0] = "url(" + self.behaviorUrl + ")";

                //styleElement[0].addBehavior( behaviorUrl );

                if(styles){
                    jQuery.each(styles,function(index,values){
                        jQuery.each(values,function(i,val){
                            css += index + ": " + val + "; ";
                        });
                    });
                }
                styleElement.attr("style" , css);
            }

        },

        styles : function(a) {
            var self = this,sheets = document.styleSheets, o = {};
            for (var i in sheets) {
                var rules = sheets[i].rules || sheets[i].cssRules;
                for (var r in rules) {
                    if (a.is(rules[r].selectorText)) {
                        o = jQuery.extend(o, self.css2json(rules[r].style), self.css2json(a.attr('style')));
                    }
                }
            }
            return o;
        },

        css2json: function(css) {
            if(css){
                var styles = css.split(';'),
                    i= styles.length,
                    json = {},
                    style, k, v;


                while (i--)
                {
                    style = styles[i].split(':');
                    k = jQuery.trim(style[0]);
                    v = jQuery.trim(style[1]);
                    if (k.length > 0 && v.length > 0)
                    {
                        if( jQuery.isArray(json[k]) === false )
                            json[k] = [];

                        json[k].push(v);
                    }
                }

                return json;
            }else{
                return {};
            }
        },

        getComputedStyle : function(dom){
          "use strict";
            var style,i,l,prop,ret={};
            if(window.getComputedStyle){     // FireFox and Chrome way
                style = window.getComputedStyle(dom, null);
                for(i = 0, l = style.length; i < l; ++i){
                    prop = style[i];
                    ret[prop] = style.getPropertyValue(prop);
                }
            } else if(dom.currentStyle){     // IE and Opera way
                style = dom.currentStyle;
                for(prop in style){
                    if(style.hasOwnProperty(prop)){
                        ret[prop] = style[prop];
                    }
                }
            } else if(dom.style){            // Style from style attribute
                style = dom.style;
                for(prop in style){
                    if(style.hasOwnProperty(prop)){
                        if(typeof style[prop] !== 'function'){
                            ret[prop] = style[prop];
                        }
                    }
                }
            }                               // else no joy, return empty
            return ret;
        }

    };

    return siteEditorCss;

});
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