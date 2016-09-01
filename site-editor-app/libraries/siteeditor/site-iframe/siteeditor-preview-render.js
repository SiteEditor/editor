(function( exports, $ ){
	var api = sedApp.editor,
        siteEditorCss = new sedApp.css,
        styleIdElements = {} ,
		rowTmpl = {} , colTmpl = {};



    api.currentCssSelector = "";

    api.currentAttr = "";

    var _getStyleId = function( element , type , prop ){

        var styleId;

        if( _.isUndefined( styleIdElements[api.currentCssSelector] ) || _.isUndefined( styleIdElements[api.currentCssSelector][prop] ) ){
            styleId = _.uniqueId( type + '_') + '_css';

            if( _.isUndefined( styleIdElements[api.currentCssSelector] ) )
                styleIdElements[api.currentCssSelector] = {} ;

            styleIdElements[api.currentCssSelector][prop] = styleId;
        }else
            styleId = styleIdElements[api.currentCssSelector][prop] ;

        return styleId;

       /* if( !_.isString( type ) || element.length == 0 )
            return ;

        if( _.isUndefined( element.data( "styleId" ) ) || _.isUndefined( element.data( "styleId" )[prop] ) ){
            styleId = _.uniqueId( type + '_') + '_css';

            if( _.isUndefined( element.data( "styleId" ) ) )
                element.data( "styleId" , {} );

            element.data( "styleId" )[prop] = styleId;
        }else
            styleId = element.data( "styleId" )[prop];

        return styleId; */

    };

	$( function() {
        api.isRTL = window.IS_RTL;
		api.settings = window._sedAppEditorSettings;
        //api.templateSettings = window._sedAppTemplateOptions;
        api.I18n = window._sedAppEditorI18n;
        api.addOnSettings = window._sedAppEditorAddOnSettings;
                          api.log( "api.addOnSettings ---------- : " , api.addOnSettings );
		if ( ! api.settings )
			return;

		var bg, parallax  , font , bgElements , textProp ,stylesProp , radius , paddings , margins , borders
        , columnRowWidth = $('[sed-role="main-content"] > .columns-row-inner').width();

		api.preview = new api.Preview({
			url: window.location.href,
			channel: api.settings.channel
		});

		api.preview.bind( 'settings', function( values ) {
			$.each( values, function( id, value ) {
				if ( api.has( id ) )
					api( id ).set( value );
				else
					api.create( id, value ,{
					    stype : api.settings.types[id] || "general"
					} );
			});
		});
                    console.log( "api.settings.values-------" , api.settings.values );
		api.preview.trigger( 'settings', api.settings.values );

        //for all settings but style editor settings not
        api.preview.bind( 'current_element', function( element ) {
            //api.currentCssSelector = element;
            api.styleCurrentSelector = element;
        });

        //for style editor settings
        api.preview.bind( 'current_css_selector', function( selector ) {
            api.currentCssSelector = selector;
        });



        /*$.each(api.templateSettings.patterns.rows , function(index , val){
            rowTmpl[val.role] = val;
        });*/

        //draggable modules in modules tab and drop in page content
        parent.jQuery( ".sed-draggable" ).sedDraggable({
            scrollSensitivity : 20,
            scrollSpeed : 20,
            dropInSortable: ".sed-drop-area-layout",
            iframeSortable: "website",
            placeholder:"<div class='sed-state-highlight-row'>test...</div>",
            dragStart : function(event , element, helper){
                parent.jQuery("#iframe_cover").show();
            },
            stop : function(event , element, helper){
                parent.jQuery("#iframe_cover").hide();
            },
            sortStop : function(event,ui) {
                var $numRows, $topArea, $botArea, $topMcArea, $botMcArea,
                    $area, $rowElement, $rowId, $tmpl, rowIndex = 0 , $start , $rowDrag;

                $topArea = $('#sed-header-drop-area');
                $botArea = $('#sed-footer-drop-area');
                $topMcArea = $('#sed-top-mcontent-drop-area');
                $botMcArea = $('#sed-bot-mcontent-drop-area');

                $numRows = $topArea.children().length + $botArea.children().length + $topMcArea.children().length + $botMcArea.children().length;
                switch (ui.sortable.attr("id")) {
                  case 'sed-header-drop-area':
                    $area = "top";
                  break;
                  case 'sed-footer-drop-area':
                    $area = "bottom";
                  break;
                  case 'sed-top-mcontent-drop-area':
                     $area = "top-mc";
                  break;
                  case 'sed-bot-mcontent-drop-area':
                     $area = "bot-mc";
                  break;
                  default :
                     $area = "";
                }

                //$tmpl = rowTmpl["custom"].start + rowTmpl["custom"].end;
                                //alert( $(ui.handle).attr("sed-draggable-element") );
                $rowDrag = $(ui.handle).attr("sed-draggable-element") || "false";
                $rowId = "sed-custom-main-row" + $numRows;
                $tmpl = $tmpl.replace("{{id}}" , $rowId);
                $tmpl = $tmpl.replace("{{area}}" , $area);
                $tmpl = $tmpl.replace("{{draggable}}" , $rowDrag);

                if(ui.direction == "down"){
                    $rowElement = $( $tmpl ).insertBefore( ui.item );
                }else{
                    $rowElement = $( $tmpl ).insertAfter( ui.item );
                }

                ui.sortable.children().each(function(index ,element){
                    if($rowElement[0] == $(this)[0]){
                        rowIndex = index;
                    }
                });

                /*
                $start = rowTmpl["custom"].start
                $start = $start.replace("{{id}}" , $rowId);
                $start = $start.replace("{{area}}" , $area);
                $start = $start.replace("{{draggable}}" , $rowDrag);

                $rowElement.attr("sed-row-area", $area);
                $rowElement.attr("id" , $rowId);
                api.preview.send( 'create_main_row' , {
                    id        : $rowId ,
                    dropArea  : $area,
                    index     : rowIndex,
                    content   : '' ,
                    role      : "custom" ,
                    start     :  $start,
                    end       :  rowTmpl["custom"].end,
                    sync      : true ,
                    type      : "custom" ,
                    attr      : {}
                });

                $(document).trigger("update_sort_row");*/
            }
        });

        //parent.jQuery( ".sed-draggable" ).sedDraggable({

        //});
           //data-type-row="draggable-element"
        /*$.each(api.templateSettings.patterns.cols , function(index , val){
            colTmpl[val.role] = val;
        });*/

        parent.jQuery( ".sed-col-draggable" ).sedDraggable({
            scrollSensitivity : 20,
            scrollSpeed : 20,
            axis : 'x',
            dropInSortable: '[sed-role="main-content"] > .columns-row-inner',
            iframeSortable: "website",
            placeholder:"<div class='sed-state-highlight-col'>test...</div>",
            dragStart : function(event , element, helper){
                parent.jQuery("#iframe_cover").show();
            },
            stop : function(event , element, helper){
                parent.jQuery("#iframe_cover").hide();
            },
            sortStop : function(event,ui) {
                var $numCols, $colElement, $colId, $tmpl, colIndex = 0 , $start , $colDrag;

                //$tmpl = colTmpl["custom"].start + colTmpl["custom"].end;
                                //alert( $(ui.handle).attr("sed-draggable-element") );
                $numCols = $('[sed-layout="column"]').length;
                $colId = "sed-custom-main-col" + $numCols;
                $tmpl = $tmpl.replace("{{id}}" , $colId);

                var t = Math.floor( 100 / $numCols );
                var ncw = 100 - ($numCols * t);
                $('[sed-layout="column"]').each( function (i,el) {
                    var newW = $(this).width() - t;
                    $(this).css("width", newW + "px");
                    api.preview.send( 'update_col_width' , {id : $(this).attr("id"),width : newW/columnRowWidth});
                });

                if(ui.direction == "down"){
                    $colElement = $( $tmpl ).insertBefore( ui.item );
                }else{
                    $colElement = $( $tmpl ).insertAfter( ui.item );
                }

                $($colElement).css("width", (100 + ncw) + "px");

                ui.sortable.children().each(function(index ,element){
                    if($colElement[0] == $(this)[0]){
                        colIndex = index;
                    }
                });

                /*$start = colTmpl["custom"].start
                $start = $start.replace("{{id}}" , $colId);

                $colElement.attr("id" , $colId);
                api.preview.send( 'create_main_col' , {
                    id        : $colId ,
                    index     : colIndex,
                    content   : '' ,
                    role      : "custom" ,
                    start     :  $start,
                    end       :  colTmpl["custom"].end,
                    sync      : true ,
                    type      : "custom" ,
                    attr      : {},
                    settings  : {width : (100 + ncw)/columnRowWidth}
                });

                $(document).trigger("update_sort_col"); */
            }
        });

		api.preview.bind( 'setting', function( args ) {
			var value;

			args = args.slice();

			if ( value = api( args.shift() ) )
				value.set.apply( value, args );
		});

		api.preview.bind( 'sync', function( events ) {
			$.each( events, function( event, args ) {
				api.preview.trigger( event, args );
			});
			api.preview.send( 'synced' );
		});

        api.preview.bind( 'active', function() {

            api.Events.trigger("startActivePreview");

            if ( api.settings.nonce )
                api.preview.send( 'nonce', api.settings.nonce );

            api.preview.send( 'currentPostId' , api.settings.post.id );

            api.preview.send( 'contextmenu-ready' );
            $(document).trigger("update_sort_row");
        });

		api.preview.send( 'ready' );

        sedApp.editor( 'page_main_row', function( value ) {
    		value.bind( function( to ) {
    			//alert(to);
    		});
        });


        sedApp.editor( 'sheet_width', function( value ) {
    		value.bind( function( to ) {
               $("#sed-sheet-width-style").remove();
               $("<style id='sed-sheet-width-style'>.sed-row-boxed{max-width : " + to + "px !important;}</style>").appendTo($("head"));

              $("#main").find(".sed-row-pb > .sed-pb-module-container").trigger("sedChangedSheetWidth");

    		});
        });


        sedApp.editor( 'page_length', function( value ) {
    		value.bind( function( to ) {

                var targEl = $("#main");

                if(to == "boxed")
                    targEl.addClass( "sed-row-boxed" ).removeClass("sed-row-wide");
                else
                    targEl.addClass( "sed-row-wide" ).removeClass("sed-row-boxed");

                $("#main").find(".sed-row-pb > .sed-pb-module-container").trigger("sedChangedPageLength" , [to]);

    		});
        });


        //for api.Ajax :: than user login ajax render
        api.preview.bind("user_login_done" , function(){
            if(api.currentAjax)
                api.currentAjax.render();
        });


        /* Custom Borders */
        $.each( [ "top" , "left" , "right" , "bottom" ], function(idx , side){
            var bordersArr = $.map(['style' , 'width' , 'color' ], function( prop ) {
            	return 'border_'+ side + '_' + prop;
            });

            api.when.apply( api, bordersArr ).done( function( style , width , color ) {
            	var update , head = $('head') ;
                update = function(index) {
                    if( !api.currentCssSelector )
                        return ;

            	    var css = '',
                        styleId ,
                        element = $( api.currentCssSelector ),
                        newSide = side;
                        //widthEl = element.outerWidth() ,
                        //borderSides = currentBorderSides

                    if( api.isRTL && side == "left" )
                        newSide = "right";
                    else if( api.isRTL && side == "right" )
                        newSide = "left";


                    styleId = _getStyleId( element , 'sed_border_' + side , 'border_' + side );

                    if( !_.isUndefined( width()[api.currentCssSelector] ) ){
                        css += "border-" + newSide + "-width :" + width()[api.currentCssSelector] + "px !important;";
                        //if(val == "right" || val == "left")
                            //widthEl -= width()[api.currentCssSelector];
                    }

                    if( !_.isUndefined( style()[api.currentCssSelector] ) )
                        css += "border-" + newSide + "-style :" + style()[api.currentCssSelector] + " !important;";

                    if( !_.isUndefined( color()[api.currentCssSelector] ) )
                        css += "border-" + newSide + "-color :" + color()[api.currentCssSelector] + " !important;";


                    //css += "max-width : " + widthEl + "px !important;";

            		// Refresh the stylesheet by removing and recreating it.
            		$( "#" + styleId ).remove();
            		style2 = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

                    if($(api.currentCssSelector).attr("sed-layout") == "row"){
                        $(document).trigger("rowCheckBorder" , [api.currentCssSelector]);
                    }
                };

            	$.each( arguments, function(index , value) {
            		this.bind( function(){
                        update( index );
            		});
            	});
            });
        });

        /* Custom Margins */
        margins = $.map(['top' , 'right' , 'bottom' , 'left' , 'lock'], function( prop ) {
        	return 'margin_' + prop;
        });

        api.when.apply( api, margins ).done( function( top , right , bottom , left , lock ) {
        	var update , head = $('head') ,
                lastValue , width;
            update = function(index) {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector ) , propStr;

                styleId = _getStyleId( element , 'sed_margin' , 'margin');

        		if ( !_.isUndefined( top()[api.currentCssSelector] ) )
                    css += "margin-top : " + top()[api.currentCssSelector]+ "px !important;";

        		if ( !_.isUndefined( right()[api.currentCssSelector] ) ){
        		    propStr = ( api.isRTL ) ? "margin-left : " : "margin-right : ";
                    css += propStr + right()[api.currentCssSelector]+ "px !important;";
                }

        		if ( !_.isUndefined( bottom()[api.currentCssSelector] ) )
                    css += "margin-bottom : " + bottom()[api.currentCssSelector]+ "px !important;";

        		if ( !_.isUndefined( left()[api.currentCssSelector] ) ){
        		    propStr = ( api.isRTL ) ? "margin-right : " : "margin-left : ";
                    css += propStr + left()[api.currentCssSelector]+ "px !important;";
                }

        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

            };

        	$.each( arguments, function(index , value) {
        		this.bind( function(){
                    update( index );
        		});
        	});
        });

        /* Custom Paddings */
        paddings = $.map(['top' , 'right' , 'bottom' , 'left' , 'lock'], function( prop ) {
        	return 'padding_' + prop;
        });

        api.when.apply( api, paddings ).done( function( top , right , bottom , left , lock ) {
        	var update , head = $('head') ,
                lastValue , width;
            update = function(index) {

                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector ) , propStr;

                styleId = _getStyleId( element , 'sed_padding' , 'padding');

        		if ( !_.isUndefined( top()[api.currentCssSelector] ) )
                    css += "padding-top : " + top()[api.currentCssSelector]+ "px !important;";

        		if ( !_.isUndefined( right()[api.currentCssSelector] ) ){
        		    propStr = ( api.isRTL ) ? "padding-left : " : "padding-right : ";
                    css += propStr + right()[api.currentCssSelector]+ "px !important;";
                }

        		if ( !_.isUndefined( bottom()[api.currentCssSelector] ) )
                    css += "padding-bottom : " + bottom()[api.currentCssSelector]+ "px !important;";

        		if ( !_.isUndefined( left()[api.currentCssSelector] ) ){
        		    propStr = ( api.isRTL ) ? "padding-right : " : "padding-left : ";
                    css += propStr + left()[api.currentCssSelector]+ "px !important;";
                }
                     //alert( api.currentCssSelector );
        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

            };

        	$.each( arguments, function(index , value) {
        		this.bind( function(){
                    update( index );
        		});
        	});
        });

        /* Custom Radius */
        radius = $.map(['tl' , 'tr' , 'br' , 'bl' , 'lock'], function( prop ) {
        	return 'border_radius_' + prop;
        });

        api.when.apply( api, radius ).done( function( tl , tr , br , bl , lock ) {
        	var update , head = $('head') ,
                lastValue;
            update = function(index) {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector ),
                    side;
                        //widthEl = element.outerWidth() ,
                        //borderSides = currentBorderSides

                styleId = _getStyleId( element , 'sed_radius' , 'radius');

        		if ( !_.isUndefined( tl()[api.currentCssSelector] ) ){

                    if( api.isRTL )
                        side = "tr";
                    else
                        side = "tl";

                    css += siteEditorCss.sedBorderRadius( tl()[api.currentCssSelector] , side );
                }

        		if ( !_.isUndefined( tr()[api.currentCssSelector] ) ){

                    if( api.isRTL )
                        side = "tl";
                    else
                        side = "tr";

                    css += siteEditorCss.sedBorderRadius( tr()[api.currentCssSelector] , side );
                }

        		if ( !_.isUndefined( br()[api.currentCssSelector] ) ){

                    if( api.isRTL )
                        side = "bl";
                    else
                        side = "br";

                    css += siteEditorCss.sedBorderRadius( br()[api.currentCssSelector] , side );
                }

        		if ( !_.isUndefined( bl()[api.currentCssSelector] ) ){

                    if( api.isRTL )
                        side = "br";
                    else
                        side = "bl";

                    css += siteEditorCss.sedBorderRadius( bl()[api.currentCssSelector] , side );
                }

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );
            };

        	$.each( arguments, function(index , value) {
        		this.bind( function(){
                    update( index );
        		});
        	});
        });

        stylesProp = ['trancparency' , 'shadow_color' , 'shadow' , 'position'];
        api.when.apply( api, stylesProp ).done( function( opacity , shadowColor , shadow , position ) {
        	var update , head = $('head');

            update = function() {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector );

                styleId = _getStyleId( element , 'sed_styles' , 'styles');

        		if ( !_.isUndefined( shadow()[api.currentCssSelector] ) ){
        		    if(!shadow()[api.currentCssSelector].values || shadow()[api.currentCssSelector].values== "none"){
                        strSh = "none";
        		    }else{
            		    var shColor = shadowColor()[api.currentCssSelector] || "#000000";
            		    var strSh = shadow()[api.currentCssSelector].values + " " + shColor;
                        strSh += shadow()[api.currentCssSelector].inset !== false ? " inset" : "";
                    }

                    css += siteEditorCss.boxShadow( strSh );
        		}

        		if ( !_.isUndefined( opacity()[api.currentCssSelector] ) )
                    css += siteEditorCss.transparency( opacity()[api.currentCssSelector] );

        		if ( !_.isUndefined( position()[api.currentCssSelector] ) )
                    css += 'position: ' + position()[api.currentCssSelector] + ' !important;';


        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

            };

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        });

        /* font */
        font = $.map(['color', 'style', 'weight', 'size', 'family'], function( prop ) {
        	return 'font_' + prop;
        });

        api.when.apply( api, font ).done( function( color, style, weight, size, family ) {
        	var update , head = $('head');


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

                      //alert(api.currentCssSelector.attr("class"));
        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                      element = $( api.currentCssSelector );

                styleId = _getStyleId( element , 'sed_font' , 'font');

        		if ( !_.isUndefined( color()[api.currentCssSelector] ) )
                    css += 'color: ' + color()[api.currentCssSelector] + ' !important;';

        		if ( !_.isUndefined( style()[api.currentCssSelector] ) )
                    css += 'font-style: ' + style()[api.currentCssSelector] + ' !important;';

        		if ( !_.isUndefined( size()[api.currentCssSelector] ) )
                    css += 'font-size: ' + size()[api.currentCssSelector] + 'px !important;';

        		if ( !_.isUndefined( weight()[api.currentCssSelector] ) )
                    css += 'font-weight: ' + weight()[api.currentCssSelector] + ' !important;';

        		if ( !_.isUndefined( family()[api.currentCssSelector] ) ){
        		    api.typography.loadFont( family()[api.currentCssSelector] );
                    css += 'font-family: ' + family()[api.currentCssSelector] + ' ';
                }

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		styleEl = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

        	};

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        });

        textProp = $.map(['decoration', 'align', 'shadow', 'shadow_color'], function( prop ) {
        	return 'text_' + prop;
        });

        textProp.push( 'line_height' );
                                                                                           //
        api.when.apply( api, textProp ).done( function( decoration, align, shadow, shadow_color, line_height ) {
        	var update , head = $('head') ;


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

                      //alert(api.currentCssSelector.attr("class"));
        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                      element = $( api.currentCssSelector ) ,
                      text_shadow , alignVal , newVal;

                styleId = _getStyleId( element , 'sed_text' , 'text');

        		if ( !_.isUndefined( decoration()[api.currentCssSelector] ) )
                    css += 'text-decoration: ' + decoration()[api.currentCssSelector] + ' !important;';

        		if ( !_.isUndefined( align()[api.currentCssSelector] ) ){
                        alignVal = align()[api.currentCssSelector];
                        //widthEl = element.outerWidth() ,
                        //borderSides = currentBorderSides

                    if( api.isRTL && alignVal == "left" )
                        newVal = "right";
                    else if( api.isRTL && alignVal == "right" )
                        newVal = "left";
                    else
                        newVal = alignVal;


                    css += 'text-align: ' + newVal + ' !important;';
                }

        		if ( !_.isUndefined( shadow()[api.currentCssSelector] ) )
                    text_shadow = shadow()[api.currentCssSelector];

                if ( !_.isUndefined( shadow_color()[api.currentCssSelector] ) && !_.isUndefined( shadow()[api.currentCssSelector] ) && shadow()[api.currentCssSelector] != "none" )
                    text_shadow += " " + shadow_color()[api.currentCssSelector];

                if ( !_.isUndefined( text_shadow ) )
                    css += 'text-shadow: ' + text_shadow + ' !important;';

        	   	if ( !_.isUndefined( line_height()[api.currentCssSelector] ) )
                    css += 'line-height: ' + line_height()[api.currentCssSelector] + 'px !important;';

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		styleEl = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

        	};

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        });


        /*sedApp.editor( 'background_color', function( value ) {
    		value.bind( function( to ) {
    			api.preview.send( 'update_gradient' , api.currentCssSelector , to );
    		});
        });*/

        parallax = $.map(['ratio' , 'image'], function( prop ) {
        	return 'parallax_background_' + prop;
        });

        api.when.apply( api, parallax ).done( function( ratio, image ) {
        	var update , ratioNum;


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

        		if ( !_.isUndefined( image()[api.currentCssSelector] ) && image()[api.currentCssSelector]  ){

                    if( !_.isUndefined( ratio()[api.currentCssSelector] ) )
                        ratioNum = ratio()[api.currentCssSelector];
                    else
                        ratioNum = 0.5;

                    $( api.currentCssSelector ).attr("data-stellar-background-ratio" , ratioNum );
                    $( api.currentCssSelector ).data("stellarBackgroundRatio" , ratioNum );
                    $(window).stellar('refresh');
                }else{
                    $( api.currentCssSelector ).removeData("stellarBackgroundRatio");
                    $( api.currentCssSelector ).removeAttr("data-stellar-background-ratio");
                    $(window).stellar('refresh');
                }

            };

          	$.each( arguments, function() {
          		this.bind( update );
          	});
        });

        /* Custom Backgrounds */
        bg = $.map(['color', 'image', 'position', 'image_scaling', 'attachment', 'gradient'], function( prop ) {
        	return 'background_' + prop;
        });

        api.when.apply( api, bg ).done( function( color, image, position, image_scaling, attachment, gradient ) {
        	var update ,
                bgSize = "auto",
                bgRepeat = "no-repeat", bgImg,
                head = $('head'), bgColor;


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '',
                      styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                      element = $( api.currentCssSelector ) ,
                      sGradient = !_.isUndefined( gradient()[api.currentCssSelector] ) && gradient()[api.currentCssSelector] ,
                      bgImage  = !_.isUndefined( image()[api.currentCssSelector] ) && image()[api.currentCssSelector] && image()[api.currentCssSelector] != "none";

                styleId = _getStyleId( element , 'sed_background' , 'background');

                      //element.toggleClass( elementClass , !! ( color()[api.currentCssSelector] || image()[api.currentCssSelector] ) )

        		// The body will support custom backgrounds if either
        		// the color or image are set.

        		if ( !_.isUndefined( color()[api.currentCssSelector] ) )
                    css += 'background-color: ' + color()[api.currentCssSelector] + ' !important;';

        		if ( !_.isUndefined( image()[api.currentCssSelector] ) && !sGradient){

        		    if( !image()[api.currentCssSelector] || image()[api.currentCssSelector] == "none" )
        			    css += 'background-image: none !important;';
                    else
                        css += 'background-image: url("' + image()[api.currentCssSelector] + '") !important;';

                }else if( !bgImage && sGradient )
                    css += siteEditorCss.gradient( gradient()[api.currentCssSelector].start , gradient()[api.currentCssSelector].end , gradient()[api.currentCssSelector] );
                else if( bgImage &&  sGradient )
                    css += siteEditorCss.gradient( gradient()[api.currentCssSelector].start , gradient()[api.currentCssSelector].end , gradient()[api.currentCssSelector] , image()[api.currentCssSelector] ) ;
                else if ( _.isUndefined( image()[api.currentCssSelector] ) && !_.isUndefined( gradient()[api.currentCssSelector] ) && !gradient()[api.currentCssSelector] )
                    css += 'background-image: none !important;';
                /*if( gradient()[api.currentCssSelector] ) {
                    api.preview.send( 'update_gradient' , api.currentCssSelector , color()[api.currentCssSelector] );
                }*/

        		if ( bgImage ) {

                    if ( !_.isUndefined( position()[api.currentCssSelector] ) )
        			    css += 'background-position: ' + position()[api.currentCssSelector] + ';';

                    if ( !_.isUndefined( attachment()[api.currentCssSelector] ) )
                        css += 'background-attachment: ' + attachment()[api.currentCssSelector] + ' !important;';

                    if( image_scaling()[api.currentCssSelector] ) {
                        switch ( image_scaling()[api.currentCssSelector] ) {
                           case "fullscreen":
                                bgSize = "100% 100%";
                           break;
                           case "fit":
                                bgSize = "100% auto";
                                bgRepeat = "repeat-y";
                           break;
                           case "tile":
                                bgSize = "auto";
                                bgRepeat = "repeat";
                           break;
                           case "tile-horizontally":
                                bgSize = "auto";
                                bgRepeat = "repeat-x";
                           break;
                           case "tile-vertically":
                                bgSize = "auto";
                                bgRepeat = "repeat-y";
                           break;
                           case "normal":
                                bgSize = "auto";
                                bgRepeat = "no-repeat";
                           break;
                           case "cover":
                                bgSize = "cover";
                                //bgRepeat = "no-repeat";
                           break;
                        }

                        css += 'background-repeat: ' + bgRepeat + ' !important;';
                        css += siteEditorCss.backgroundSize( bgSize );
                    }


        		}

                if( gradient()[api.currentCssSelector] || image_scaling()[api.currentCssSelector])
                    css += "behavior: url(" + LIBBASE.url + "PIE/PIE.htc" + ");";

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

                if( !_.isUndefined( image()[api.currentCssSelector] )  && api('parallax_background_image')() ){
                    $(window).stellar('refresh');
                }

        	};

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        });

	});

})( sedApp, jQuery );
