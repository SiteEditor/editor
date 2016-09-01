/**
 * colorThemes.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
define("siteEditor/modules/ColorThemes",
["siteEditor/siteEditorCss","siteEditor/siteEditorCore"],
function( siteEditorCss , siteEditor ) {
    siteEditorCss = new siteEditorCss();

    function ColorThemes( ){
        var self = this;

        self.elements = [];
        self.groupLength = 5;
        self.currentColorGroup;
        //self.wsElement = jQuery( siteEditor.siteSelector ).contents();
        self.init();
    }

    ColorThemes.prototype = {

        //constructor: ColorThemes,
        init: function(){
            var self = this;
            self.addElement("background", 0, self.background);
            self.addElement("links", 4, self.linksElement);
        },

        setColorGroup: function(colors){
            var self = this;   alert(colors.toString());
            if( jQuery.isArray(colors) && colors.length == self.groupLength ){
                self.currentColorGroup = colors;
            }
        },

        //colorGroupIndex 0|1|2|3|4 ... until self.groupLength - 1
        addElement: function(element, colorGroupIndex ,callback){
            var self = this;
            self.elements.push({
              element : element ,
              callback : callback,
              colorGroupIndex : colorGroupIndex
            });
        },

        setColorsToSite: function(){
            var color, self = this;
            if(self.elements && self.currentColorGroup){
                jQuery.each(self.elements,function(index,value){
                    color = self.currentColorGroup[value.colorGroupIndex];

                    value.callback && value.callback.call(self,color );
                });
            }
        },

        background: function(color){
            var self = this,
            wsElement = jQuery( siteEditor.siteSelector ).contents();
            wsElement.find("body").css("background-color" , color);
        },

        linksElement: function(color){
            var self = this,
            wsElement = jQuery( siteEditor.siteSelector ).contents();
            wsElement.find("body a").css("color" , color);
            wsElement.find("body a:hover").css("color" , color);
        },

        buttonsElement: function(color){
            var self = this,
            wsElement = jQuery( siteEditor.siteSelector ).contents();
            wsElement.find("body button").css("background-color" , color);
            wsElement.find("body input[type='button']").css("background-color" , color);
            wsElement.find("body input[type='submit']").css("background-color" , color);
            wsElement.find("body input[type='reset']").css("background-color" , color);
            wsElement.find("body [sed-role='button']").css("background-color" , color);

            wsElement.find("body button").css("border-color" , color);
            wsElement.find("body input[type='button']").css("border-color" , color);
            wsElement.find("body input[type='submit']").css("border-color" , color);
            wsElement.find("body input[type='reset']").css("border-color" , color);
            wsElement.find("body [sed-role='button']").css("border-color" , color);

            wsElement.find("body button").css("color" , color);
            wsElement.find("body input[type='button']").css("color" , color);
            wsElement.find("body input[type='submit']").css("color" , color);
            wsElement.find("body [sed-role='button']").css("color" , color);

        },

        textBoxElement: function(color){
            var self = this,
            wsElement = jQuery( siteEditor.siteSelector ).contents();
            wsElement.find("body a").css("color" , color);
            wsElement.find("body a:hover").css("color" , color);
        },

        blockQuoteElement: function(color){
            var self = this,
            wsElement = jQuery( siteEditor.siteSelector ).contents();
            wsElement.find("body blockquote").css("color" , color);
            wsElement.find("body [sed-role='blockquote']").css("color" , color);
        },

        menuElement: function(color){
            var self = this,
            wsElement = jQuery( siteEditor.siteSelector ).contents().find("body [sed-role='h-menu'],body [sed-role='v-menu'],body [sed-role='menu']");
            wsElement.css("background-color" , color);
        }


    };

    return ColorThemes;

});