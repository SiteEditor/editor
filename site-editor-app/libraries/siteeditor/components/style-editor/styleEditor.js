/**
 * siteEditorCss.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
define("siteEditor/components/StyleEditor",
["siteEditor/siteEditorCss",
"siteEditor/siteEditorCore",
"siteEditor/modules/Background",
"siteEditor/modules/Border"],
function( siteEditorCss , siteEditor , Background, Border) {
    siteEditorCss = new siteEditorCss();

    function StyleEditor( ){
        var self = this;

        self.backgrounds = [];
        self.borders = [];

    }

    StyleEditor.prototype = {

        //constructor: background,

        backgroundColor: function(){
            var self = this;
            /*
            jQuery(".sed-background-color").each(function(index,element){
                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                //var element = "#" + elementId;

                if(!self.backgrounds[styleElement]){
                    var currentBG = new Background;
                    self.backgrounds[styleElement] = currentBG;
                }else{
                    var currentBG = self.backgrounds[styleElement];
                }

                currentBG.element = styleElement;

                var el = jQuery( siteEditor.siteSelector ).contents();

                var colorPickerOptionBG = colorPickerOption;
                colorPickerOptionBG.color = el.find( currentBG.element ).css("background-color") || "transparent";
                colorPickerOptionBG.hide = function (color) {
                    currentBG.updateColor(color); //alert(currentBG.color);
                };

                jQuery(this).spectrum(colorPickerOptionBG);
            }); */
        },


        gradient: function(){
            var self = this;

            jQuery(".sed-gradient").each(function(index,element){
                jQuery(this).click(function(e){
                    var styleElement = jQuery(this).attr("sed-style-element");
                    if(!styleElement)    return;

                    //var element = "#" + elementId;

                    if(!self.backgrounds[styleElement]){
                        var currentBG = new Background;
                        self.backgrounds[styleElement] = currentBG;
                    }else{
                        var currentBG = self.backgrounds[styleElement];
                    }
                    currentBG.element = styleElement;
                    //var css = siteEditorCss.setGradient( currentBG );
                    currentBG.gradient = jQuery(this).find("a");
                    currentBG.set();
                    //self.backgrounds[styleElement] = currentBG;
                    //alert(self.backgrounds.length);
                });
            });

            jQuery(".sed-no-gradient").each(function(index,element){

                jQuery(this).click(function(e){
                    var styleElement = jQuery(this).attr("sed-style-element");
                    if(!styleElement)    return;

                    //var element = "#" + elementId;

                    if(!self.backgrounds[styleElement]){
                        var currentBG = new Background;
                        self.backgrounds[styleElement] = currentBG;
                    }else{
                        var currentBG = self.backgrounds[styleElement];
                    }
                    currentBG.element = styleElement;
                    currentBG.gradient = "none";
                    currentBG.set();
                });
            });

        },

        backgroundAttachment: function(){
            var self = this;

            //background position
            jQuery(".sed-background-attachment").each(function(index,element){
                var $this = jQuery(this);
                var styleElement = $this.attr("sed-style-element");
                if(!styleElement)    return;

            //var element = "#" + elementId;

                if(!self.backgrounds[styleElement]){
                    var currentBG = new Background;
                    self.backgrounds[styleElement] = currentBG;
                }else{
                    var currentBG = self.backgrounds[styleElement];

                }
                currentBG.element = styleElement;

                jQuery(this).click(function(e){
                    var value = jQuery(this).attr("data-value");

                    currentBG.attachment = value;
                    currentBG.set();
                });

            });

        },

        backgroundPosition: function(){
            var self = this;

            //background position
                jQuery(".sed-background-position").each(function(index,element){
                    var $this = jQuery(this);

                    var styleElement = $this.attr("sed-style-element");
                    if(!styleElement)    return;

                //var element = "#" + elementId;

                    if(!self.backgrounds[styleElement]){
                        var currentBG = new Background;
                        self.backgrounds[styleElement] = currentBG;
                    }else{
                        var currentBG = self.backgrounds[styleElement];

                    }
                    currentBG.element = styleElement;

                    $this.find(".background-psn-sq").click(function(e){
                        var position = jQuery(this).attr("data-value");

                        $this.find(".background-psn-sq").each(function(index,element){
                            jQuery(this).removeClass("active_position");
                        });
                        jQuery(this).addClass("active_position");
                        //alert(currentBG.image);
                        currentBG.position = position;
                        currentBG.set();
                    });
            });
        },


        library: function(){
            var self = this;
         /*
            jQuery(".sed-library").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                if(!self.backgrounds[styleElement]){
                    var currentBG = new Background;
                    self.backgrounds[styleElement] = currentBG;
                }else{
                    var currentBG = self.backgrounds[styleElement];
                }

                currentBG.element = styleElement;

                var libBGHDimgLoader = false;
                jQuery(this).click(function(e){
                    var $this = jQuery(this);
                    if(loadLazyLoader === false){
                        siteEditor.autoLoadScripts(LIBBASE.url + "lazyload/js/jquery.bttrlazyloading.min.js", function () {
                            $this.next().find(".library-bg-img").bttrlazyloading({
                              //container: '#library-bg-img',
                              //updatemanually: true,
                              //triggermanually: true,
                              width: 27,
                              height: 27
                           });
                           loadLazyLoader = true;
                        });
                    }else{
                        if(libBGHDimgLoader === false){
                           $this.next().find(".library-bg-img").bttrlazyloading({
                             //container: '#library-bg-img',
                              width: 27,
                              height: 27
                           });
                           libBGHDimgLoader = true;
                        }
                    }
                });

                jQuery(this).next().find(".bground1").click(function(e){

                     var imgFullSrc = jQuery(this).find("img").attr("full-src");
                     currentBG.image = imgFullSrc || "none";
                     currentBG.set();
                });
                jQuery(this).next().find(".no-image").click(function(e){
                     currentBG.image = "none";
                     currentBG.set();
                });

            }); */
        },


        upload: function(){
            var self = this;
            /*
            jQuery(".sed-uploader").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                if(!self.backgrounds[styleElement]){
                    var currentBG = new Background;
                    self.backgrounds[styleElement] = currentBG;
                }else{
                    var currentBG = self.backgrounds[styleElement];
                }

                currentBG.element = styleElement;

                var uploaderId = jQuery(this).attr("id");
                var mediaGroup = jQuery(this).attr("sed-media-group");

                jQuery(this).seduploader({
                    url : LIBBASE.url + "media/uploader/upload.php?media_group=" + mediaGroup,
                    chunk_size : '1mb',
                    unique_names : true,
                    multi_selection : false,
                    //drop_element : options.drop_element,
                	filters : {
                	    max_file_size : '10mb',
                		mime_types: [
                			{title : "Images", extensions : "jpg,jpeg,png,gif"}
                		]
                	},
                });

                jQuery(this).on("UploadComplete",function(up, files){
                    var src = jQuery("#" + uploaderId + "-items").find(".img-icon").attr("full-src");
                    currentBG.image = src || "none";
                    currentBG.set();
                });

            });  */
        },


        length: function(){
            var self = this;

            jQuery(".sed-length").each(function(index,element){
                jQuery(this).click(function(e){

                    var styleElement = jQuery(this).attr("sed-style-element");
                    if(!styleElement)    return;
                    styleElement = jQuery( siteEditor.siteSelector ).contents().find( styleElement );

                     var type = jQuery(this).attr("data-value");
                     switch (type) {
                       case "wide":
                          if( styleElement.css("position") == "absolute" ||  styleElement.css("position") == "fixed" ){
                              styleElement.css("left", 0);
                          }
                          styleElement.css( "max-width" , jQuery( siteEditor.siteSelector ).contents().find( 'body' ).css("width"));
                       break;
                       case "boxed":
                          var w = jQuery( siteEditor.siteSelector ).contents().find( 'body' ).attr("sed-sheet-width");
                          styleElement.css( "max-width" , w);
                          if( styleElement.css("position") == "absolute" ||  styleElement.css("position") == "fixed" ){
                              var w1 = parseInt( jQuery( siteEditor.siteSelector ).contents().find( 'body' ).css("width") );
                              var w2 = parseInt( w );
                              var ml = (w1 - w2)/2;
                              if(ml)
                                  styleElement.css("left", ml + "px");
                          }
                       break;
                     }

                });
            });
        },

        borderRadius:function(){

            jQuery(".sed-corner-spinner").each(function(index,element){

                jQuery( this ).spinner({
                    min:0
                });

                jQuery(this).on("spinstop", function(e,ui){
                    borderRadiusCal(this);
                });

                jQuery(this).on("spin", function(e,ui){
                    borderRadiusCal(this);
                });

                jQuery(this).on("spinchange", function(e){
                    //borderRadiusCal(this);
                });

                jQuery(this).on("keyup", function(e){
                    borderRadiusCal(this);
                });

                //jQuery(element).parents(".sed-border-radius").find(".sed-border-radius-lock")

            });

            jQuery(".sed-border-radius-lock").each(function(index,element){
                jQuery( this ).change(function(){
                    var isChecked = jQuery(this).is(':checked');
                    if(isChecked){
                        var corners = [];
                        jQuery(element).parents(".sed-border-radius").find(".sed-corner-spinner").each(function(index,element){
                            corners.push(jQuery(this).val());
                        });
                        var minCorner = Math.min.apply(Math, corners);
                        jQuery(element).parents(".sed-border-radius").find(".sed-corner-spinner").each(function(index,element){
                            jQuery(this).spinner( "value", minCorner );
                        });
                    }
                });
            });

            function borderRadiusCal(element){
                var sizes, sizes_demo, tl,bl,tr,br ;

                var styleElement = jQuery(element).attr("sed-style-element");
                if(!styleElement)  return;

                var styleElementLock = jQuery(element).parents(".sed-border-radius").find(".sed-border-radius-lock").eq(0);
                var isChecked = styleElementLock.is(':checked');

                var spinner = jQuery(element);

                var val = jQuery(element).val();//spinner.spinner( "value" );

                if(isChecked){

                    jQuery(element).parents(".sed-border-radius").find(".sed-corner-spinner").each(function(i,el){
                        jQuery(this).spinner( "value",val );
                    });

                    jQuery(element).parents(".sed-border-radius").find(".demo-border-radius").each(function(index,el){
                          switch (jQuery(this).next().find(".sed-corner-spinner").attr("sed-data-type-radius")) {
                            case "tl":
                              sizes_demo = val + "px 0 0 0";
                            break;
                            case "tr":
                              sizes_demo = "0 " + val + "px 0 0";
                            break;
                            case "br":
                              sizes_demo = "0 0 " + val + "px 0";
                            break;
                            case "bl":
                              sizes_demo = "0 0 0 " + val + "px";
                            break;
                          }

                          siteEditorCss.borderRadius(this , sizes_demo );
                    });
                    sizes = val + "px " + val + "px " + val + "px " + val + "px";
                }else{
                    tl = jQuery(element).parents(".sed-border-radius").find("[sed-data-type-radius='tl']").spinner( "value" );
                    bl = jQuery(element).parents(".sed-border-radius").find("[sed-data-type-radius='bl']").spinner( "value" );
                    tr = jQuery(element).parents(".sed-border-radius").find("[sed-data-type-radius='tr']").spinner( "value" );
                    br = jQuery(element).parents(".sed-border-radius").find("[sed-data-type-radius='br']").spinner( "value" );

                    switch (jQuery(element).attr("sed-data-type-radius")) {
                      case "tl":
                        sizes_demo = val + "px 0 0 0";
                      break;
                      case "tr":
                        sizes_demo = "0 " + val + "px 0 0";
                      break;
                      case "br":
                        sizes_demo = "0 0 " + val + "px 0";
                      break;
                      case "bl":
                        sizes_demo = "0 0 0 " + val + "px";
                      break;
                    }

                    var el_demo = jQuery(element).parents(".BorderRadius").find(".demo-border-radius");

                    siteEditorCss.borderRadius(el_demo , sizes_demo );

                    sizes = tl + "px " + tr + "px " + br + "px " + bl + "px";
                }
                styleElement = jQuery( siteEditor.siteSelector ).contents().find( styleElement );
                siteEditorCss.borderRadius(styleElement ,sizes);

            }
        },

        //margin , padding , border-radius,...
        margin: function(){
            var self = this;

            jQuery(".sed-margin").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;
                styleElement = jQuery( siteEditor.siteSelector ).contents().find( styleElement );

                jQuery(this).on("spinstop", function(event,ui){
                    var spinner = jQuery(this);
                    var dataType = spinner.attr("data-type");
                    var val = spinner.spinner( "value" );
                    styleElement.css( dataType ,val + "px" );
                });

                jQuery(this).on("spin", function(event,ui){
                    var spinner = jQuery(this);
                    var dataType = spinner.attr("data-type");
                    var val = spinner.spinner( "value" );
                    styleElement.css( dataType ,val + "px" );
                });


            });

        },

        padding: function(){
            var self = this;

            jQuery(".sed-padding").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;
                styleElement = jQuery( siteEditor.siteSelector ).contents().find( styleElement );

                jQuery(this).on("spinstop", function(event,ui){
                    var spinner = jQuery(this);
                    var dataType = spinner.attr("data-type");
                    var val = spinner.spinner( "value" );
                    styleElement.css( dataType ,val + "px" );
                });

                jQuery(this).on("spin", function(event,ui){
                    var spinner = jQuery(this);
                    var dataType = spinner.attr("data-type");
                    var val = spinner.spinner( "value" );
                    styleElement.css( dataType ,val + "px" );
                });


            });

        },

        shadow: function(){
            var self = this;

            jQuery(".sed-shadow").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;
                styleElement = jQuery( siteEditor.siteSelector ).contents().find( styleElement );

                var shadowColor,shadowOptions;
                jQuery(this).find(".shadow a").on("click", function(e){  //".header-box-shadow
                    e.preventDefault();
                    var dbs = jQuery(this).attr("data-box-shadow");
                    if(jQuery.inArray(dbs,["initial","inherit","none"]) > -1){
                        siteEditorCss.boxShadow( styleElement, dbs );
                        shadowOptions = dbs;
                    }else{
                        var dbsArr = dbs.split(" ");
                        var options = {};
                        switch (dbsArr.length) {
                          case 4:
                            options.spread = dbsArr[3];
                            options.blur = dbsArr[2];
                          break;
                          case 3:
                            options.blur = dbsArr[2];
                          break;
                        }
                        options.HShadow = dbsArr[0];
                        options.VShadow = dbsArr[1];
                        var dbsin = jQuery(this).attr("data-box-shadow-inset") || "";
                        if(dbsin == "true")
                            options.inset = "inset";

                        options.color = shadowColor || "#000000";
                        siteEditorCss.boxShadow(styleElement , options);
                        shadowOptions = options;
                    }


                });

                var SHCPEL = jQuery(this).attr("sed-shadow-cp-el");

                if(SHCPEL){
                    var colorPickerOptionShadow = colorPickerOption;
                    colorPickerOptionShadow.color = "#000000";
                    colorPickerOptionShadow.hide = function (color) {
                        shadowColor = color.toString();
                        if(typeof shadowOptions == "object"){
                            shadowOptions.color = shadowColor;
                            siteEditorCss.boxShadow(styleElement ,shadowOptions);

                        }

                    };
                    jQuery(SHCPEL).spectrum(colorPickerOptionShadow);
                }
            });
        },

        transparency: function(){
            var self = this;

            jQuery(".sed-spinner-transparency").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;
                styleElement = jQuery( siteEditor.siteSelector ).contents().find( styleElement );

                jQuery( this ).spinner("option",{
                  min: 0,
                  max: 100,
                  start: 100
                });

                jQuery( this ).on("spinstop", function(event,ui){
                    var spinner = jQuery(this);
                    var val = spinner.spinner( "value" );
                    siteEditorCss.transparency( styleElement, val );
                });

                jQuery( this ).on("spin", function(event,ui){
                    var spinner = jQuery(this);
                    var val = spinner.spinner( "value" );
                    siteEditorCss.transparency( styleElement, val );
                });
            });
        },


        imageScaling: function(){
            var self = this;

            jQuery(".sed-image-scaling").each(function(index,element){

                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                if(!self.backgrounds[styleElement]){
                    var currentBG = new Background;
                    self.backgrounds[styleElement] = currentBG;
                }else{
                    var currentBG = self.backgrounds[styleElement];
                }

                currentBG.element = styleElement;

                jQuery(this).find(".scaling").click(function(e){
                     if(currentBG.image != "none"){
                         var type = jQuery(this).attr("value");
                         switch (type) {
                           case "fullscreen":
                                currentBG.size = "100% 100%";
                           break;
                           case "fit":
                                currentBG.size = "100% auto";
                           break;
                           case "tile":
                                currentBG.size = "auto";
                                currentBG.repeat = "repeat";
                           break;
                           case "tile-horizontally":
                                currentBG.size = "auto";
                                currentBG.repeat = "repeat-x";
                           break;
                           case "tile-vertically":
                                currentBG.size = "auto";
                                currentBG.repeat = "repeat-y";
                           break;
                           case "normal":
                                currentBG.size = "auto";
                                currentBG.repeat = "no-repeat";
                           break;
                         }
                         currentBG.set();
                     }
                });

            });
        },

        borderColor: function(){
            var self = this;
            /*
            jQuery(".sed-border-color").each(function(index,element){
                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                //var element = "#" + elementId;

                if(!self.borders[styleElement]){
                    var currentBO = new Border;
                    self.borders[styleElement] = currentBO;
                }else{
                    var currentBO = self.borders[styleElement];
                }

               currentBO.element = styleElement;

                //var el = jQuery( siteEditor.siteSelector ).contents();

                var colorPickerOptionBorder = colorPickerOption;
                colorPickerOptionBorder.color = "#000000";
                colorPickerOptionBorder.hide = function (color) {
                    var borderColor = color.toString();
                    currentBO.color["top"] = borderColor;
                    currentBO.color["bottom"] = borderColor;
                    currentBO.color["right"] = borderColor;
                    currentBO.color["left"] = borderColor;
                    currentBO.set();
                };
                jQuery(this).spectrum(colorPickerOptionBorder);
            });
            */
        },

        borderWidth: function(){
            var self = this;

            jQuery(".sed-border-width").each(function(index,element){
                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                //var element = "#" + elementId;

                if(!self.borders[styleElement]){
                    var currentBO = new Border;
                    self.borders[styleElement] = currentBO;
                }else{
                    var currentBO = self.borders[styleElement];
                }

               currentBO.element = styleElement;
               borderWidthHeader(element);
                //var el = jQuery( siteEditor.siteSelector ).contents();

                jQuery(this).on("spin", function(e){
                    borderWidthHeader(this);
                });

                jQuery(this).on("spinchange", function(e){
                    borderWidthHeader(this);
                });

                jQuery(this).on("keyup", function(e){
                    borderWidthHeader(this);
                });

                 function borderWidthHeader(element){
                    var spinner = jQuery(element);
                    var val = spinner.spinner( "value" );
                    var borderWidth = val + "px";
                    currentBO.width["top"] = borderWidth;
                    currentBO.width["bottom"] = borderWidth;
                    currentBO.width["right"] = borderWidth;
                    currentBO.width["left"] = borderWidth;
                    currentBO.set();
                 }

             });



        },


        borderStyle: function(){
            var self = this;

            jQuery(".sed-border-style").each(function(index,element){
                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                //var element = "#" + elementId;

                if(!self.borders[styleElement]){
                    var currentBO = new Border;
                    self.borders[styleElement] = currentBO;
                }else{
                    var currentBO = self.borders[styleElement];
                }

               currentBO.element = styleElement;

                //var el = jQuery( siteEditor.siteSelector ).contents();

                jQuery(this).find(".border").on('click', function(e) {
                    var borderStyle = jQuery(this).attr("sed-data-border-style");
                    currentBO.style["top"] = borderStyle;
                    currentBO.style["bottom"] = borderStyle;
                    currentBO.style["right"] = borderStyle;
                    currentBO.style["left"] = borderStyle;
                    currentBO.set();
                });

             });


        },

        borderSide: function(){
            var self = this;

            jQuery(".sed-border-side").each(function(index,element){
                var styleElement = jQuery(this).attr("sed-style-element");
                if(!styleElement)    return;

                //var element = "#" + elementId;

                if(!self.borders[styleElement]){
                    var currentBO = new Border;
                    self.borders[styleElement] = currentBO;
                }else{
                    var currentBO = self.borders[styleElement];
                }

               currentBO.element = styleElement;
               jQuery(this).find(".border-side").each(function(){
                    var val = jQuery(this).val(), isChecked = jQuery(this).is(':checked');

                    if(isChecked){
                        currentBO.side.push( val );
                    }
               });
                //var el = jQuery( siteEditor.siteSelector ).contents();

                jQuery(this).find(".border-side").on('change',  function(evt, params) {
                    var val = jQuery(this).val(), isChecked = jQuery(this).is(':checked');

                    if(isChecked){
                        currentBO.side.push( val );
                    }else{
                        var removeItem = val;
                        currentBO.side = jQuery.grep(currentBO.side, function(value) {
                          return value != removeItem;
                        });
                    }
                    currentBO.set();
                });

             });


        },

        render: function(){
            var self = this;

            jQuery( siteEditor.siteSelector ).load(function(){
                //self.backgroundColor();
                //self.gradient();
                //self.length();
                //self.borderRadius();
                //self.transparency();
                //self.library();
                //self.backgroundPosition();
                //self.shadow();
                //self.borderColor();
                //self.borderWidth();
                //self.borderSide();
                //self.borderStyle();
                //self.imageScaling();
                //self.backgroundAttachment();
                //self.upload();
                //self.margin();
                //self.padding();
            });
        }
    };

    return StyleEditor;
    //var StyleEditor = new StyleEditor;
    //StyleEditor.render();

});