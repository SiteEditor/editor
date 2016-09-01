(function( exports, $ ) {

    var api = sedApp.editor ;

    api.StyleEditor = api.Class.extend({
        initialize: function( options , params ){

            $.extend( this, params || {} );

            this.options = $.extend( {
                distance : 100
            }, options || {} );

            this.currentItem;

            this.lastDomElements = "";

            this.styleRole = $('[sed-role]');

            this.editorState = "off";
            this.sedId = 0;
            this.docNodes = [];

            this.initDomElements = false;

            this.ready();
        },

        ready : function() {},

        hoverBox : function() {
            var hoverBox = $("#style-editor-hover-box" , window.parent.document) , self = this;
            /*$('[sed-role]').livequery(function(){

                $(this).mouseover(function(e){
                    //e.stopImmediatePropagation();
                    e.stopPropagation();
                          ////api.log($(this));
                    var w = $(this).outerWidth() , h = $(this).outerHeight() ,
                        offset = $(this).offset() , l = offset.left ,
                        t = offset.top;

                    hoverBox.show();

                    hoverBox.css({
                        width  : w,
                        height : h,
                        left   : l,
                        top    : t
                    });

                });

                $(this).mouseout(function(e){
                    //e.stopImmediatePropagation();
                    e.stopPropagation();

                    //api.log($(this));
                    hoverBox.hide();

                });

           });*/

           var iframe = $("#website" , window.parent.document);
           var ifrTop = iframe.offset().top;
           var ifrLeft = iframe.offset().left;
               ////api.log( ifrTop ); ////api.log( ifrLeft );
           $('body').mouseover(function(e){

                if(self.editorState == "off")
                    return ;

                var x = e.pageX - $( window ).scrollLeft(),
                    y = e.pageY - $( window ).scrollTop(),
                    element = document.elementFromPoint( x , y );

                   /* if( !$(element).attr("sed-role") )
                        return ; */

                var w = $( element ).outerWidth() , h = $( element ).outerHeight() ,
                    offset = $( element ).offset() , l = offset.left ,
                    t = offset.top;

                    l = l - $( window ).scrollLeft() + ifrLeft;
                    t = t - $( window ).scrollTop() + ifrTop;

                    hoverBox.show();
                                   ////api.log( hoverBox );
                    hoverBox.css({
                        width  : w,
                        height : h,
                        left   : l,
                        top    : t
                    });

           });

           $('body').mouseout(function(e){
                if(self.editorState == "off")
                    return ;

                hoverBox.hide();
           });

           hoverBox.on("mouseover mousemove" , function(e){

                if(self.editorState == "off")
                    return ;

                var x = e.pageX - ifrLeft,
                    y = e.pageY - ifrTop,
                    element = document.elementFromPoint( x , y );

                   /* if( !$(element).attr("sed-role") )
                        return ; */
                            ////api.log( $( element ).css("borderRadius") );
                var w = $( element ).outerWidth() , h = $( element ).outerHeight() ,
                    offset = $( element ).offset() , l = offset.left ,
                    t = offset.top;
                               ////api.log(x +","+ y);
                    l = l - $( window ).scrollLeft() + ifrLeft;
                    t = t - $( window ).scrollTop() + ifrTop;

                    hoverBox.show();

                    hoverBox.css({
                        width  : w,
                        height : h,
                        left   : l,
                        top    : t
                    });
           });

           hoverBox.mouseout(function(e){
                if(self.editorState == "off")
                    return ;

                hoverBox.hide();
           });

           hoverBox.on("click" , function(e){
                var x = e.pageX - ifrLeft,
                    y = e.pageY - ifrTop,
                    element = document.elementFromPoint( x , y ) ,
                    id = $(element).data("styleId") ,
                    pId = $(element).data("styleParentId") ,
                    module = $(element).parents('[sed-layout-role="pb-module"]:first');

                    if( $(element).attr("sed-layout-role") == "pb-module" )
                        moduleId = $(element).data("styleId");
                    else if(module.length)
                        moduleId = module.data("styleId");
                    else
                        moduleId = -1;

                ////api.log( $(element).text());

                self.preview.send( 'styleEditorElementSelected', {
                    id        :  id,
                    pId       :  pId,
                    moduleId  :  moduleId
                });
           });



        },

        changeState : function( state ){
            var self = this;
            if(!state || $.inArray( state , ["on" , "off"] ) == -1)
                return ;

            this.editorState = state;

        },

        send: function( id, data ) {
            this.preview.send( id , data);
        },

        getElementName : function( tagName ){
            var html = '<span class="node">' + tagName + '</span>';
            html += '<span class="edit-style-element-action fa icon-edit fa-lg "></span>';
            html += '<span class="code-editor-action fa widget_meta fa-lg "></span>';
            return html;
        },

        createObject : function( el , pId , prev ){
            var info = {} , self = this;
            if (el.nodeType === 1) {
                self.sedId++;
                info.id = self.sedId;//el.id || '';


                //info.className = el.className || '';

                if(el.className.indexOf("sed_app_contextmenu") > -1 )//|| el.className.indexOf("sed-handle-sort-row") > -1 || el.className.indexOf("sed-pb-handle-row-") > -1
                    return false;


                info.name = el.tagName.toLowerCase();


                if( $.inArray(info.name , ["script" , "head" , "style" , "link"] ) > -1  )
                    return false;

                $(el).data("styleId" , info.id);
                $(el).attr("data-style-id" , info.id);
                //$(el).attr("data-style-index" , info.id);
                $(el).data("styleParentId" , pId);

                info.t    = info.name;
                info.pId  = pId;
                info.tag  = info.name ;
                info.name = self.getElementName( info.name );

                if(prev === false){
                    var pIndex = $('[data-style-id="' + pId + '"]').data("styleIndex");
                    this.docNodes.splice(pIndex + 1, 0, info);
                    $(el).data("styleIndex" , pIndex + 1);
                }else{
                    var pvIndex = $('[data-style-id="' + prev + '"]').data("styleIndex");
                    this.docNodes.splice(pvIndex + 1, 0, info);
                    $(el).data("styleIndex" , pvIndex + 1);
                }

                //this.docNodes.push(info);
            }
        },

        elementToObj : function(el , pId) {
            var child, children, i,info = {} , self = this;

            if (el.nodeType === 1) {
                self.sedId++;
                info.id = self.sedId;

                //if( !_.isUndefined( el.className ) && el.className.indexOf("sed_app_contextmenu") > -1 || el.className.indexOf("sed-handle-sort-row") > -1 || el.className.indexOf("sed-pb-handle-row-") > -1 )
                    //return false;

                info.name = el.tagName.toLowerCase();

                if( $.inArray(info.name , ["script" , "head" , "style" , "link"] ) > -1  )
                    return false;


                $(el).data("styleId" , info.id);
                $(el).attr("data-style-id" , info.id);
                $(el).data("styleIndex" , this.docNodes.length);
                //$(el).attr("data-style-index" , info.id);
                $(el).data("styleParentId" , pId);

                info.t    = info.name;
                info.pId  = pId;
                info.name = self.getElementName( info.name );

                this.docNodes.push(info);

                children = el.childNodes;
                for (i = 0; i < children.length; i++) {
                    child = self.elementToObj(children[i] , info.id);
                    //if (child && /^\S*$/.test(child.contents)) {
                        //info.children.push(child);
                    //}
                }

            }

        },

        //:hover , :active , :focus , :visited
        elementStateSimulation : function() {
            var i,j, sel = /:hover/ , css;
            for(i = 0; i < document.styleSheets.length; ++i){
                for(j = 0; j < document.styleSheets[i].cssRules.length; ++j){
                    if(sel.test(document.styleSheets[i].cssRules[j].selectorText)){
                        css += document.styleSheets[i].cssRules[j].selectorText + "{" + document.styleSheets[i].cssRules[j].style.cssText + "}";
                    }
                }
            }
            css = css.replace( sel , ".sed-hovered");
        },

        findCurrentModule : function( id ){
            var element = $( "[data-style-id='" + id + "']" ) ,
            module = $(element).parents('[sed-layout-role="pb-module"]:first');

            if( $(element).attr("sed-layout-role") == "pb-module" )
                moduleId = $(element).data("styleId");
            else if( module.length )
                moduleId = module.data("styleId");
            else
                moduleId = -1;

            return moduleId;
        }

    });

    $( function() {

        api.styleEditor = new api.StyleEditor({} , {
            preview : api.preview
        });

        api.styleEditor.hoverBox();

		api.preview.bind( 'changeStateStyleEditor', function( state ) {
            api.styleEditor.changeState( state );

            if(state == "off")
                api.preview.send( 'closeDesignPanel' );
		});

        api.preview.bind( 'afterShowStyleEditor', function(  ) {
            api.preview.send( 'updateNavigator', updateNavigator() || [] );
        });

        var updateNavigator = function(){
            var domChanged = true;
                                        //$( ":not([data-style-id])" )
            var startTime = new Date();

            if(api.styleEditor.initDomElements === false){
                api.styleEditor.sedId = 0;
                api.styleEditor.docNodes = [];
                api.styleEditor.elementToObj($('html')[0] , 0);
                api.styleEditor.initDomElements = true;
 //               $( "[data-style-id]" ).attr("sed-loaded-element" , true);
            }else{
                var newElms = $( ":not([sed-loaded-element])" );
                newElms.each( function(index, elm){
                    var pId = $(this).parent().data("styleId") || 0 ,
                        prev = $(this).prev().data("styleId") || false;

                    api.styleEditor.createObject( this , pId , prev);
     //               $(this).attr("sed-loaded-element" , true);
                });
            }

            ////api.log( (new Date() - startTime) );

            if( api.styleEditor.lastDomElements  === JSON.stringify( api.styleEditor.docNodes ) )
                domChanged = false;
            else
                api.styleEditor.lastDomElements = JSON.stringify( api.styleEditor.docNodes );

            var currentStyleId = ((api.currentModuleElement && api.currentModuleElement.length == 1) ? api.currentModuleElement.data("styleId") : -1),
                moduleId;

            if(currentStyleId != -1)
                moduleId = api.styleEditor.findCurrentModule( currentStyleId );
            else
                moduleId = -1;

            var data = {
                docNodes       : api.styleEditor.docNodes ,
                domChanged     : domChanged ,
                currentStyleId : currentStyleId,
                defaultStyleId : $("body").data("styleId"),
                moduleId       : moduleId
            };

            api.currentModuleElement = "";

            return data;
        };

        $(window).load(function(){
          var startTime = new Date();
 //           $('html *').attr("sed-loaded-element" , "true");
            ////api.log( (new Date() - startTime) );
        });


        var hoverBox = $("#style-editor-hover-box" , window.parent.document);
        var iframe = $("#website" , window.parent.document);
        var ifrTop = iframe.offset().top;
        var ifrLeft = iframe.offset().left;

        api.preview.bind( 'addHighlightToElement', function( id ) {
            ////api.log( "[data-style-id='" + id + "']" );
            var selector = "[data-style-id='" + id + "']",
                w = $( selector ).outerWidth() , h = $( selector ).outerHeight() ,
                offset = $( selector ).offset() , l = offset.left ,
                t = offset.top;
                           ////api.log(x +","+ y);
                l = l - $( window ).scrollLeft() + ifrLeft;
                t = t - $( window ).scrollTop() + ifrTop;

                hoverBox.show();

                hoverBox.css({
                    width  : w,
                    height : h,
                    left   : l,
                    top    : t ,
                    backgroundColor : "blue" ,
                    opacity : 0.4
                });
        });

        api.preview.bind( 'removeHighlightElement', function( id ) {
            hoverBox.css({
                backgroundColor : "transparent" ,
                opacity : 1
            });
            hoverBox.hide();
        });

        api.preview.bind( 'findDomElementInfo', function( id ) {
            var selector = "[data-style-id='" + id + "']" ,
                element = $( selector ) , attrs = element[0].attributes
                tag = element[0].tagName.toLowerCase();

            var map = {} , aLength = attrs.length;

            for (a = 0; a < aLength; a++) {
                    map[attrs[a].name.toLowerCase()] = attrs[a].value;
            }

            var info = {
                attrs : map ,
                tag   : tag
            };

            api.preview.send( 'domElementInfo', info );

        });

        api.preview.bind( 'findCurrentModule', function( id ) {

            var moduleId = api.styleEditor.findCurrentModule( id );

            api.preview.send( 'currentModuleStyleId', moduleId );

        });

        api.preview.bind( 'changeColorPalette', function( css ) {

            if($("#global_css_framework").length > 0)
              $("#global_css_framework").html(css);
            else
              $("<style id='global_css_framework'>" + css + "</style>").appendTo( $('head') );

        });

        /****
            editing by  : siteeditor developer 
            style for colors and fonts
        ******/ 
        api.preview.bind('colors-font-less-compiled',function( style ){
            
            append_style( style.getCss , style.id );
            
        });

        api.preview.bind('font-sets-style' , function( css ){
            append_style( css , 'font-sets-style' );
        })

        api.preview.bind('sed_head' , function( html ){
            $('head').append(html);
        })

        var append_style = function( style , id ){

            if( $('#'+id).length > 0 ){
                $('#'+id).html( style );
            }else{
                css = "<style id='"+ id +"' type='text/css'>" + style + "</style>"
                $( css ).appendTo('body');
            }
        };





    });

}(sedApp, jQuery));