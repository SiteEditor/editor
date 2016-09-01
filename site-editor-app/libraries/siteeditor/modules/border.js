/**
 * Border.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
define("siteEditor/modules/Border",
["siteEditor/siteEditorCss","siteEditor/siteEditorCore"],
function( siteEditorCss , siteEditor ) {
    siteEditorCss = new siteEditorCss();

    function Border( ){
        var self = this;

        self.element = "body";
        self.color = {
            top : "#000000",
            bottom : "#000000",
            right : "#000000",
            left : "#000000"
        };
        self.width = {
            top : "0px",
            bottom : "0px",
            right : "0px",
            left : "0px"
        };
        self.style = {
            top : "none",
            bottom : "none",
            right : "none",
            left : "none"
        };
        self.side = [];
        self.allSide = ["top","bottom","left","right"];

    }

    Border.prototype = {

        //constructor: background,

        set: function(){
            var self = this;

            if(self.element){
                var el = jQuery( siteEditor.siteSelector ).contents();

                    jQuery.each(self.allSide,function(i,val){

                        if(jQuery.inArray(val,self.side) > -1){
                            el.find( self.element ).css("border-"+ val , self.style[val] + " " + self.width[val] + " " + self.color[val]);
                         }else{
                            //self.style[val] = "none";
                            el.find( self.element ).css("border-"+ val , "none " + self.width[val] + " " + self.color[val]);
                         }
                    });
            }
        }
    };

    return Border;

});