(function( exports, $ ) {

    var api = sedApp.editor;

    api.SEDModuleFreeDraggable = api.Class.extend({
        initialize: function( params , options ){
            var self = this;
            //, $parent = $('[sed-role="row-pb"]').parent()
            //$parent.addClass("sed-pb-rows-box bp-component");

            $.extend( this, options || {} );

            this.rowsZIndex = {};

            this.ready();


        },

        ready : function(){
            var self = this;
            this.modulesDraggableInit();
            this.modulesHandles();

            api.Events.bind( "createNewRowDraggableEl" , function( RowD ){
                self.addZIndexForRow( RowD.attr("sed_model_id") ); 
            });

            api.Events.bind( "ctxtAct_bringToFront"  , function(event , context , element){
                var id = $(context).parents('[sed-layout-role="pb-module"]:first').attr("sed_model_id");
                self.rowsZIndex[id].bringToFront( $(context).parent() );
            });

            api.Events.bind( "ctxtAct_bringForward"  , function(event , context , element){
                var id = $(context).parents('[sed-layout-role="pb-module"]:first').attr("sed_model_id");
                self.rowsZIndex[id].bringForward( $(context).parent() );
            });

            api.Events.bind( "ctxtAct_sendToBack"  , function(event , context , element){
                var id = $(context).parents('[sed-layout-role="pb-module"]:first').attr("sed_model_id");
                self.rowsZIndex[id].sendToBack( $(context).parent() );
            });

            api.Events.bind( "ctxtAct_sendBackward"  , function(event , context , element){
                var id = $(context).parents('[sed-layout-role="pb-module"]:first').attr("sed_model_id");
                self.rowsZIndex[id].sendBackward( $(context).parent() );
            });

            api.Events.bind( "createNewModuleDraggable" , function(moduleName , moduleItem , parentRow ){  //api.log(arguments);
                self.rowsZIndex[parentRow.attr("sed_model_id")].zIndexCreate( moduleItem );
            });

        },

        addZIndexForRow : function(id){
            this.rowsZIndex[id] = new api.ModuleFreeDraggableZIndex(id , {
                preview         : api.preview
            });
        },

        modulesDraggableInit : function(){
            var self = this;

            $('.module-element-draggable').livequery(function(){
                var that = this;

                self.moduledragEditSwitch( $(this) );

                $(this).draggable({
                    containment: "parent",
                    //stack: ".module-element-draggable" ,
                    //handle : ".module-element-draggable-handle" ,
                    guidelines: {
                    items: function() {
                        var guidelines = [];
                        $(this).parent().children('.module-element-draggable').not(this).each(function(item)
                        {
                            var offset = $(this).offset();
                            var size = { width: $(this).width(), height:  $(this).height() };
                            guidelines.push({ item : $(this), position: offset.top, snapSide: "top", oppositeSnapSide : "bottom" ,element: $(this), size: size, offset: offset });
                            guidelines.push({ item : $(this), position: offset.left, snapSide: "left", oppositeSnapSide : "right" , element: $(this), size: size, offset: offset });
                            guidelines.push({ item : $(this), position: offset.left + size.width, snapSide: "right", oppositeSnapSide : "left" , element: $(this), size: size, offset: offset });
                            guidelines.push({ item : $(this), position: offset.top + size.height, snapSide: "bottom", oppositeSnapSide : "top" , element: $(this), size: size, offset: offset });
                            guidelines.push({ item : $(this), position: offset.left + (size.width/2), snapSide: "vcenter", oppositeSnapSide : "" , element: $(this), size: size, offset: offset });
                            guidelines.push({ item : $(this), position: offset.top + (size.height/2), snapSide: "hcenter", oppositeSnapSide : "" , element: $(this), size: size, offset: offset });
                        });
                        return guidelines;
                        },
                        snapTolerance: 4,
                        margins: function() {
                            var margins = [];
                            $(this).parent().children('.module-element-draggable').not(this).each(function(item)
                            {
                            var offset = $(this).offset();
                            var size = { width: $(this).width(), height:  $(this).height() };
                            margins.push({ position: offset.top, snapSide: "top", element: $(this), size: size, offset: offset });
                            margins.push({ position: offset.left, snapSide: "left", element: $(this), size: size, offset: offset });
                            margins.push({ position: offset.left + size.width, snapSide: "right", element: $(this), size: size, offset: offset });
                            margins.push({ position: offset.top + size.height, snapSide: "bottom", element: $(this), size: size, offset: offset });
                            margins.push({ position: offset.left + (size.width/2), snapSide: "vcenter", element: $(this), size: size, offset: offset });
                            margins.push({ position: offset.top + (size.height/2), snapSide: "hcenter", element: $(this), size: size, offset: offset });
                            });
                            return margins;
                        },
                    },
                    /*overlap: {
                      items: function() {
                          return $(this).parent().children('.module-element-draggable').not(this).map(function(item) {
                              var offset = $(this).offset();
                              $t = $(this);
                              return { top: offset.top, right: $t.width() + offset.left, bottom: $t.height() + offset.top, left: offset.left, element: $t }
                          });
                      },
                      overlap: function (elements, draggedEl) {
                          $(this).parent().children('.module-element-draggable').removeClass("overlap"); if (elements.length > 0) {
                              $(elements).map(function() { return this.element[0]; }).add(this).addClass("overlap")
                          };
                      }
                    },  */
                    start : function(event , ui){
                        $(this).find(">.ui-resizable-handle").show();
                        $(this).addClass("module-dragging");
                    },
                    drag : function(event , ui){
                        $(this).find(">.ui-resizable-handle").hide();
                        $(this).removeClass("module-element-draggable-hover");
                    },
                    stop : function(event , ui){
                        $(this).find(">.ui-resizable-handle").show();
                        $(this).addClass("module-element-draggable-hover");
                        $(this).removeClass("module-dragging");
                        //alert( $(this).css("bottom") );
                    }
                }).resizable({
                    handles : "all" ,
                    autoHide : true
                });
            });

            $('[data-type-row="draggable-element"]').livequery(function(){
                $(this).resizable({
                    handles : "s"
                });
            });
        },

        modulesHandles: function(){
            var self = this;
            // add controller to each element in pages include settings And Delete And Move
            $('.module-element-draggable').livequery(function(){
                var element = $(this);

               element.hover(function(e){

                    /*if(api.styleEditor.editorState == "on")
                        return ;*/

                    e.stopPropagation();

                    $(this).find(">.module-element-edit-handle").show();
                    $(this).addClass("module-element-draggable-hover");
                },function(e){
                   /*api.styleEditor.editorState == "on" ||*/
                    if( self.resizing === true)
                        return ;

                    //e.stopPropagation();
                    $(this).find(">.module-element-edit-handle").hide();
                    $(this).removeClass("module-element-draggable-hover");
                });

            });

        },

        moduledragEditSwitch: function( element ){
            var self = this , id = element.attr("sed_model_id") + "-cover" ,cover = $('<div id="' + id + '" class="module-element-draggable-cover"></div').appendTo( element );

            cover.addClass(element.data("moduleContextmenu") + "_cover");

            var mouseHandle = $('<div class="module-element-edit-handle"><div class="mouse-handle"></div></div').appendTo( element );

            mouseHandle.toggle(function() {
                element.draggable( "disable" );
                cover.hide();
            }, function() {
                element.draggable( "enable" );
                cover.show();
            });
        },

        moduleLocked: function( element ){
            element.draggable( "disable" );
            element.addClass("sed-module-locked");
        },

        moduleUnlock: function( element ){
            element.draggable( "enable" );
            element.removeClass("sed-module-locked");
        }

    });

        var _pIn = function( string , number_radix ){
            return parseInt(string , number_radix)
        }

    api.ModuleFreeDraggableZIndex = api.Class.extend({
        initialize: function( rowId , options ){
            var self = this;
            //, $parent = $('[sed-role="row-pb"]').parent()
            //$parent.addClass("sed-pb-rows-box bp-component");

            $.extend( this, options || {} );

            this.modulesZIndex = [];

            this.maxZIndex = 0;

            this.ready();


        },

        ready : function(){
            var self = this;
        },

        zIndexCreate: function( element ){
            if(!element)
                return ;

            this.modulesZIndex.push({
                item   : element ,
                zIndex : this.maxZIndex + 1
            });
            element.css("z-index" , this.maxZIndex + 1 );
            this.maxZIndex += 1;
        },

        zIndexDelete: function( element ){
            if(!element)
                return ;

            var self = this , elZIndex = _pIn( element.css("z-index") );
            $.each( this.modulesZIndex , function(index , elm){
                var zIn = elm.zIndex;

                if(zIn > elZIndex){
                    elm.item.css("z-index" , (zIn - 1) );
                    elm.zIndex = (zIn - 1);  //self.modulesZIndex[index].zIndex
                }else if(zIn == elZIndex){
                    delete self.modulesZIndex[index];
                    self.maxZIndex -= 1;
                    return false;
                }

            });

        },

        findElementByZIndex: function( zIndex ){
            var $thisElement;
            $.each( this.modulesZIndex , function(index , elm){
                var zIn = elm.zIndex;
                if(zIn == zIndex){
                    $thisElement = elm.item;
                    return false;
                }

            });

            return $thisElement;
        },

        updateZIndex: function( element , newZIndex ){

            $.each( this.modulesZIndex , function(index , elm){
                if(elm.item[0] == element[0]){
                    elm.zIndex = newZIndex;
                    return false;
                }

            });

        },

        //
        bringToFront: function( element ){
            var elZIndex = _pIn( element.css("z-index") ) , self = this;
            $.each( this.modulesZIndex , function(index , elm){

                var zIn = elm.zIndex;

                if(zIn > elZIndex){
                    elm.item.css("z-index" , (zIn - 1) );
                    elm.zIndex = (zIn - 1);  //self.modulesZIndex[index].zIndex
                }else if(zIn == elZIndex){
                    element.css("z-index" , self.maxZIndex );
                    elm.zIndex = self.maxZIndex;
                }

            });

        },

        bringForward: function( element ){

            var elZIndex = _pIn ( element.css("z-index") ),
            oldEl = this.findElementByZIndex( (elZIndex+1) );
            if(oldEl){
                oldEl.css("z-index" , elZIndex);
                this.updateZIndex( oldEl , elZIndex);
                element.css("z-index" , (elZIndex+1));
                this.updateZIndex( element , (elZIndex+1));
            }

        },

        sendBackward: function( element ){

            var elZIndex = _pIn( element.css("z-index") ),
            oldEl = this.findElementByZIndex( (elZIndex-1) );//alert( elZIndex );
            if(oldEl){
                oldEl.css("z-index" , elZIndex);
                this.updateZIndex( oldEl , elZIndex);
                element.css("z-index" , (elZIndex-1));
                this.updateZIndex( element , (elZIndex-1))
            }

        },

        sendToBack: function( element ){
            var elZIndex = _pIn( element.css("z-index") );
            $.each( this.modulesZIndex , function(index , elm){
                var zIn = elm.zIndex;
                if(zIn < elZIndex){
                    elm.item.css("z-index" , (zIn + 1) );
                    elm.zIndex = (zIn + 1);  //self.modulesZIndex[index].zIndex
                }else if(zIn == elZIndex){
                    element.css("z-index" , 1 );
                    elm.zIndex = 1;
                }

            });

        }

    });


    $( function() {

        api.sedModuleFreeDraggable = new api.SEDModuleFreeDraggable({} , {
            preview         : api.preview,
            contentBuilder  : api.contentBuilder,
        });

    });

}(sedApp, jQuery));