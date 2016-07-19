(function( exports, $ ) {

    var api = sedApp.editor;

    api.SiteEditorIframe = api.Class.extend({
        initialize: function( options , params ){
            var self = this;

            $.extend( this, params || {} );

            self.options = $.extend({
                headerDPA : "#tmpl-hd-dpa",
                footerDPA : "#tmpl-ft-dpa",
                topMCDPA : "#tmpl-top-mc-dpa",
                botMCDPA : "#tmpl-bot-mc-dpa",
                dragNDropHandle : "#tmpl-drag-n-drop",
                dragNDropElHandle : "#tmpl-dnp-el-hdl",
                dragNDropHelper : "#tmpl-drag-n-drop-helper",
                dnpPlaceHolder : "sed-state-highlight-row",
                layoutDPAClass : "sed-drop-area-layout"
            },options );

            self.layoutDPAClass = self.options.layoutDPAClass;
            self.dnpHandleSelector = "." + ($(self.options.dragNDropHandle).first().attr("handle-dnp") || "sed-handle-sort-row");

            self.dnpElHandleSelector = "." + ($(self.options.dragNDropElHandle).first().attr("handle-dnp") || "sed-dnp-el-handle");

            //site layout element
            self.siteLayoutEl = (typeof self.options.siteLayoutSelector != "undefined") ? $( self.options.siteLayoutSelector ) : ($( '[sed-role="layout"]' ).eq(0) || {}) ;

            if( typeof self.options.siteLayoutSelector == "undefined" ){
                if( typeof self.siteLayoutEl.attr("id") != "undefined" ){
                    self.siteLayoutSelector = "#" + self.siteLayoutEl.attr("id");
                }else{
                    self.siteLayoutEl.attr("id" , "sed-site-layout" );
                    self.siteLayoutSelector = "#sed-site-layout";
                }
            }else{
                self.siteLayoutSelector = self.options.siteLayoutSelector;
            }

            //header element
            self.siteHeaderEl = (typeof self.options.siteHeaderSelector != "undefined") ? $( self.options.siteHeaderSelector ) : ($( '[sed-role="header"]' ).eq(0) || {}) ;

            if( typeof self.options.siteHeaderSelector == "undefined" ){
                if( typeof self.siteHeaderEl.attr("id") != "undefined" ){
                    self.siteHeaderSelector = "#" + self.siteHeaderEl.attr("id");
                }else{
                    self.siteHeaderEl.attr("id" , "sed-site-header" );
                    self.siteHeaderSelector = "#sed-site-header";
                }
            }else{
                self.siteHeaderSelector = self.options.siteHeaderSelector;
            }

            //header inside elements
            self.siteHElementEl = (typeof self.options.siteHElementSelector != "undefined") ? $( self.options.siteHElementSelector ) : ($( '[sed-role="header-element"]' ) || {}) ;

            if( typeof self.options.siteHElementSelector == "undefined" ){
                self.siteHElementSelector = ".sed-site-h-element";
                self.siteHElementEl.addClass( "sed-site-h-element");
            }else{
                self.siteHElementSelector = self.options.siteHElementSelector;
            }



            //self.siteHElementSelector = '[sed-role="header-element"]';



            //main content element
            self.siteMContentEl = (typeof self.options.siteMContentSelector != "undefined") ? $( self.options.siteMContentSelector ) : ($( '[sed-role="main-content"]' ).eq(0) || {}) ;

            if( typeof self.options.siteMContentSelector == "undefined" ){
                if( typeof self.siteMContentEl.attr("id") != "undefined" ){
                    self.siteMContentSelector = "#" + self.siteMContentEl.attr("id");
                }else{
                    self.siteMContentEl.attr("id" , "sed-site-mcontent" );
                    self.siteMContentSelector = "#sed-site-mcontent";
                }
            }else{
                self.siteMContentSelector = self.options.siteMContentSelector;
            }

            //sidebar or column in main content
            self.siteColumnEl = (typeof self.options.siteColumnSelector != "undefined") ? $( self.options.siteColumnSelector ) : ($( '[sed-layout="column"]' ) || {}) ;

            if( typeof self.options.siteColumnSelector == "undefined" ){
                self.siteColumnSelector = ".sed-site-column";
                self.siteColumnEl.addClass( "sed-site-column");
            }else{
                self.siteColumnSelector = self.options.siteColumnSelector;
            }

            //footer element
            self.siteFooterEl = (typeof self.options.siteFooterSelector != "undefined") ? $( self.options.siteFooterSelector ) : ($( '[sed-role="footer"]' ).eq(0) || {}) ;

            if( typeof self.options.siteFooterSelector == "undefined" ){
                if( typeof self.siteFooterEl.attr("id") != "undefined" ){
                    self.siteFooterSelector = "#" + self.siteFooterEl.attr("id");
                }else{
                    self.siteFooterEl.attr("id" , "sed-site-footer" );
                    self.siteFooterSelector = "#sed-site-footer";
                }
            }else{
                self.siteFooterSelector = self.options.siteFooterSelector;
            }


            //site editor main Row eg header , footer or ...
            self.layoutRowEl = (typeof self.options.layoutRowSelector != "undefined") ? $( self.options.layoutRowSelector ) : ($( '[sed-layout="row"]' ) || {}) ;

            if( typeof self.options.layoutRowSelector == "undefined" ){
                self.layoutRowSelector = ".sed-layout-row";
                self.layoutRowEl.addClass( "sed-layout-row");
            }else{
                self.layoutRowSelector = self.options.layoutRowSelector;
            }

            self.layoutRowArr = [];
            self.layoutRowEl.each(function(index,element){
                self.layoutRowArr.push( element );
            });

            //Dynamic Row
            self.dynamicRowEl = (typeof self.options.dynamicRowSelector != "undefined") ? $( self.options.dynamicRowSelector ) : ($( '[sed-type-row="dynamic"]' ) || {}) ;

            if( typeof self.options.dynamicRowSelector == "undefined" ){
                self.dynamicRowSelector = ".sed-dynamic-row";
                self.dynamicRowEl.addClass( "sed-dynamic-row");
            }else{
                self.dynamicRowSelector = self.options.dynamicRowSelector;
            }

            self.dynamicRowArr = [];
            self.dynamicRowEl.each(function(index,element){
                self.dynamicRowArr.push( element );
                if($(this).attr("sed-row-area") == "no")
                    $(this).addClass("sed-dynamic-draggable-row");
            });



            //Static Row
            self.staticRowEl = (typeof self.options.staticRowSelector != "undefined") ? $( self.options.staticRowSelector ) : ($( '[sed-type-row="static"]' ) || {}) ;

            if( typeof self.options.staticRowSelector == "undefined" ){
                self.staticRowSelector = ".sed-static-row";
                self.staticRowEl.addClass( "sed-static-row");
            }else{
                self.staticRowSelector = self.options.staticRowSelector;
            }


            //Top Row
            self.topRowEl = (typeof self.options.topRowSelector != "undefined") ? $( self.options.topRowSelector ) : ($( '[sed-row-area="top"]' ) || {}) ;

            if( typeof self.options.topRowSelector == "undefined" ){
                self.topRowSelector = ".sed-top-area-row";
                self.topRowEl.addClass( "sed-top-area-row");
            }else{
                self.topRowSelector = self.options.topRowSelector;
            }

            //Bottom Row
            self.bottomRowEl = (typeof self.options.bottomRowSelector != "undefined") ? $( self.options.bottomRowSelector ) : ($( '[sed-row-area="bottom"]' ) || {}) ;

            if( typeof self.options.bottomRowSelector == "undefined" ){
                self.bottomRowSelector = ".sed-bottom-area-row";
                self.bottomRowEl.addClass( "sed-bottom-area-row");
            }else{
                self.bottomRowSelector = self.options.bottomRowSelector;
            }


            //top main content Row
            self.topMCRowEl = (typeof self.options.topMCRowSelector != "undefined") ? $( self.options.topMCRowSelector ) : ($( '[sed-row-area="top-mc"]' ) || {}) ;

            if( typeof self.options.topMCRowSelector == "undefined" ){
                self.topMCRowSelector = ".sed-top-mc-area-row";
                self.topMCRowEl.addClass( "sed-top-mc-area-row");
            }else{
                self.topMCRowSelector = self.options.topMCRowSelector;
            }

            //Bottom main content Row
            self.botMCRowEl = (typeof self.options.botMCRowSelector != "undefined") ? $( self.options.botMCRowSelector ) : ($( '[sed-row-area="bot-mc"]' ) || {}) ;

            if( typeof self.options.botMCRowSelector == "undefined" ){
                self.botMCRowSelector = ".sed-bot-mc-area-row";
                self.botMCRowEl.addClass( "sed-bot-mc-area-row");
            }else{
                self.botMCRowSelector = self.options.botMCRowSelector;
            }

            if( $( $(self.options.headerDPA).html() ).attr("id") ){
                self.headerDPASelector =  "#" + $( $(self.options.headerDPA).html() ).attr("id");
            }else{
                self.headerDPASelector =  "#sed-header-drop-area";
            }


            if( $( $(self.options.footerDPA).html() ).attr("id") ){
                self.footerDPASelector =  "#" + $( $(self.options.footerDPA).html() ).attr("id");
            }else{
                self.footerDPASelector =  "#sed-footer-drop-area";
            }


            if( $( $(self.options.topMCDPA).html() ).attr("id") ){
                self.topMCDPASelector =  "#" + $( $(self.options.topMCDPA).html() ).attr("id");
            }else{
                self.topMCDPASelector =  "#sed-top-mcontent-drop-area";
            }


            if( $( $(self.options.botMCDPAEl).html() ).attr("id") ){
                self.botMCDPAElSelector =  "#" + $( $(self.options.botMCDPAEl).html() ).attr("id");
            }else{
                self.botMCDPAElSelector =  "#sed-bot-mcontent-drop-area";
            }


        },

        headerDropArea: function( ){
            var self = this;

            self.siteLayoutEl.prepend( $(self.options.headerDPA).html() );
            var headerDPAEl = $( self.headerDPASelector );

            self.topRowEl.each(function(index,element){
                if( $.inArray(element , self.layoutRowArr) > -1 ){
                    headerDPAEl.append($(this));
                }
            });
            //self.sortable( headerDPAEl , {connectWith: "." + self.layoutDPAClass , items: self.layoutRowSelector, cancel: self.staticRowSelector} );

        },

        footerDropArea: function( ){
            var self = this;

            self.siteLayoutEl.append( $(self.options.footerDPA).html() );
            var footerDPAEl = $( self.footerDPASelector );

            self.bottomRowEl.each(function(index,element){
                if( $.inArray(element , self.layoutRowArr) > -1 ){
                    footerDPAEl.append($(this));
                }
            });
            //self.sortable( footerDPAEl , {connectWith: "." + self.layoutDPAClass , items: self.layoutRowSelector, cancel: self.staticRowSelector} );

        },

        topMcDropArea: function( ){
            var self = this;

            self.siteMContentEl.prepend( $(self.options.topMCDPA).html() );
            var topMCDPAEl = $(self.topMCDPASelector);

            self.topMCRowEl.each(function(index,element){
                if( $.inArray(element , self.layoutRowArr) > -1 ){
                    topMCDPAEl.append($(this));
                }
            });
            //self.sortable( topMCDPAEl , {connectWith: "." + self.layoutDPAClass , items: self.dynamicRowSelector} );

        },

        botMcDropArea: function( ){
            var self = this;

            self.siteMContentEl.append( $(self.options.botMCDPA).html() );
            var botMCDPAEl = $(self.botMCDPAElSelector);

            self.botMCRowEl.each(function(index,element){
                if( $.inArray(element , self.layoutRowArr) > -1 ){
                    botMCDPAEl.append($(this));
                }
            });
            //self.sortable( botMCDPAEl , {connectWith: "." + self.layoutDPAClass , items: self.dynamicRowSelector} );

        },

        dnpElements: function(){
            var self = this;


            /*
            self.siteHeaderEl.mouseover(function(){

                 $( self.siteHElementSelector ).css("position","relative");
             }); */
        },

        columnResizable: function(){
            var self = this;
            var mContentColumn =self.mContentColumn;
            var MCWidth = self.siteMContentEl.width();
            var minW = 50;
            var maxW = MCWidth - 50;

            //resize Column
            $('[sed-layout="column"]').livequery(function(){
                var prevWidth;
                if(!$(this).hasClass("main-col-first")){
                    $( this ).sedColumnResize({
                        resizeStart : function(event , item){
                            prevWidth = item.prev().width();
                           // console(item.prev().attr("class"));
                        },
                        resize : function(event, d, item, minWidth, maxWidth) {
                            var p = item.prev();
                            if(prevWidth + d >= minWidth && prevWidth + d <= maxWidth){
                                p.css( 'width', (prevWidth + d) + "px" );
                            }

                        },
                        stop : function(event, item) {
                            var columnRowWidth = $('[sed-role="main-content"] > .columns-row-inner').width();
                            self.preview.send( 'update_col_width' , {id : item.attr("id"),width : item.width()/columnRowWidth});
                            self.preview.send( 'update_col_width' , {id : item.prev().attr("id"),width : item.prev().width()/columnRowWidth});
                        }
                    });
                }



            });

        },

        columnSortable: function(){
             var self = this;
             $('[sed-role="main-content"] > .columns-row-inner').livequery(function(){

                 $( this ).sortable({
                    items: self.siteColumnSelector,
                    containment: "parent",
                    axis: "x",
                    items : '[sed-layout="column"]',
                    cancel : '[sed-type-col="static"]',
                    cursorAt: { top: 15,left: 114 },
                    cursor: "move",
                    //appendTo: document.body,
                    //forcePlaceholderSize: true,
                    //placeholder: self.options.dnpPlaceHolder,
                    handle: ".drag_btn",//self.dnpHandleSelector,
                    helper: function() {
                        return $( $(self.options.dragNDropHelper).html() );
                    },
                    start: function ( event, ui ){

                    },
                    stop: function ( event, ui ){
                        $(document).trigger("update_sort_col");
                    }
                 });

             });

        },

        columnInit: function(){
              var self = this;
              self.siteColumnSelector = '[sed-layout="column"]';

            $(document).on("update_sort_col", function(event){
                $('[sed-layout="column"]').each(function(index , rowEl){
                    self.preview.send( 'update_sort_col' , {id : $(this).attr("id"),index : index});
                });
            });

           var _colEdit = function(){
                var dnp = $( $(self.options.dragNDropHandle).html() ).appendTo( $(this) );
                var el = $(this);

               /* var resizeH = el.find(".ui-resizable-handle");
                $($("#tmpl-row-resizable-handle").html()).appendTo( resizeH );
                resizeH.hide(); */

                $(document).trigger("rowCheckBorder" , [this]);
                //check for dnp place in top row
                $(document).trigger("rowCheckHandle" , [this]);

                $(this).find(".remove_btn").on("click",function(){
                    el.remove();
                });

                el.hover(function(e){

                        /*if(api.styleEditor.editorState == "on")
                            return ;*/

                        if(el.hasClass("drag-sty-active") === false){
                            dnp.show();
                            //resizeH.show();
                        }

                        el.addClass("drag-sty");
                    },function(e){

                        /*if(api.styleEditor.editorState == "on")
                            return ;*/

                        if(el.hasClass("drag-sty-active") === false){
                            dnp.hide();
                            //resizeH.hide();
                        }
                        el.removeClass("drag-sty"); // resizable-sty
                    });
            };

            $('[sed-layout="column"]').livequery(_colEdit);

        },

        columnLayout: function(){
            var self = this;

            self.columnInit();

            self.columnResizable(this);

            self.columnSortable();
            //$( ".selector" ).sortable( "destroy" );



        },

        rowLayout: function(){
            var self = this;

            //self.resizable( self.siteHeaderEl , { handles: "s" });
            //self.resizable( self.siteHMenuEl, { handles: "s" });
            //self.resizable( self.siteFooterEl, { handles: "s" });


            $(document).on("rowCheckBorder", function(event , element){
                var bTop = $(element).css("border-top-width"),bBot = $(element).css("border-bottom-width"),
                    bLeft = $(element).css("border-left-width"),bRight = $(element).css("border-right-width"),
                    bTopS = $(element).css("border-top-style"),bBotS = $(element).css("border-bottom-style"),
                    bLeftS = $(element).css("border-left-style"),bRightS = $(element).css("border-right-style");

                if( bTopS && $.inArray(bTopS , ['hidden' , 'none']) == -1 && parseInt(bTop) > 0 ){
                    $(element).find(".sed-handle-sort-row").css("top" , (-parseInt(bTop)) + "px");
                    $(element).find(".sed-highlight-row-top").css("top" , (-parseInt(bTop)) + "px");
                    $(element).find(".sed-highlight-row-right").css("top" , (-parseInt(bTop)) + "px");
                    $(element).find(".sed-highlight-row-left").css("top" , (-parseInt(bTop)) + "px");
                }else{
                    $(element).find(".sed-handle-sort-row").css("top" , 0);
                    $(element).find(".sed-highlight-row-top").css("top" , 0);
                    $(element).find(".sed-highlight-row-right").css("top" , 0);
                    $(element).find(".sed-highlight-row-left").css("top" , 0);
                }

                if( bBotS && $.inArray(bTopS , ['hidden' , 'none']) == -1 && parseInt(bBot) > 0 ){
                    $(element).find(".sed-highlight-row-bottom").css("bottom" , (-parseInt(bBot)) + "px");
                    $(element).find(".sed-highlight-row-right").css("bottom" , (-parseInt(bBot)) + "px");
                    $(element).find(".sed-highlight-row-left").css("bottom" , (-parseInt(bBot)) + "px");
                }else{
                    $(element).find(".sed-highlight-row-bottom").css("bottom" , 0);
                    $(element).find(".sed-highlight-row-right").css("bottom" , 0);
                    $(element).find(".sed-highlight-row-left").css("bottom" , 0);
                }

                if( bLeftS && $.inArray(bTopS , ['hidden' , 'none']) == -1 && parseInt(bLeft) > 0 ){
                    $(element).find(".sed-highlight-row-bottom").css("left" , (-parseInt(bLeft)) + "px");
                    $(element).find(".sed-highlight-row-top").css("left" , (-parseInt(bLeft)) + "px");
                    $(element).find(".sed-highlight-row-left").css("left" , (-parseInt(bLeft)) + "px");
                }else{
                    $(element).find(".sed-highlight-row-bottom").css("left" , 0);
                    $(element).find(".sed-highlight-row-top").css("left" , 0);
                    $(element).find(".sed-highlight-row-left").css("left" , 0);
                }

                if( bRightS && $.inArray(bTopS , ['hidden' , 'none']) == -1 && parseInt(bRight) > 0 ){
                    $(element).find(".sed-highlight-row-bottom").css("right" , (-parseInt(bRight)) + "px");
                    $(element).find(".sed-highlight-row-top").css("right" , (-parseInt(bRight)) + "px");
                    $(element).find(".sed-highlight-row-right").css("right" , (-parseInt(bRight)) + "px");
                }else{
                    $(element).find(".sed-highlight-row-bottom").css("right" , 0);
                    $(element).find(".sed-highlight-row-top").css("right" , 0);
                    $(element).find(".sed-highlight-row-right").css("right" , 0);
                }

            });

            $(document).on("rowCheckHandle", function(event , element){
                if($(element).length == 0)
                    return ;

                if( $(element).offset().top < 15){
                    $(element).find(".drag-sty-part2").css("top" , (0 - $(element).offset().top) + "px");
                }else{
                    $(element).find(".drag-sty-part2").css("top" , "-15px");
                }
            });

            $(document).on("update_sort_row", function(event){
                var $topArea, $botArea, $topMcArea, $botMcArea;
                $topArea = $('#sed-header-drop-area');
                $botArea = $('#sed-footer-drop-area');
                $topMcArea = $('#sed-top-mcontent-drop-area');
                $botMcArea = $('#sed-bot-mcontent-drop-area');
                $dropArea = [$topArea , $botArea , $topMcArea , $botMcArea];
                $.each($dropArea ,function(i , areaEl){
                    areaEl.children().each(function(index , rowEl){
                        self.preview.send( 'update_sort_row' , {id : $(this).attr("id"),index : index});
                    });
                });
            });

            var _rowEdit = function(){
                var $tmpl = $(this).attr("sed-type-row") == "static" ? $("#tmpl-without-drag-n-drop").html() : $("#tmpl-drag-n-drop").html(),
                dnp = $( $tmpl ).appendTo( $(this) ), el = $(this) ,prev ,
                elZIn, prevZIn , next , nextZIn , offset = el.offset() , elMarginB;

                $(document).trigger("rowCheckBorder" , [this]);
                //check for dnp place in top row
                $(document).trigger("rowCheckHandle" , [this]);

                self.resizable( $(this), { handles: "s" });

                var resizeH = el.find(".ui-resizable-handle");
                if(resizeH.length > 0){
                    $($("#tmpl-row-resizable-handle").html()).appendTo( resizeH );
                    resizeH.hide();
                }


                $(this).find(".remove_btn").on("click",function(){

                    el.remove();
                });


                el.hover(function(e){

                    /*if(api.styleEditor.editorState == "on")
                        return ;*/

                    prev = el.prev();
                    elZIn = el.css("z-index");
                    prevZIn = prev.css("z-index");
                    next = el.next();
                    nextZIn = next.css("z-index");
                    elMarginB = el.css("margin-bottom");

                    if(prev.length > 0){
                        if(prevZIn != "auto" && (elZIn == "auto" || elZIn < prevZIn)){
                            el.css("z-index" , prevZIn + 1);
                        }
                    }
                    if(next.length > 0){
                        if(nextZIn != "auto" && (elZIn == "auto" || elZIn < nextZIn)){
                            el.css("z-index" , nextZIn + 1);
                        }
                    }
                    //fix resizeble for last row
                    /*if($(window).height() - offset.top - el.height() < 50 ){
                        el.css("margin-bottom" , "50px");
                    } */

                    //$(document).trigger("rowCheckHandle" , [el]);

                    if(el.hasClass("drag-sty-active") === false){
                        dnp.show();
                        resizeH.show();
                    }

                    el.addClass("drag-sty");
                },function(e){

                    /*if(api.styleEditor.editorState == "on")
                        return ;*/

                    if(prev.length > 0 || next.length > 0){
                        el.css("z-index" , elZIn);
                    }

                    //$(document).trigger("rowCheckHandle" , [el]);

                    //el.css("margin-bottom" , elMarginB);
                    if(el.hasClass("drag-sty-active") === false){
                        dnp.hide();
                        resizeH.hide();
                    }
                    el.removeClass("drag-sty"); // resizable-sty
                });
            };

            /*$(self.dynamicRowSelector).each(function(index,element){

            });*/
            $('[sed-layout="row"]').livequery(_rowEdit);

            //$("div").animate({left:'250px'});

//self.sortable( headerDPAEl , {connectWith: "." + self.layoutDPAClass , items: self.layoutRowSelector, cancel: self.staticRowSelector} );
            var $firstRowBeforeSort;
            var options = {
                //axis: "y",
                appendTo: document.body,
                containment: 'body',
                cursorAt: { top: 15,left: 114 },
                cursor: "move",
                connectWith : ".sed-drop-area-layout",
                items : '[sed-layout="row"]',
                cancel : '[sed-type-row="static"]',
                //cursorAt: { left: 5 },
                //forceHelperSize: true,
                //forcePlaceholderSize: true,
                placeholder: self.options.dnpPlaceHolder,
                handle: ".drag_btn",//self.dnpHandleSelector,
                helper: function() {
                    return $( $(self.options.dragNDropHelper).html() );
                },
                start : function(event , ui){
                    $firstRowBeforeSort = $("#sed-header-drop-area").children().first();
                },
                stop : function(event , ui){

                    $(document).trigger("update_sort_row");
                    $(document).trigger("rowCheckHandle" , [ui.item]);
                    $(document).trigger("rowCheckHandle" , [$("#sed-header-drop-area").children().first()]);
                    $(document).trigger("rowCheckHandle" , [$firstRowBeforeSort]);
                },
                opacity: 0.75,
                //revert: 100,
                tolerance: "pointer",
                //zIndex: 9999
                //scrollSensitivity: 100,
                scrollSpeed: 40,
                //distance: 5
                //dropOnEmpty: false
            };

            $(".sed-drop-area-layout").livequery(function(){
                $(this).sortable( options );
            });

            $(".sed-dynamic-draggable-row").draggable({
                axis: "y" ,
                handle: ".drag_btn" ,
                zIndex: 10000
            });

            /*
            $( 'html' ).css("max-width","100%");
            $( 'body' ).css("max-width","100%");
            var layout = $( '[sed-role="layout"]' );
            layout.css("max-width","100%");

            layout.parents().each(function(index , element){
                if(element.nodeName.toLowerCase() != "body" && element.nodeName.toLowerCase() != "html"){
                    $( this ).css("width","100%");
                    $( this ).css("max-width","100%");
                }
            });
            var rows = $( '[sed-layout="row"]' );
            rows.each(function(index , element){
                if($( element ).attr("sed-length") == "wide"){
                    //$( element ).css("width","100%");
                    $( element ).css("max-width","100%");
                }else{
                    var w = $( 'body' ).attr("sed-sheet-width");
                    $( element ).css("max-width", w);
                    $( element ).css("margin", "0 auto");
                    if( $( element ).css("position") == "absolute" ||  $( element ).css("position") == "fixed" ){
                        var w1 = parseInt( layout.css("width") );
                        var w2 = parseInt( w );
                        var ml = (w1 - w2)/2;
                        if(ml)
                            $( element ).css("left", ml + "px");
                    }
                }
            }); */

            $(self.dynamicRowSelector).on("click",function(){
                var resizeH = $(this).find(".ui-resizable-handle");

                $(self.dynamicRowSelector).each(function(index,element){
                    $(this).removeClass("drag-sty-active");
                    $(this).find(".sed-handle-sort-row").hide();
                    $(this).find(".ui-resizable-handle").hide();
                });
                $(this).addClass("drag-sty-active");
                $(this).find(".sed-handle-sort-row").show();
                resizeH.show();
            });

            $('body').on('click', function (e) {
                $(self.dynamicRowSelector).each(function () {
                    //the 'is' for buttons that trigger popups
                    //the 'has' for icons within a button that triggers a popup
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $(self.dynamicRowSelector).has(e.target).length === 0) {
                        $(this).removeClass("drag-sty-active");
                        $(this).find(".sed-handle-sort-row").hide();
                        $(this).find(".ui-resizable-handle").hide();
                    }
                });
            });

        },

        siteLayout: function(){
            var self = this;

            self.rowLayout();

            self.headerDropArea();
            self.footerDropArea();
            self.topMcDropArea();
            self.botMcDropArea();
        },

        moduleControls : function( ){
            var self = this ;

            //start text_title module
            /*
            $(".sed-title").livequery(function(){
                var elId = $(this).attr("id") , elem = this;
                if(!elId){
                    $(".sed-title").each(function(index , element){
                        if(elem === element){
                            elId = "sed-title-box-content" + index;
                            $(this).attr("id" , elId);
                        }
                    });
                }

                if(typeof tinymce == "undefined") {
                    self.autoLoadScripts( _sedAssetsUrls.editor.libs + "/tinymce/tinymce.min.js", function () {
                         self.textEditable( "#" + elId );
                         //self.textEditable( ".sed-title2" );
                    });
                }else{
                    self.textEditable( "#" + elId );
                }
            });*/
            //end text_title module


        },

        render: function( ){
            var self = this;

            //--self.siteLayout();


            //self.resizable( self.siteColumnEl, { handles: "e" });



            //--self.columnLayout();

            //--self.moduleControls();



            $(document).on("handleElement", function(event , element , tmpl , selector , objDrag){
                element.append( $( tmpl ).html() );
                var dnp = element.find( selector );
                element.hover(function(e){
                        dnp.show();
                    },function(e){
                        dnp.hide();
                    });
                objDrag = objDrag || {};
                var options = $.extend( {},{
                    containment : self.siteHeaderSelector,
                    stack: self.siteHElementSelector
                } , objDrag );

                self.draggable( element , options);
                element.css("position","relative");
            });

            self.siteHElementEl.each(function(index,element){
                $(document).trigger("handleElement",[$(this) , self.options.dragNDropElHandle , self.dnpElHandleSelector]);
            });


            $("#sed-text-controll", window.parent.document).click(function(){
                var titleBox = '<div class="sed-title-box">' + $("#tmpl-text-title-controll", window.parent.document).html() + '</div>';

                var el = $( titleBox ).appendTo( self.siteHeaderSelector );
                $(document).trigger("handleElement",[el , self.options.dragNDropElHandle , self.dnpElHandleSelector]);
            });


            var logoElement;

            $('#logo-enable-in-header', window.parent.document).change(function(){
                var isChecked = $(this).is(':checked');
                var logoBox = $("#tmpl-logo-controll", window.parent.document).html();

                if(isChecked){
                    logoElement = $( logoBox ).appendTo( self.siteHeaderSelector );
                    $(document).trigger("handleElement",[logoElement , self.options.dragNDropElHandle , self.dnpElHandleSelector]);
                }else{
                    if(logoElement)
                        logoElement.remove();
                }
            });

            $('#add-img-header', window.parent.document).click(function(e){
                e.preventDefault();
                var imgBox = $("#tmpl-img-h-ctrl", window.parent.document).html();

                var imgElement = $( imgBox ).appendTo( self.siteHeaderSelector );
                $(document).trigger("handleElement",[imgElement , self.options.dragNDropElHandle , self.dnpElHandleSelector]);
            });

            //start footer controll

            $('#add-img-footer', window.parent.document).click(function(e){
                e.preventDefault();
                var imgBox = $("#tmpl-img-f-ctrl", window.parent.document).html();

                var imgElement = $( imgBox ).appendTo( self.siteFooterSelector );
                $(document).trigger("handleElement",[imgElement , self.options.dragNDropElHandle , self.dnpElHandleSelector , {
                    containment : self.siteFooterSelector,
                    stack: self.siteFElementSelector
                }]);
            });

            // end footer controll


            //$(self.siteHeaderSelector).append("<div>wefcjwoiehfviow</div>");

            /*
            $(document).on("textEdit", function(event , selector){
                if(typeof tinymce == "undefined") {
                    self.autoLoadScripts( _sedAssetsUrls.editor.libs + "/tinymce/tinymce.min.js", function () {
                         self.textEditable( selector );
                         //self.textEditable( ".sed-title2" );
                    });
                }else{
                    self.textEditable( selector );
                }

            });

            $(document).trigger("textEdit",[".sed-title"]);



            $(document).on( "mouseover", ".sed-title", function(e){
                 var isDisabled = $(this).parent().draggable( "option", "disabled" );
                 if(!isDisabled)
                    $(this).parent().draggable( "disable" );

            });

            $(document).on( "mousemove" , ".sed-title-box " + self.dnpElHandleSelector, function(e){
                 var isDisabled = $(this).parent().draggable( "option", "disabled" );
                 if(isDisabled)
                    $(this).parent().draggable( "enable" );
            });  */
            //

        },

        // auto load scripts in plugins
        autoLoadScripts: function(url, callback) {

            var script = document.createElement("script")
            script.type = "text/javascript";

            if (script.readyState) { //IE
                script.onreadystatechange = function () {
                    if (script.readyState == "loaded" || script.readyState == "complete") {
                        script.onreadystatechange = null;
                        callback();
                    }
                };
            } else { //Others
                script.onload = function () {
                    callback();
                };
            }

            script.src = url;
            document.getElementsByTagName("head")[0].appendChild(script);
        },

        draggable: function( element , settings ){

            var options = {
                snap:true ,
                snapMode: "both",
                snapTolerance: 30,
                scroll: true,
                handle: self.dnpHandleSelector,
                /*helper: function() {
                    return $( $(self.options.dragNDropHelper).html() );
                }, */
                opacity : 0.8,
                revert:100
            };

            options = $.extend( {}, options , settings );

            element.draggable( options );
            //element.disableSelection();
        },

        sortable: function( dropArea , settings ){
            var self = this;
            var options = {
                //axis: "y",
                appendTo: document.body,
                containment: 'body',
                cursorAt: { top: 15,left: 114 },
                cursor: "move",
                //cursorAt: { left: 5 },
                //forceHelperSize: true,
                //forcePlaceholderSize: true,
                placeholder: self.options.dnpPlaceHolder,
                handle: ".drag_btn",//self.dnpHandleSelector,
                helper: function() {
                    return $( $(self.options.dragNDropHelper).html() );
                },
                opacity: 0.75,
                //revert: 100,
                tolerance: "pointer",
                //zIndex: 9999
                //scrollSensitivity: 100,
                scrollSpeed: 40,
                //distance: 5
                //dropOnEmpty: false
            };

            options = $.extend( {}, options , settings );

            dropArea.livequery(function(){
                $(this).sortable( options );
            });
            self.topRowEl.each(function(index,element){
                //$(this).disableSelection();
            });
        },

        resizable: function( element , settings ){
            var options = {
                handles: "n, e, s, w"

            };

            options = $.extend( {}, options , settings );

            element.resizable( options );
        },

        getMceToolBar : function( type , row ){
            switch ( type ) {
              case "title":
                  if( row == 1 ) 
                    return "formatselect | bold italic underline strikethrough | fontselect fontsizeselect";  //| closeeditor

                  if( row == 2 )
                    return "charmap removeformat | undo redo | link unlink | forecolor backcolor | alignleft aligncenter alignright alignjustify";
              break;
              case "paragraph":
                  if( row == 1 )
                    return "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect";  // | closeeditor

                  if( row == 2 )
                    return "pastetext removeformat charmap | bullist numlist outdent indent  | undo redo | link unlink | forecolor backcolor";
              break;
              case "normal-paragraph":
                  if( row == 1 )
                    return "pastetext removeformat charmap | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect";  // | closeeditor

                  if( row == 2 )
                    return "bold italic underline strikethrough | outdent indent  | undo redo | link unlink | forecolor backcolor";
              break;
              case "simple-paragraph":
                  if( row == 1 )
                    return "bold italic underline strikethrough | fontselect fontsizeselect";  // | closeeditor

                  if( row == 2 )
                    return "pastetext removeformat charmap | undo redo | link unlink | forecolor backcolor";
              break;
              case "normal-text":
                  if( row == 1 )
                    return "formatselect | fontselect fontsizeselect";   //| closeeditor

                  if( row == 2 )
                    return "charmap strikethrough | bold italic underline | undo redo | link unlink | forecolor";
              break;
              case "simple-text":
                  if( row == 1 )
                    return "fontselect fontsizeselect | charmap strikethrough";   //| closeeditor
                                                                                  
                  if( row == 2 )
                    return "bold italic underline | undo redo | forecolor";
              break;
            }
        },

        textEditable: function( selector , type ){
          type = (type) ? type : "title";  // title || paragraph || normal-text

          var plugin , toolbar1 , toolbar2 ,
            self = this,
            plugins = [
                "advlist autolink link lists charmap spellchecker",
                "wordcount visualchars visualblocks",
                "directionality textcolor colorpicker paste"
            ];

          this.MceToolBarType = ["title" , "paragraph" , "normal-paragraph" , "simple-paragraph" , "normal-text" , "simple-text" ];



          toolbar1 = self.getMceToolBar( type , 1 );
          toolbar2 = self.getMceToolBar( type , 2 );

          var fontsize_formats = "";
          for (var i=8; i <= 100; i++)  {
              fontsize_formats += i + "px";
              if( i != 100 )
                fontsize_formats += " ";
          }

          tinymce.init({
                  selector: selector,
                  plugins: plugins,

                  fontsize_formats: fontsize_formats,
                  font_formats : api.mceFontFormats ,
/*font_formats: "Andale Mono=andale mono,times;"+
    "Arial=arial,helvetica,sans-serif;"+
    "Arial Black=arial black,avant garde;"+
    "Book Antiqua=book antiqua,palatino;"+
    "Comic Sans MS=comic sans ms,sans-serif;"+
    "Courier New=courier new,courier;"+
    "Georgia=georgia,palatino;"+
    "Helvetica=helvetica;"+
    "Impact=impact,chicago;"+
    "Symbol=symbol;"+
    "Tahoma=tahoma,arial,helvetica,sans-serif;"+
    "Terminal=terminal,monaco;"+
    "Times New Roman=times new roman,times;"+
    "Trebuchet MS=trebuchet ms,geneva;"+
    "Verdana=verdana,geneva;"+
    "Webdings=webdings;"+
    "Wingdings=wingdings,zapf dingbats"
}); */

                  toolbar1: toolbar1,
                  toolbar2: toolbar2,

                  //contextmenu: "cut copy paste | undo redo | link ",

                  menubar: false,
                  inline: true,
                  toolbar_items_size: 'small',
                  resize: false,
                  object_resizing : false ,
                  paste_as_text: true,

                  setup: function(editor) {

                     //editor.getBody().setAttribute('contenteditable', false);
                     api.log(editor  );

                    var edId = this.id;

                    /*
                    // Add a custom button
                    editor.addButton('closeeditor', {
                        title : 'Close',
                        icon: 'close',
                        onclick : function() {
                            //tinymce.execCommand( 'mceRemoveEditor', false, edId );
                            //tinymce.editors[edId].hide(); autohide = true;
                            //tinymce.ui.FloatPanel.hide();
                            var panel = editor.theme.panel;
                            panel.hide();
                            panel.blur();
                            $("#" + edId).removeClass("mce-edit-focus");
                            editor.theme.panel.settings.autohide = true;
                            panel.close();
                            panel.disabled();
                
                            //$(this).parents(".mce-tinymce.mce-floatpanel:first").hide();

                            /*var index1 = $.inArray( edId , api.initParagraphsEditors );
                            var index2 = $.inArray( edId , api.initTitlesEditors );

                            if( index1 != -1 )
                                api.initParagraphsEditors.splice( index1 , 1 );
                            else if( index2 != -1 )
                                api.initTitlesEditors.splice( index2 , 1 );

                        }
                    });

                   /* $("#" + edId).on('mouseover', function(e) {
                        tinymce.editors[edId].show();
                    });*/

                     editor.on('focus', function(e) {

                        if( !_.isUndefined( $("#" + this.id).data("toolbar1") ) ){
                            if( $.inArray( $("#" + this.id).data("toolbar1") , self.MceToolBarType ) == -1 )
                                editor.settings.toolbar1 = $("#" + this.id).data("toolbar1");
                            else
                                editor.settings.toolbar1 = self.getMceToolBar( $("#" + this.id).data("toolbar1") , 1 );
                        }

                        if( !_.isUndefined( $("#" + this.id).data("toolbar2") ) ){
                            if( $.inArray( $("#" + this.id).data("toolbar2") , self.MceToolBarType ) == -1 )
                                editor.settings.toolbar2 = $("#" + this.id).data("toolbar2");
                            else
                                editor.settings.toolbar2 = self.getMceToolBar( $("#" + this.id).data("toolbar2") , 2 );
                        }
                     });


                     editor.on('change', function(e) {

                                           //     {format : 'html'}
                         if( !_.isUndefined( e.originalEvent ) && !_.isUndefined( e.originalEvent.command ) && e.originalEvent.command.toLowerCase() == "fontname" ){
                            //e.target.setAttribute('data-sed-font-family' , e.originalEvent.value );
                            var fonts = [];
                            $("#" + this.id).find('[style]').each(function(){
                                var ffamily = $(this)[0].style.fontFamily;
                                if( !_.isUndefined( ffamily ) && !_.isEmpty( ffamily )  ){
                                    ffamily = ffamily.replace(/\"/g, "");
                                    ffamily = ffamily.replace(/\'/g, "");
                                    fonts.push( ffamily );
                                }
                            });

                            api.typography.loadFont( e.originalEvent.value , this.id , fonts );
                         }
                              //alert( $( tinymce.dom.getParent(e.target.selection.getNode(), 'span') ).html() );
                        //save content in shortcode models
                        var content = this.getContent({format : 'html'});
                           //api.log( this );
                        var postId = api.pageBuilder.getPostId( $("#" + this.id) ),
                            children = api.contentBuilder.getShortcodeChildren( this.id );

                        if(children.length != 1){
                            alert("In Text Editor content not Allowed using any shortcodes");
                            return ;
                        }

                        var contentModel = children[0];

                        if(contentModel.tag != "content"){
                            alert("your shortcode incorrect , shortcode not AS content model");
                            return ;
                        }
                        //this.save();
                          ////api.log( contentModel );    
                        contentModel.content = content;
                        api.contentBuilder.updateShortcode( contentModel );

                        $("#" + this.id).trigger( "sed.changeMCEContent", [ content ] );

                  });


                        /*$('#' + editor.id).on('mouseout', function() {
                          $('#' + editor.id + '_tbl '+'.mceToolbar').hide();
                        });

                        editor.on('focus', function() {
                          $('#' + editor.id + '_tbl '+'.mceToolbar').show();
                        });*/

          /*tinymce.activeEditor.selection.onSetContent(function(){
          
          });*/

                  }
          });



        }

    });

    api.SiteEditorTypography = api.Class.extend({
        initialize: function( options , params ){
            var self = this;

            this.fonts = api.fonts;
            this.loadedFonts = [];
            this.googleFontsSettings = "";
            this.baseLoadedFonts = [];
            this.mceUsingFonts = _.isEmpty( api('page_mce_used_fonts')() ) ? {} : api('page_mce_used_fonts')();

            $.extend( this, params || {} );

            /*$('[data-sed-font-name]').livequery(function(){
                $(this)
            });*/

        },

        loadFont: function( font , editorId , editorFonts ){

            /*if( _.isUndefined( $(element).attr("typographyId") ) ){
                var _id = _.uniqueId( "typography_" );
                $(element).attr("typographyId" , _id );
                this.usingFonts[_id] = font;
            }else{
                this.usingFonts[ $(element).attr("typographyId") ] = font;
            } */
            var self = this;

            if( !_.isUndefined( editorId ) && !_.isUndefined( editorFonts ) ){
                this.mceUsingFonts[editorId] = editorFonts;
            }

            if( $.inArray( font , this.loadedFonts ) != -1 || $.inArray( font , this.baseLoadedFonts ) != -1 ){
                if( !_.isUndefined( editorId ) && !_.isUndefined( editorFonts ) ){
                    this.sendData();
                }
                return ;
            }

            if( $.inArray( font , _.keys( this.fonts["custom_fonts"] ) ) != -1 )
                this.loadCustomFont( font );
            else if( $.inArray( font , _.keys( this.fonts["google_fonts"] ) ) != -1  )
                this.loadGoogleFont( font );

            if( !_.isUndefined( editorId ) && !_.isUndefined( editorFonts ) ){
                this.sendData();
            }

        },

        loadCustomFont: function( font ){

            var the_font = '@font-face {';
            the_font += 'font-family: ' + this.customFontsSettings[font].name + ';';
            the_font += 'src: url("' + this.customFontsSettings[font].src.eot + '");';
            the_font += 'src:';
            the_font += "url('" + this.customFontsSettings[font].src.eot + "?#iefix') format('eot'),";
            the_font += "url('" + this.customFontsSettings[font].src.woff + "') format('woff'),";
            the_font += "url('" + this.customFontsSettings[font].src.ttf + "') format('truetype'),";
            the_font += "url('" + this.customFontsSettings[font].src.svg + "#" + this.customFontsSettings[font].name + "') format('svg');";
            the_font += "font-weight: 400;";
            the_font += "font-style: normal;";
            the_font += "}";

            var style = '<style type="text/css">' + the_font  + '</style>';

            $( style ).appendTo( $("head") );

            this.loadedFonts.push( font );

        },

        loadGoogleFont: function( font ){

    		//replace spaces with "+" sign
    		var the_font = font.replace(/\s+/g, '+'),
                protocol = ( IS_SSL ) ? "https" : "http" ;

            if( !_.isEmpty( this.googleFontsSettings ) )
                the_font += ":" + this.googleFontsSettings;

    		//add reference to google font family
    		$('head').append('<link href="'+ protocol +'://fonts.googleapis.com/css?family='+ the_font +'" rel="stylesheet" type="text/css">');

            this.loadedFonts.push( font );

        },

        sendData: function( ){
            var self = this;

            /*var allFonts = _.union( _.values( this.mceUsingFonts ) );
            allFonts = _.filter( allFonts , function( font ){
                return $.inArray( font , _.keys( self.fonts["custom_fonts"] ) ) != -1 || $.inArray( font , _.keys( self.fonts["google_fonts"] ) ) != -1;
            });*/


            //should before sub_themes_models_update sended
            api.preview.send( 'page_mce_used_fonts' , this.mceUsingFonts );

        },

    });

    $( function() {
        api.settings = window._sedAppEditorSettings;
        api.mceFontFormats = window._sedTinymceFontFormats;
        api.fonts = window._sedAppEditorFonts;

        api.typography = new api.SiteEditorTypography({} , {
            googleFontsSettings : window._sedGoogleFontsSettings ,
            customFontsSettings : window._sedCustomFontsSettings ,
            baseLoadedFonts     : window._sedBaseLoadedFonts
        });  

        api.siteIframe = new api.SiteEditorIframe({} , {
            preview : api.preview
        });

        api.siteIframe.render();

    });

}(sedApp, jQuery));