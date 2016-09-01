/**
 * siteEditorControls.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */                           // , "siteEditor/siteEditorCss"
define("siteEditor/pbModulesControls",
["siteEditor/dom/Sizzle", "siteEditor/sedAppClass"],
function( $, sedApp ) {
	var api = sedApp.editor;
    var siteEditorCss = new sedApp.css;
    var undoManager = sedApp.undoManager;
    api.MSElement = {};
    api.modulesShortcodesAttrs = api.modulesShortcodesAttrs || {} ;
    api.createRelations = api.createRelations || {};
    api.currenStyleEditorContolsValues = api.currenStyleEditorContolsValues || {};

    api.SiteEditorControls = api.Control.extend({

		ready: function() {
            var control = this;

            this.isModuleControl = !_.isUndefined( control.params ) && !_.isUndefined( control.params.category ) && control.params.category == "module-settings";

            this.isStyleControl = !_.isUndefined( control.params ) && !_.isUndefined( control.params.category ) && control.params.category == "style-editor";

            if( this.isModuleControl ){
                this.attr = control.params.attr_name;

                this.shortcode = control.params.shortcode;

                if(!this.attr){
                    //api.log("attr not valid this attr is : " , this.attr);
                    return ;
                }

            }

            if( this.isStyleControl ){
                this.cssSelector = ( !_.isUndefined( control.params.selector ) ) ? control.params.selector : '';
                this.cssSettingType = ( !_.isUndefined( control.params.css_setting_type ) ) ? control.params.css_setting_type : "module";
            }

            if( !_.isUndefined( control.params.default_value ) )
                control.defaultValue = control.params.default_value;
            else
                control.defaultValue = "";

            this._ready();

            if( !_.isUndefined( this.extraOpt ) && !_.isUndefined( this.extraOpt.attrs ) ){
                this.update( this.extraOpt.attrs );
            }else if( !_.isUndefined( this.extraOpt ) ){
                this.update( this.extraOpt );
            }else{
                this.update( );
            }

        },

        refresh : function( val ) {
            var control = this ;

            if( this.isModuleControl ){
                var attrStatus;

                control.previewer.send( 'current_shortcode', control.shortcode );
                control.previewer.send( 'current_attr', control.attr );

                if( !_.isUndefined( control.params.sub_shortcodes_update ) && !_.isUndefined( control.params.sub_shortcodes_update["class"] ) && !_.isUndefined( control.params.sub_shortcodes_update.attr ) ){
                    control.previewer.send( 'subShortcodesAttrUpdate', {
                        "class" : control.params.sub_shortcodes_update["class"] ,
                        "attr"  : control.params.sub_shortcodes_update.attr ,
                        "value" : val,
                    });
                }

                if( !_.isUndefined( control.params.force_refresh ) && control.params.force_refresh === true ){
                    //api.previewer.refresh();
    				attrStatus = "force_refresh";  // "force_refresh" || "normal"
                }else{
    				attrStatus = "normal";  // "force_refresh" || "normal"
                }

    			control.previewer.send( 'current_attr_status', attrStatus );
            }

            this._refresh( val );

            if( this.isStyleControl ){
                var currentCssSelector;

                if( this.cssSettingType == "module" && this.cssSelector ){
                    currentCssSelector = ( this.cssSelector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + this.cssSelector : '[sed_model_id="' + api.currentTargetElementId + '"]';
                }else if( this.cssSettingType == "module" ){
                    currentCssSelector = api.currentCssSelector;
                }else{
                    currentCssSelector = this.cssSelector;
                }

                this.previewer.send( 'current_css_setting_type', this.cssSettingType );
                this.previewer.send( 'current_css_selector', currentCssSelector );

                var settingId = this.settings["default"].id;

                if( _.isUndefined( api.currenStyleEditorContolsValues[currentCssSelector] ) )
                    api.currenStyleEditorContolsValues[currentCssSelector] = {};

                api.currenStyleEditorContolsValues[currentCssSelector][settingId] = val;  console.log( "api.currenStyleEditorContolsValues------------" , api.currenStyleEditorContolsValues );
            }

            this.setValue( val );

            this.currentValue = val;

            if( this.isModuleControl && !_.isUndefined( control.params.sub_category ) ){
                this.setAtts( val );
                api.Events.trigger("moduleControlRefresh" , control.params.sub_category , control , val );
            }

        },

        setValue : function( val ){
            var control = this ;

            if( $.isArray( val ) ){
                val = val.join(",");
            }

            control.setting.set( val );
        },

        update  : function( value ) {
            var control = this , cValue ;

            if( this.isModuleControl && !_.isUndefined( value ) && _.isObject( value ) ){

                var elementAttrs = $.extend( true , {} , value );

                if( !_.isUndefined( elementAttrs ) && !_.isUndefined( elementAttrs[control.attr] ) ){
                    cValue = elementAttrs[control.attr];
                    this.setAtts( elementAttrs  , "all");

                }else{
                    cValue = control.defaultValue;
                }

            }else if( !_.isUndefined( value ) && !_.isObject( value ) ){
                cValue = value;
            }else{
                cValue = control.defaultValue;
            }

            this.currentValue = cValue;

            this._update( cValue );

            if( this.isModuleControl ){
                api.Events.trigger("moduleControlUpdate" , control.shortcode , control.attr , cValue );
            }

        },

        setAtts : function( values , type ){

            if(!values)
                return ;

            type = (!type) ? "current" : type;  //"current" || "all"

            switch ( type ) {
              case "current" :
                  if( _.isUndefined( api.modulesShortcodesAttrs[this.shortcode] ) )
                      api.modulesShortcodesAttrs[this.shortcode] = {};

                  api.modulesShortcodesAttrs[this.shortcode][this.attr] =  values;
              break;
              case "all" :
                  api.modulesShortcodesAttrs[this.shortcode] =  values;
              break;
            }

        },

        _ready   : function( ) { },

        _update  : function( val ) { },

        _refresh : function( val ) { }

    });

	api.SedDropdownControl = api.SiteEditorControls.extend({

		_ready: function() {

			var control = this,
			dropdown = this.container.find('.sed-dropdown');
            //$thisElement, $prevElement ;

            this.dropdown = dropdown;

            if ( !_.isUndefined( control.params.options_selector ) ) {
                this.statuses = $( control.params.options_selector, dropdown );
            }else{
                return ;
            }

            if ( !_.isUndefined( control.params.selected_class ) ) {
                this.selectedClass = control.params.selected_class;

                this.updateClass = function(element){
                     dropdown.find( "." + control.selectedClass ).removeClass( control.selectedClass );
                     element.addClass( control.selectedClass );
                }

            }

            this.statuses.on("click" ,function(e){
                var value;

                if( typeof control.getValue == "function" )
                    value = control.getValue( $(this) );
                else
                    value = $(this).attr("data-value");

                if ( control.params.selected_class ) {
                    control.updateClass( $(this) );
                }

                //$thisElement = $(this);

                control.refresh( value );
            });

		},

        _update: function( currValue ){
            var control = this,
                //$prevElement,
                isGradient = !_.isUndefined( control.params ) && !_.isUndefined( control.params.type ) && control.params.type == "gradient",
                newCurrValue;

            if( !control.statuses ){
                if( !_.isUndefined( console ) && !_.isUndefined( console.error ) )
                    console.error("not exsist or undefined options(statuses) for this control");
                else
                    alert("not exsist or undefined options(statuses) for this control");

                return ;
            }

            if ( control.params.selected_class ) {
                control.dropdown.find( "." + control.selectedClass ).removeClass( control.selectedClass );
            }

            if( isGradient && _.isObject( currValue ) ){
                newCurrValue = _.clone( currValue );
                delete newCurrValue.start;
                delete newCurrValue.end;
            }else
                newCurrValue = currValue;

            control.statuses.each(function(index, el){
                var value;
                if( typeof control.getValue == "function" ){

                    if( isGradient ){
                        value = control.getValue( $(this) , "update" );
                    }else
                        value = control.getValue( $(this) );

                }else
                    value = $(this).attr("data-value");

                if( ( !_.isObject( newCurrValue ) && newCurrValue == value ) || ( _.isObject( newCurrValue ) && _.isEqual( value , newCurrValue ) ) ){
                    //$prevElement = $(this);
                    if ( control.params.selected_class ) {
                        control.updateClass( $(this) );
                    }
                    return false;
                }
            });
        }

    });

	api.GradientControl = api.SedDropdownControl.extend({
		_ready: function() {
			var control = this;

            this.getValue = function(element , type ){
                if(element.hasClass("sed-no-gradient")){
                    return "";
                }else{

                    if( !_.isUndefined( type ) && type == "update" ){

                        return {
                            type            : $( element ).attr("data-gradient-type"),
                            opacity         : $( element ).attr("data-gradient-opacity"),
                            percent         : $( element ).attr("data-gradient-percent"),
                            orientation     : $( element ).attr("data-gradient-Orientation")
                        };

                    }else{

                        /*var gColor = api.fn.gradientGetStartEndColor( control ) ,
                            startColor  =  gColor.start,
                            endColor    =  gColor.end;*/

                        return {
                            /*start           : startColor,
                            end             : endColor,*/
                            type            : $( element ).attr("data-gradient-type"),
                            opacity         : $( element ).attr("data-gradient-opacity"),
                            percent         : $( element ).attr("data-gradient-percent"),
                            orientation     : $( element ).attr("data-gradient-Orientation")
                        };

                    }
                }
            };

            api.SedDropdownControl.prototype._ready.apply( this, [] );
		}
    });

    api.SpinnerControl = api.SiteEditorControls.extend({

        _ready: function() {

			var control = this ,
				spinner = this.container.find('.sed-spinner');

            this.spinnerOptions =  {};
            this.spinner = spinner;

            if ( typeof control.params.min !== undefined ) {
                this.spinnerOptions.min = control.params.min;
            }

            if ( typeof control.params.max !== undefined ) {
                this.spinnerOptions.max = control.params.max;
            }

            if ( control.params.step ) {
                this.spinnerOptions.step = control.params.step;
            }

            if ( control.params.page ) {
                this.spinnerOptions.page = control.params.page;
            }

            if ( control.params.lock &&  control.params.lock.id &&  control.params.lock.spinner ) {
                 this.lock = $("#" + control.params.lock.id);
                 this.spinnerConnect = $(control.params.lock.spinner);
                 this.controlsConnect = control.params.lock.controls || [];
            }

            spinner.spinner(this.spinnerOptions);
                        //spin   spinchange
            spinner.on("spinstop keyup", function(e,ui){
                var spVal = $(this).val();

                if( !_.isUndefined( control.lock ) && control.lock.prop('checked') && !_.isUndefined( control.spinnerConnect ) ){
                    control.spinnerConnect.val( spVal );

                    _.each(control.controlsConnect , function( controlId ){
                        var spControl = api.control.instance( controlId );

                        spControl.refresh( spVal );

                    });
                }

                control.refresh( spVal );
            });

        },

        _update: function( val ){
            this.spinner.val( val );
        }

    });


    api.SpinnerLockControl = api.SiteEditorControls.extend({

        _ready: function() {

			var control = this ,
				spinnerLock = this.container.find('.sed-lock-spinner');

            this.spinnerLock = spinnerLock;

            if ( !_.isUndefined( control.params.spinner ) && !_.isUndefined( control.params.controls ) ) {
                this.spinners = $( control.params.spinner );
                this.controls = control.params.controls || [];
            }else{
                return ;
            }

            spinnerLock.livequery(function(){
                $(this).change(function(){
                    var isChecked = $(this).prop('checked');

                    if(isChecked){

                        var corners = [];

                        control.spinners.each(function(index,element){
                            corners.push($(this).val());
                        });

                        var minCorner = Math.min.apply(Math, corners);
                        control.spinners.each(function(index,element){
                            $(this).val( minCorner );
                        });

                        _.each(control.controls , function( controlId ){
                            var spControl = api.control.instance( controlId );

                            spControl.refresh( minCorner );

                        });

                    }

                    control.refresh( isChecked );

                });
            });

        },

        _update: function( val ){
            this.spinnerLock.prop( 'checked', val );
        }

    });


    api.ColorControl = api.SiteEditorControls.extend({

        _ready: function() {

			var control = this ,
				picker = this.container.find('.sed-colorpicker');

            this.picker = picker;

            var colorPickerOptionBG = _.clone( colorPickerOption );

            picker.val( '' );

            if( !_.isUndefined( this.params.show_input ) )
                colorPickerOptionBG.showInput = this.params.show_input;

            var _refresh = function( color ){

                color = _.isNull( color ) ? "transparent" : color.toString();
                control.refresh( color );

            };

            var _lazyRefresh = _.debounce(function( color ){
                _refresh( color );
            }, 20);

            colorPickerOptionBG.change = function (color) {
                _refresh( color );
            };

            colorPickerOptionBG.move = function (color) {
                _lazyRefresh( color );
            };

            colorPickerOptionBG.dragstop = function (color) {
                _refresh( color );
            };

            colorPickerOptionBG.hide = function(color) {
                _refresh( color );
            }

            this.picker.spectrum(colorPickerOptionBG);
        },

        _update: function( val ){
            val = ( val == "transparent" ) ? "" : val;
            this.picker.spectrum("set", val);
        }

    });

/*
	api.ColorControl = api.Control.extend({
		ready: function() {
			var control     = this,
				picker      = this.container.find('.sed-colorpicker'),
                oldValue,
                $thisValue  = control.setting();

            this.picker = picker;

            this.defaultValue = !_.isUndefined( this.params.default_value ) ? this.params.default_value : "	transparent";

            var colorPickerOptionBG = _.clone( colorPickerOption ) ,
                defColor = $thisValue || this.defaultValue;

            var rgbaColor = tinycolor( defColor );
            rgbDefColor = rgbaColor.toRgb();

            defColor = ( defColor == "transparent" || (rgbDefColor.r == 0 && rgbDefColor.g == 0 && rgbDefColor.b == 0 && rgbDefColor.a == 0 ) ) ? "" : defColor;

            this.picker.val( defColor );

            var _refresh = function( color ){

                color = _.isNull( color ) ? "transparent" : color.toString();

                $thisValue = color;

                control.currentValue = color;
                control.setting.set( $thisValue );

				if( !_.isUndefined( control.params.shortcode ) && !_.isUndefined( control.params.attr_name ) )
					api.Events.trigger("moduleControlRefresh" , control.params.shortcode , control.currentValue);

            };

            var _lazyRefresh = _.debounce(function( color ){
                _refresh( color );
            }, 20);

            colorPickerOptionBG.change = function (color) {
                _refresh( color );
            };

            colorPickerOptionBG.move = function (color) {
                _lazyRefresh( color );
            };

            colorPickerOptionBG.dragstop = function (color) {
                _refresh( color );
            };

            colorPickerOptionBG.hide = function(color) {
                _refresh( color );
            }

                   //control.setting.set( false );
            this.picker.spectrum(colorPickerOptionBG);

		},

        // && !_.isObject( targetElement ) for when module all settings update ,
        //modules send shortcode attrs === targetElement is
        // not valid one targetElement
        update: function( value ){
            var control = this,$thisValue = control.setting();

            var defColor = value || $thisValue || control.defaultValue || "transparent";

            var rgbaColor = tinycolor( defColor );
            rgbDefColor = rgbaColor.toRgb();

            defColor = ( defColor == "transparent" || (rgbDefColor.r == 0 && rgbDefColor.g == 0 && rgbDefColor.b == 0 && rgbDefColor.a == 0 ) ) ? "" : defColor;

            control.currentValue = defColor;

            control.picker.spectrum("set", defColor );

        }

	});
*/

    api.FormModel = api.Class.extend({

        initialize: function( element , params ) {
            var self = this ,
                synchronizer = api.MSElement.synchronizer.html,
				type;

			this.element = element;
			this.events = '';

            params = $.extend( true , {
                refresh  : function(){}
            }, params || {} );

            $.extend( this, params );

			if ( this.element.is('input, select, textarea') ) {
				this.events += 'change';
				synchronizer = api.MSElement.synchronizer.val;

                if ( this.element.is('select') ) {
                    synchronizer = api.MSElement.synchronizer.select;
                }

				if ( this.element.is('input') ) {
					type = this.element.prop('type');
					if ( api.MSElement.synchronizer[ type ] )
						synchronizer = api.MSElement.synchronizer[ type ];

                    var inputTypes = ['text', 'password' , 'tel' , 'time' , 'date' , 'url' , 'email' , 'search'  ];
					if ( $.inArray( type , inputTypes ) > -1 ) {
						this.events += ' keyup';
					} else if ( 'range' === type ) {
						this.events += ' input propertychange';
					}

				} else if ( this.element.is('textarea') ) {
					this.events += ' keyup';
				}
			}

            $.extend( this, synchronizer );

			this.element.bind( this.events, function(e){
                self.refresh( self.getVal() );
			});

        },


    });

  	api.MSElement.synchronizer = {};

  	$.each( [ 'html', 'val' ], function( i, method ) {
  		api.MSElement.synchronizer[ method ] = {
  			updateField: function( to ) {
  				this.element[ method ]( to );
  			},
  			getVal: function() {
  				return this.element[ method ]();
  			}
  		};
  	});

  	api.MSElement.synchronizer.checkbox = {
  		updateField: function( to ) {
  			this.element.prop( 'checked', to );
  		},
  		getVal: function() {
  			return this.element.prop( 'checked' );
  		}
  	};

  	api.MSElement.synchronizer.radio = {
  		updateField: function( to ) {
  			this.element.filter( function() {
  				return this.value === to;
  			}).prop( 'checked', true );
  		},
  		getVal: function() {
  			return this.element.filter( ':checked' ).val();
  		}
  	};

  	api.MSElement.synchronizer.select = {
  		updateField: function( to ) {
  		    if( this.element.attr("multiple") == "multiple" && to ){
                to = to.split( "," );
  		    }

            this.element.val( to );

  		},
  		getVal: function() {
  			return this.element.val();
  		}
  	};

	/*api.StyleEditorElements = api.SiteEditorControls.extend({
		ready: function() {
			var control = this,
                $thisValue = control.setting() ,
                element = control.container.find('.sed-module-element-control');

            control.defaultValue = control.params.default_value || "";

            control.targetElement = api.currentCssSelector;
                                           ////api.log( element );
            this.formModel = new api.FormModel( element , {
                refresh  : function( val ){
                    if(_.isUndefined( control.targetElement )){
                        alert("pls ad control.targetElement = api.currentTargetElementId; instade of this if condition");
                        return ;
                    }
                    control.previewer.currentSelector = control.targetElement;
                    //set value
                    $thisValue[control.targetElement] = val;

					control.currentValue = val;
                    control.setting.set( $thisValue );

					if( !_.isUndefined( control.params.shortcode ) && !_.isUndefined( control.params.attr_name ) )
						api.Events.trigger("moduleControlRefresh" , control.params.shortcode , control.params.attr_name , val );
                }
            });

            this.update();

		},

        update: function( targetElement ){
            var control = this,$thisValue = control.setting();

            control.targetElement = ( !_.isUndefined( targetElement ) && !_.isObject( targetElement ) ) ? targetElement : api.currentCssSelector;

            if( control.targetElement && !_.isUndefined( $thisValue[control.targetElement] )  ){
                attrValue = $thisValue[control.targetElement];
            }else{
                attrValue = control.defaultValue;

				if( !_.isUndefined( control.params.force_refresh_setting ) && control.params.force_refresh_setting ){

					control.previewer.currentSelector = control.targetElement;
					//set value
					$thisValue[control.targetElement] = _.clone( attrValue );
					control.setting.set( $thisValue );

				}

            }

			control.currentValue = attrValue;

            this.formModel.updateField( attrValue );
        }

	});*/
    //api.ModulesElement
    api.SiteEditorElements = api.SiteEditorControls.extend({

		_ready: function() {
            var control = this ,
                element = control.container.find('.sed-module-element-control');

            this.formModel = new api.FormModel( element , {
                refresh  : function( val ){
                    control.refresh( val );
                }
            });

        },

        _update  : function( val ) {
            this.formModel.updateField( val );
        },

    });

	/*api.SiteEditorElements = api.Control.extend({
		ready: function() {
			var control = this,
                $thisValue = control.setting() ,
                element = control.container.find('.sed-element-control');



            this.formModel = new api.FormModel( element , {
                refresh  : function( val ){

                    control.currentValue = val;
                    control.setting.set( val );

					if( !_.isUndefined( control.params.shortcode ) && !_.isUndefined( control.params.attr_name ) )
						api.Events.trigger("moduleControlRefresh" , control.params.shortcode , control.params.attr_name , val );
                }
            });

            this.update();
		},

        update: function( newVal ){
            var control = this,$thisValue = control.setting();

            if( !_.isUndefined( newVal )  ){
                attrValue = newVal;
            }else{
                attrValue = $thisValue;
            }

            control.currentValue = attrValue;
            this.formModel.updateField( attrValue );
        }

	});*/

    api.ChangeMediaControl = api.Class.extend({

        initialize : function( control , options ) {
			var self = this,
                changeButton    = control.container.find('.sed-change-media-button');

            this.options = {};

            $.extend( this.options , options || {}  );

            if( !_.isUndefined( control.params.subtypes ) ){
                this.options.subtypes = _.clone( control.params.subtypes );
            }

            changeButton.livequery(function(){
                $(this).click(function(){
                    api.previewer.trigger( 'openMediaLibrary' , {
                        options :  {
                            'media' : self.options
                        }
                    });
                    //control.refresh(  );
                });
            });

            /*if( control.has_remove_btn ){
                control.removeBtn.livequery(function(){
                    $(this).click(function(){
                        control.refresh("");
                    });
                });
            }

            if( !_.isUndefined( this.options.eventKey ) ){

                api.previewer.bind( "sedChangeMedia" +  this.options.eventKey ,  function( attachment ) {

                    self.changeMedia( attachment );

                });

            }*/
        } ,

        changeMedia : function(){

        }

    });


    api.ChangeVideoControl = api.SiteEditorControls.extend({

		_ready : function() {

            var control = this ,
                options =  {
                    "supportTypes"       : ["video"],
                    "selctedType"        : "single",   // single or multiple
                    "dialog"     : {
                        "title"     :  !_.isUndefined( control.params.lib_title ) ? control.params.lib_title : api.I18n.change_video_library ,
                        "buttons"   :    [
                            {
                                "title"    :   !_.isUndefined( control.params.btn_title ) ? control.params.btn_title : api.I18n.change_video_btn ,
                                "type"     :   "change_media" ,
    							"select_validation" :   true
                            }
                        ]
                    },
                };

            this.fieldUrl    = control.container.find(".media-url-field");
            this.removeBtn   = control.container.find(".remove-media-src-btn");
            //this.subtypes    = control.params.subtypes || ["mp4"];

            this.removeBtn.livequery(function(){
                $(this).click(function(){
                    control.refresh("");
                });
            });

            options.eventKey = "video_" + control.id;

            api.previewer.bind( "sedChangeMedia" +  options.eventKey ,  function( attachment ) {

                control.refresh( attachment.id );

            });

            api.ChangeMediaControl.prototype.initialize.apply( this, [ control , options ] );

        },

        _update : function( val ) {
            this._updateUrlField( val );
        },

        _refresh : function( val ) {
            this._updateUrlField( val );
        },

        _updateUrlField : function( attach_id ){
            var attachment = _.findWhere( api.attachmentsSettings , { id : attach_id}  );

            if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) && !_.isUndefined( attachment.url ) ){
                this.fieldUrl.val( attachment.url );
            }else{
                this.fieldUrl.val( "" );
            }
        },

    });

    api.ChangeAudioControl = api.SiteEditorControls.extend({

		_ready : function( control, type  , options ) {

            var control = this ,
                options =  {
                    "supportTypes"       : ["audio"],
                    "selctedType"        : "single",   // single or multiple
                    "dialog"     : {
                        "title"     :    !_.isUndefined( control.params.lib_title ) ? control.params.lib_title : api.I18n.change_audio_library,
                        "buttons"   :    [
                            {
                                "title"    :   !_.isUndefined( control.params.btn_title ) ? control.params.btn_title : api.I18n.change_audio_btn ,
                                "type"     :   "change_media" ,
    							"select_validation" :   true
                            }
                        ]
                    },
                };

            this.fieldUrl    = control.container.find(".media-url-field");
            this.removeBtn   = control.container.find(".remove-media-src-btn");
            //this.subtypes    = control.params.subtypes || ["mp3"];

            this.removeBtn.livequery(function(){
                $(this).click(function(){
                    control.refresh("");
                });
            });

            options.eventKey = "audio_" + control.id;

            api.previewer.bind( "sedChangeMedia" +  options.eventKey ,  function( attachment ) {

                control.refresh( attachment.id );

            });

            api.ChangeMediaControl.prototype.initialize.apply( this, [ control , options ] );

        },

        _update : function( val ) {
            this._updateUrlField( val );
        },

        _refresh : function( val ) {
            this._updateUrlField( val );
        },

        _updateUrlField : function( attach_id ){
            var attachment = _.findWhere( api.attachmentsSettings , { id : attach_id}  );

            if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) && !_.isUndefined( attachment.url ) ){
                this.fieldUrl.val( attachment.url );
            }else{
                this.fieldUrl.val( "" );
            }
        },

    });

    api.ChangeFilesControl = api.SiteEditorControls.extend({

		_ready : function( control, type  , options ) {

            var control = this ,
                options =  {
                    "supportTypes"       :   !_.isUndefined( control.params.support_types ) && $.isArray( control.params.support_types )  ? control.params.support_types : ["archive" , "document" , "spreadsheet" , "interactive" , "text"],
                    "selctedType"        : "single",   // single or multiple
                    "dialog"     : {
                        "title"     :    !_.isUndefined( control.params.lib_title ) ? control.params.lib_title : api.I18n.change_file_library,
                        "buttons"   :    [
                            {
                                "title"    :   !_.isUndefined( control.params.btn_title ) ? control.params.btn_title : api.I18n.change_file_btn ,
                                "type"     :   "change_media" ,
    							"select_validation" :   true
                            }
                        ]
                    },
                };


            this.fieldUrl    = control.container.find(".media-url-field");
            this.removeBtn   = control.container.find(".remove-media-src-btn");
            //this.subtypes    = control.params.subtypes || ["pdf"];

            this.removeBtn.livequery(function(){
                $(this).click(function(){
                    control.refresh("");
                });
            });

            options.eventKey = "file_" + control.id;

            api.previewer.bind( "sedChangeMedia" +  options.eventKey ,  function( attachment ) {

                control.refresh( attachment.id );

            });

            api.ChangeMediaControl.prototype.initialize.apply( this, [ control , options ] );

        },

        _update : function( val ) {
            this._updateUrlField( val );
        },

        _refresh : function( val ) {
            this._updateUrlField( val );
        },

        _updateUrlField : function( attach_id ){
            var attachment = _.findWhere( api.attachmentsSettings , { id : attach_id}  );

            if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) && !_.isUndefined( attachment.url ) ){
                this.fieldUrl.val( attachment.url );
            }else{
                this.fieldUrl.val( "" );
            }
        },

    });

    api.ChangeImageControl = api.SiteEditorControls.extend({

		_ready: function() {

            var control = this ,
                removeButton    = control.container.find('.remove-img-btn');
                options =  {
                    "supportTypes"       : ["image"],
                    "selctedType"        : "single",   // single or multiple
                    "dialog"     : {
                        "title"     :    !_.isUndefined( control.params.lib_title ) ? control.params.lib_title : api.I18n.change_img_library,
                        "buttons"   :    [
                            {
                                "title"    :   !_.isUndefined( control.params.btn_title ) ? control.params.btn_title : api.I18n.change_img_btn ,
                                "type"     :   "change_media" ,
                                "select_validation" :   true
                            }
                        ]
                    },
                };


            if( !_.isUndefined( control.params.rel_size_control ) ){
                this.relSizeControlId = control.params.rel_size_control;
            }

            options.eventKey = "image_" + control.id;

            control.imgDemo = control.container.find("img.change_img");

            removeButton.livequery(function(){
                $(this).click(function(){

                    control._updateImageDemo( 0 );

                    if( control.isStyleControl ){
                        control.refresh( "none" );
                    }else{
                        control.refresh( "" );
                    }

                });
            });

            api.previewer.bind( "sedChangeMedia" +  options.eventKey ,  function( attachment ) {

                control._updateImageDemo( attachment.id );

                if( control.isStyleControl ){
                    var imgUrl = control._getImageUrl( attachment.id , "full" ) + "?attachment_id=" + attachment.id;
                    control.refresh( imgUrl );
                }else{
                    control.refresh( attachment.id );
                }

                //start update image sizes in settings dialog (update options in select field)
                api.previewer.trigger( 'addAttachmentSizes' , {
                    id    : attachment.id  ,
                    sizes : attachment.sizes
                });

                control._updateImageSizes( attachment.id );

            });

            api.ChangeMediaControl.prototype.initialize.apply( this, [ control , options ] );
        },

        _update  : function( value ) {
            var attach_id = 0;
            if( _.isString( value ) && value.indexOf("?attachment_id=") > -1 ){
                attach_id = parseInt( value.substring( value.indexOf("?attachment_id=") + 15 ) );
            }else{
                attach_id = parseInt( value );
            }

            this._updateImageDemo( attach_id );

            this._updateImageSizes( attach_id );

        },

        _updateImageDemo : function( attach_id ){
            var imgUrl = this._getImageUrl( attach_id );
            this.imgDemo.attr("src" , imgUrl );
        },

        _getImageUrl : function( attach_id , size ){
            var attachment = _.findWhere( api.attachmentsSettings , { id : attach_id}  );

            if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) ){
                if( !_.isUndefined( size ) && !_.isUndefined( attachment.sizes ) && !_.isUndefined( attachment.sizes[size] ) ){
                    var imgUrl = attachment.sizes[size].url;
                }else{
                    var imgUrl = ( !_.isUndefined( attachment.sizes ) && !_.isUndefined( attachment.sizes.thumbnail ) ) ? attachment.sizes.thumbnail.url : attachment.url;
                }
            }else{
                var imgUrl = SED_BASE_URL + 'images/no-image.jpg';
            }

            return imgUrl;
        },

        _updateImageSizes : function( attach_id ){
            if( !_.isUndefined( this.relSizeControlId ) ){
                api.Events.trigger( 'imageUpdateUsingSizes' , {
                    controlId  :  this.relSizeControlId,
                    attachId   :  attach_id ,
                    customSize :  ( !_.isUndefined( api.settings.controls[this.relSizeControlId] ) && !_.isUndefined( api.settings.controls[this.relSizeControlId].has_custom_size ) ) ? api.settings.controls[this.relSizeControlId].has_custom_size : false
                });
            }
        }

    });


    api.Events.bind( 'imageUpdateUsingSizes' , function( obj ) {
        if( !_.isUndefined( obj ) && !_.isUndefined( obj.controlId ) &&  !_.isUndefined( obj.attachId ) ){
            if( obj.attachId != 0 && !_.isUndefined( api.attachmentSizes[obj.attachId] ) ){

                var optionsStr = "" ,
                    id = obj.attachId ,
                    currSize = $("#sed_pb_" + obj.controlId ).val();

                if( !_.isUndefined( obj.customSize ) && obj.customSize ){
                    var selected = ( $.trim( currSize ) == "") ? 'selected="selected"': '';
                    optionsStr += '<option value="" ' + selected + '> ' + api.I18n.custom_size + ' </option>';
                }else if( $.inArray( currSize , _.keys( api.attachmentSizes[id] ) ) == -1 ){
                    currSize = "full";
                }

                _.each( api.attachmentSizes[id] , function( size , key ){
                    var selected = (currSize == key) ? 'selected="selected"': '';
                    optionsStr += '<option value="' + key + '" ' + selected + '> ' + api.addOnSettings.imageModule.sizes[key].label + ' - ' + size.width + " x " + size.height + ' </option>';
                });

                $("#sed_pb_" + obj.controlId ).html( optionsStr );
            }
        }
    });


    api.MultiImagesControl = api.SiteEditorControls.extend({

		_ready: function() {

            var control = this ,
                removeActions = control.container.find('.remove-img-action'),
                selectButton  = control.container.find('.select-img-btn'),
                options =  {
                    "supportTypes"       : ["image"],
                    "selctedType"        : "multiple",   // single or multiple
                    "dialog"     : {
                        "title"     :    !_.isUndefined( control.params.lib_title ) ? control.params.lib_title : api.I18n.change_img_library,
                        "buttons"   :    [
                            {
                                "title"    :   !_.isUndefined( control.params.btn_title ) ? control.params.btn_title : api.I18n.images_gallery_update ,
                                "type"     :   "change_media" ,
                                "select_validation" :   true
                            }
                        ]
                    },
                };

            this.options = options;
            if( !_.isUndefined( control.params.subtypes ) ){
                this.options.subtypes = _.clone( control.params.subtypes );
            }

            this.options.eventKey = "multi_images_" + control.id;

            selectButton.livequery(function(){
                $(this).click(function(){
                    var currentImages = [];
                    if( !_.isUndefined( control.currentValue ) && !_.isEmpty( control.currentValue ) ){
                        if( $.isArray( control.currentValue ) )
                            currentImages = control.currentValue;
                        else if( _.isString( control.currentValue ) )
                            currentImages = control.currentValue.split(",");
                    }
                    control.options.selectionSended = currentImages;
                    api.previewer.trigger( 'openMediaLibrary' , {
                        options :  {
                            'media' : control.options
                        }
                    });
                });
            });

            control.organizeBox = control.container.find(".images-organize-box");
            control.sortableImages = control.organizeBox.find(".images-sortable");

            control.sortableImages.sortable({
                update : function( e , ui){
                    var currValue = [];
                    control.sortableImages.find(">li.item-image").each(function(){
                        var val = $(this).attr("sed-attachment-id");
                        currValue.push( val );
                    });

                    control.refresh( currValue );
                }
            }).disableSelection();

            removeActions.livequery(function(){
                $(this).click(function(){

                    var imgItem = $(this).parents(".item-image:first"),
                        attachId = imgItem.attr("sed-attachment-id") ,
                        currValue;

                    imgItem.remove();

                    if( _.isEmpty( control.currentValue ) )
                        return ;

                    currValue = control.currentValue;

                    if( _.isString( currValue ) )
                        currValue = currValue.split(",");

                    if( !$.isArray( currValue ) )
                        return ;

                    currValue = _.map( currValue , function( id ){
                        return parseInt( id );
                    });

                    var index = $.inArray( parseInt( attachId ) , currValue );

                    if( index > -1){
                        currValue.splice( index , 1);
                        control.refresh( currValue );
                    }

                });
            });

            api.previewer.bind( "sedChangeMedia" +  this.options.eventKey ,  function( attachments ) {

                var ids = _.pluck( attachments , 'id' );

                control._updateOrganizeBox( ids );

                control.refresh( ids );

                _.each( attachments , function( attachment ){
                    //start update image sizes in settings dialog (update options in select field)
                    api.previewer.trigger( 'addAttachmentSizes' , {
                        id    : attachment.id  ,
                        sizes : attachment.sizes
                    });
                })

            });
        },

        _update  : function( ids ) {
            this._updateOrganizeBox( ids );
        },

        _updateOrganizeBox : function( ids ){
            var control = this , currValue;
            control.sortableImages.html("");

            if( !_.isEmpty( ids ) && ids ){

                if( _.isString( ids ) )
                    currValue = ids.split(",");
                else if( $.isArray( ids ) )
                    currValue = ids;

                var images_html = "";
                _.each( currValue , function( attach_id ){

                    var attachment = _.findWhere( api.attachmentsSettings , { id : attach_id}  );

                    if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) ){
                        var imgUrl = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url  : attachment.url;
                    }else{
                        var imgUrl = SED_BASE_URL + 'images/no-image.jpg';
                        attach_id = 0;
                    }

                    images_html += '<li sed-attachment-id="' + attach_id + '" class="item-image"><img class="gallery-img" src="' + imgUrl + '" width="100" height="100"><span class="remove-img-action icon-delete"></span></li>';
                });

                control.sortableImages.html( images_html );
            }
        },

    });

    api.ChangeIconControl = api.SiteEditorControls.extend({

		_ready: function() {

            var control = this ,
                removeButton  = control.container.find('.remove-icon-btn'),
                selectButton  = control.container.find('.select-icon-btn'),
                sedDialog = {
                    tmpl      :  "tmpl-dialog-icon-library",
                    selector  :  "#sed-dialog-icon-library" ,
                    data      :  {
                        "selctedType"    : "single" ,
                        "shortcodeName"  : "" ,
                        "eventKey"       : "change_icon_" + control.id ,
                        "currentIcons"   :  ""
                    },
                    id        :  "sedDialogIconLibrary",
                    options   :  {
                        "dialog_options" : {
                            "autoOpen"      : false,
                            "dialogClass"   : "icon-library-dialog",
                            "modal"         : true,
                            "width"         : 880,
                            "height"        : 550
                        }
                    },
                    extra     :  {} ,
                };

            this.sedDialog = sedDialog;

            selectButton.livequery(function(){
                $(this).click(function(){
                    api.Events.trigger( 'element_open_dialog' , control.sedDialog );
                });
            });

            control.icoDemo = control.container.find(".icon-demo");

            removeButton.livequery(function(){
                $(this).click(function(){

                    control._updateIconDemo( "" );

                    control.refresh( "" );

                });
            });

            api.Events.bind( sedDialog.data.eventKey ,  function( icon ) {

                control._updateIconDemo( icon );

                control.refresh( icon );

            });
        },

        _update  : function( icon ) {
            this._updateIconDemo( icon );
        },

        _updateIconDemo : function( icon ){
            this.icoDemo.removeClass( this.icoDemo.attr("sed-icon") ).addClass( icon );
            this.icoDemo.attr("sed-icon" , icon);
        },

    });

    api.OrganizeIconsControl = api.SiteEditorControls.extend({

		_ready: function() {

            var control = this ,
                removeActions = control.container.find('.remove-icon-action'),
                selectButton  = control.container.find('.select-icon-btn'),
                sedDialog = {
                    tmpl      :  "tmpl-dialog-icon-library",
                    selector  :  "#sed-dialog-icon-library" ,
                    data      :  {
                        "selctedType"    : "multi" ,
                        "shortcodeName"  : "" ,
                        "eventKey"       : "organize_icons_" + control.id ,
                        "currentIcons"   :  ""
                    },
                    id        :  "sedDialogIconLibrary",
                    options   :  {
                        "dialog_options" : {
                            "autoOpen"      : false,
                            "dialogClass"   : "icon-library-dialog",
                            "modal"         : true,
                            "width"         : 880,
                            "height"        : 550
                        }
                    },
                    extra     :  {} ,
                };

            this.sedDialog = sedDialog;

            selectButton.livequery(function(){
                $(this).click(function(){
                    var currentIcons = "";
                    if( !_.isUndefined( control.currentValue ) && !_.isEmpty( control.currentValue ) ){
                        if( $.isArray( control.currentValue ) )
                            currentIcons = control.currentValue.join(",");
                        else if( _.isString( control.currentValue ) )
                            currentIcons = control.currentValue;
                    }
                    control.sedDialog.data.currentIcons = currentIcons;
                    api.Events.trigger( 'element_open_dialog' , control.sedDialog );
                });
            });

            control.organizeBox = control.container.find(".icons-organize-box");
            control.sortableIcons = control.organizeBox.find(".icons-sortable");

            control.sortableIcons.sortable({
                update : function( e , ui){
                    var currValue = [];
                    control.sortableIcons.find(">li.item-icon").each(function(){
                        var val = $(this).attr("sed-icon");
                        currValue.push( val );
                    });

                    control.refresh( currValue );
                }
            }).disableSelection();

            removeActions.livequery(function(){
                $(this).click(function(){

                    var iconItem = $(this).parents(".item-icon:first"),
                        icon = iconItem.attr("sed-icon") ,
                        currValue;

                    iconItem.remove();

                    if( _.isEmpty( control.currentValue ) )
                        return ;

                    currValue = control.currentValue;

                    if( _.isString( currValue ) )
                        currValue = currValue.split(",");

                    if( !$.isArray( currValue ) )
                        return ;

                    var index = $.inArray( icon , currValue );

                    if( index > -1){
                        currValue.splice( index , 1);
                        control.refresh( currValue );
                    }

                });
            });

            api.Events.bind( sedDialog.data.eventKey ,  function( icons ) {

                control._updateOrganizeBox( icons );

                control.refresh( icons );

            });
        },

        _update  : function( icons ) {
            this._updateOrganizeBox( icons );
        },

        _updateOrganizeBox : function( icons ){
            var control = this , currValue;
            control.sortableIcons.html("");

            if( !_.isEmpty( icons ) && icons ){

                if( _.isString( icons ) )
                    currValue = icons.split(",");
                else if( $.isArray( icons ) )
                    currValue = icons;

                var icons_html = "";
                _.each( currValue , function( icon ){
                    icons_html += '<li sed-icon="' + icon + '" class="item-icon"><span class="' + icon + '"></span><span class="remove-icon-action icon-delete"></span></li>';
                });
                control.sortableIcons.html( icons_html );

            }
        },

    });

    api.WidgetControl = api.SiteEditorControls.extend({

        _ready   : function( ) {
            var control = this ,
                form = control.container.find('.widget-form');

            this.form = form;
            this.formModels = [];

            this.form.find( ':input[name]' ).each( function(){
                var fElm = $(this);
                control.formModels.push( new api.FormModel( fElm , {
                    refresh : function( val ){
                        control.previewer.send( 'current_widget', {
                            fieldName    : fElm.attr("name") ,
                            phpClass     :  control.container.find('[name="php_class"]').val(),
                            idBase       :  control.container.find('[name="id_base"]').val() ,
							className	:  control.container.find('[name="widget-classname"]').val()
                        });
                        control.refresh( control._serialize() );
                    }
                }));
            });  ////api.log( this.formModels );

            control.defaultValue = control._serialize();

        },

		/**
		 * Find all inputs in a widget container that should be considered when
		 * comparing the loaded form with the sanitized form, whose fields will
		 * be aligned to copy the sanitized over. The elements returned by this
		 * are passed into this._getInputsSignature(), and they are iterated
		 * over when copying sanitized values over to the the form loaded.
		 *
		 * @param {jQuery} container element in which to look for inputs
		 * @returns {jQuery} inputs
		 * @private
		 */
		_getInputs: function( container ) {
			return $( container ).find( ':input[name]' );
		},

        _serialize : function( ){

            var $widgetContent = this.form.find(".widget-content") ,
                $inputs = this._getInputs( $widgetContent );
			// Store the value we're submitting in data so that when the response comes back,
			// we know if it got sanitized; if there is no difference in the sanitized value,
			// then we do not need to touch the UI and mess up the user's ongoing editing.
			/*$inputs.each( function() {
				var input = $( this ),
					property = self._getInputStatePropertyName( this );
				input.data( 'state' + updateNumber, input.prop( property ) );
			} ); */

			/*if ( instanceOverride ) {
				data += '&' + $.param( { 'sanitized_widget_setting': JSON.stringify( instanceOverride ) } );
			} else { */
			var data = $inputs.serialize();
			//}
			//data += '&' + $widgetContent.find( '~ :input' ).serialize();
            return data ;
        },

        _update  : function( val ) {
             val = decodeURIComponent(val.replace(/\+/g," "));
             var fvals = val.split("&") , control = this ,
                 formfieldNames = [];

             //api.log( "fvals ------ : " , fvals );

             //api.log( "control.formModels ------ : " , control.formModels );

             $.each(fvals , function(index , value){
                if(value){
                    var param = value.split("=");
                    formfieldNames.push( param[0] );
                    var property = control._getInputStatePropertyName( '[name="' + param[0] + '"]' );
                    if(param.length == 2){
                        var value = (property == "value") ? param[1] : true;
                        control.formModels[index].updateField( value );
                    }
                }
                //control.form.find('[name="' + param[0] + '"]').val( param[1] );
             });

             formfieldNames = _.filter( formfieldNames , function( name ){
                return $.trim(name) != "";
             });

             //if checkboxes And radio buttons have not checked ,  serialize not include them   , [type='radio']
            control.form.find("[type='checkbox']").each(function(){
                if( $(this).attr("name") && $.inArray( $(this).attr("name") , formfieldNames) == -1 )
                    $(this).prop("checked" , false);
             });

        } ,
		/**
		 * Get the property that represents the state of an input.
		 *
		 * @param {jQuery|DOMElement} input
		 * @returns {string}
		 * @private
		 */
		_getInputStatePropertyName: function( input ) {
			var $input = $( input );

			if ( $input.is( ':radio, :checkbox' ) ) {
				return 'checked';
			} else {
				return 'value';
			}
		},


    });

	api.CheckBoxesControl = api.SiteEditorControls.extend({
		_ready: function() {
			var control = this,
				checkboxes = this.container.find('.sed-checkboxes'), oldValue;

            if( !_.isUndefined( this.params.default_value ) ){

                if( _.isString( this.params.default_value ) )
                    this.defaultValue = this.params.default_value.split(",");
                else if( $.isArray( this.params.default_value ) )
                    this.defaultValue = this.params.default_value;
                else
                    this.defaultValue = [];

            }else
                this.defaultValue = [];


            if ( !_.isUndefined( control.params.options_selector ) ) {
               this.statuses = $( control.params.options_selector, checkboxes );
            }else{
                return ;
            }

            this.statuses.on("change", function(){

                var $thisValue = control.setting() ,
                    attrStatus,
                    currVal = [];

                control.statuses.each(function(index , el){
                    var isChecked = $(this).prop( 'checked' );

                    if( isChecked )
                        currVal.push( $(this).val() );

                });

                var isChecked = $(this).prop( 'checked' ),
                index = $.inArray( $(this).val() , currVal );

                if(isChecked &&  index == -1){
                    currVal.push( $(this).val() );
                }else if(!isChecked && index > -1){
                    currVal.splice( index , 1);
                }

                control.refresh( currVal );

            });

		},

        _update: function( val ){
            var control = this,
                currValue = [];

            if( _.isString( val ) )
                currValue = val.split(",");
            else if( $.isArray( val ) )
                currValue = val;

            this.statuses.each(function(index , el){

                if( $.inArray( $(this).val() , currValue) > -1)
                    $(this).prop( 'checked', true );
                else
                    $(this).prop( 'checked', false );

            });
        }

    });


    api.Animations = api.SiteEditorControls.extend({

		_ready: function() {
              var control = this ,  dialog = $("#dialog_page_box_" + this.shortcode + "_animation" );

              dialog.find(".animation-dialog-inner").html($("#tmpl-dialog-animations").html());

              var spinner = dialog.find(".sed-bp-spinner");
              this.animateType = dialog.find(".sed_pb_animation_type_class");
              this.duration = dialog.find(".sed_pb_animation_duration");
              this.delay = dialog.find(".sed_pb_animation_delay");
              this.offset = dialog.find(".sed_pb_animation_offset");
              this.iteration = dialog.find(".sed_pb_animation_iteration") ;

              spinner.spinner();
                                   // spinchange spin keyup
              spinner.on("spinstop", function(e,ui){
                  control.refresh( control.getValues() );
              });

              this.animateType.on("change", function(e,ui){
                  control.refresh( control.getValues() );
              });

        },

        _update  : function( val ) {
            var animateSettings = val.split(",");

            this.updateField("animateType" , animateSettings[3]);
            this.updateField("duration" , animateSettings[2]);
            this.updateField("delay" , animateSettings[0]);
            this.updateField("offset" , animateSettings[4]);
            this.updateField("iteration" , animateSettings[1]);
        },

        getValues: function(prop , val){
            var values = [this.delay.val() , this.iteration.val() , this.duration.val() , this.animateType.val() , this.offset.val()];
            return values.join(",");
        },

        updateField: function(prop , val){
            if(val && this[prop] != "undefined")
                this[prop].val( val );
            else if(this[prop] != "undefined")
                this[prop].val( '' );
        }

    });



    api.ModuleSkins = api.SiteEditorControls.extend({

		_ready: function() {      //'_skin_dialog' : skin is control id in pb-shortcode.class.php line 347
            var control = this ,  dialog = $("#dialog_page_box_" + this.shortcode + "_" + this.attr ),
            tmpl;

            this.dialog = dialog;

            this.container.find(".sed-select-module-skins-btn").on("click" , function(){
                control._loadRender( $(this).data("moduleName") );
            });

            api.Events.bind( "loadSkinsDirectly" , function( moduleName ){
                control._loadRender( moduleName );
            });

            dialog.find('li > a.pb-skin-item').livequery(function(){
                $(this).click(function(){
                    control.skinItem = $(this);
                    control.refresh( $(this).data("skinName") );
                });
            });

        },

        _skinSupport : function( ){
            if( _.isUndefined( this.params.support ) )
                return ;
                         //console.log( "this.params.support ---------- : " , this.params.support   );
            var self = this,
                skins = this.params.support ,
                type = ( !_.isUndefined( skins.type ) ) ? skins.type.toLowerCase() : "include";

            if( !$.isArray(skins.fields) || skins.fields.length == 0 )
                return ;

            if( type == "include" )
                this.dialog.find("li:first").hide();

            _.each( skins.fields , function( field ){

                var skinEl = self.dialog.find("[data-skin-name='" + field + "']").parents("li:first");

                if( skinEl.length > 0 && type == "include"  )
                    skinEl.show();
                else if( skinEl.length > 0 && type == "exclude"  )
                    skinEl.hide();
            });
        },

        _loadRender : function( moduleName ){

            var dialog = this.dialog
                control = this;

            $(".error-load-skins" , dialog).hide();
            //dialog.dialog( "open" );

            control.moduleName = moduleName;

            //load skins if already not loaded
            if($("#sed-tmpl-modules-skins-" + control.attr + "-" +  control.shortcode).length > 0 ){
                tmpl = $("#sed-tmpl-modules-skins-" + control.attr + "-" +  control.shortcode);
                dialog.find(".skins-dialog-inner").html( tmpl.html() );

                this._skinSupport();
                api.Events.trigger( "skins_loaded_" + control.shortcode + "_skin" );
            }else{
                control.loadmoduleSkins();
            }

        },

        _refresh : function( val ) {
            this.dialog.find('li > .pb-skin-item').removeClass("pb-skin-item-selected");
            this.skinItem.addClass("pb-skin-item-selected");
        },

        _update  : function( val ) {
            var skin;

            this.dialog.find('li > .pb-skin-item').removeClass("pb-skin-item-selected");
            this.dialog.find('li > .pb-skin-item').each(function(){
                skin = $(this).data("skinName");
                if(skin == val){
                    $(this).addClass("pb-skin-item-selected");
                }
            });
        },

        loadmoduleSkins:function(){
            var control = this , dialog = this.dialog;

            $.ajax({
                type: "POST",
                url: SEDAJAX.url,
                data:
                {
                    action                          :  'load_skins',
                    module                          :  control.moduleName,
                    shortcode                       :  control.shortcode ,
                    default_helper_shortcodes       :  api.defaultHelperShortcodes ,
                    nonce                           :  api.addOnSettings.moduleSkins.nonce ,
                    sed_page_ajax                   :  'module_load_skins'
                },
                beforeSend: function()
                {
                    $('.loading' , dialog).css({opacity:0, display:"block", visibility:'visible',position:'absolute', top:'21px', left:'345px'}).animate({opacity:1});
                },
                /*error: function()
                {
                alert('Couldn\'t add the font because the server didn’t respond.<br/>Please reload the page, then try again');
                },*/
                success: function(response)
                {
                    $('.loading' , dialog).hide();
                    // Check if the user is logged out.
                    if ( '0' == response ) {
                        api.previewer.preview.iframe.hide();
                        api.previewer.login().done( function() {
                            api.previewer.save();
                            api.previewer.preview.iframe.show();
                        } );
                        return;
                    }

                    // Check for cheaters.
                    if ( '-1' == response ) {
                        api.previewer.cheatin();
                        return;
                    }

                    if ( '-2' == response ) {
                        $(".error-load-skins" , dialog).show();
                        $(".error-load-skins span" , dialog).html( api.I18n.invalid_data);
                        return;
                    }

                    $(".error-load-skins" , dialog).hide();

                    response = $.parseJSON( response );
                    if( response.success === true){

                        var skinTmpl = response.data.output;
                        dialog.find(".skins-dialog-inner").html( skinTmpl );
                        $('<script type="text/html" id="sed-tmpl-modules-skins-' + control.attr + "-" + control.shortcode + '">' + skinTmpl + '</script>').appendTo( $("body") );

                        control._skinSupport();
                        api.Events.trigger( "skins_loaded_" + control.shortcode + "_skin" );
                        //dialog.dialog("option" , "position" , { my: "right-20", at: "right" , of: "#sed-site-preview" });

                        if(response.data.js_tpl)
                            control.previewer.send( 'moduleSkinsTpl', response.data.js_tpl );

                        if(response.data.data_module_skins)
                            control.previewer.send( 'dataModuleSkins', response.data.data_module_skins );

                    }else{
                        $(".error-load-skins span").html( response.data.output);

                    }

                }
            });

        },

    });

    /*api.createRelations = {

        module_element : function( value , type , id ,  shortcode , attr , selectedControl ){
            type = (!type) ? "hide" : type;
            var element = $( "#sed_pb_" + id );

            if( element.is('select') ){  ////api.log( element.find("option[value='" + value + "']") );
                                    ////api.log( type );
                if(type == "hide")
                    element.find("option[value='" + value + "']").hide();
                else
                    element.find("option[value='" + value + "']").show();

            }else if( element.is('input:[type="radio"]') ){

                if(type == "hide")
                    element.find("[data-skin-name='" + value + "']").parent().hide();
                else
                    element.find("[data-skin-name='" + value + "']").parent().show();

            }else if( element.is('input:[type="checkbox"]') ){

                if(type == "hide")
                    element.find("[type='checkbox']").parent().hide();
                else
                    element.find("[data-skin-name='" + value + "']").parent().show();

            }

            if( !_.isUndefined( selectedControl ) && type == "hide" ){

                var newVal;

                if( !_.isUndefined( selectedControl.defaultValue )  ){
                    var defaultOption = element.find("option[value='" + selectedControl.defaultValue + ":visible']");
                    if(defaultOption.length > 0 ){
                        newVal = defaultOption.val();
                    }else
                        newVal = element.find("option:visible:first").val();
                }else
                    newVal = element.find("option:visible:first").val();

                selectedControl.refresh( newVal );
                selectedControl.update( );
            }

        },

        skins : function( value , type , id , shortcode , attr , selectedControl ){
            type = (!type) ? "hide" : type;
            var element = $( "#sed_pb_" + id + "_dialog");
            if(type == "hide")
                element.find("[data-skin-name='" + value + "']").parent().hide();
            else
                element.find("[data-skin-name='" + value + "']").parent().show();
        },

        style_editor_element : function( value , type , id , shortcode , attr , selectedControl){
            api.createRelations.module_element( value , type , id );
        }
    }; */

    /*
        //only one control
        "dependency"    => array(
            'controls'  =>  array(
                "control"  => "length" ,
                "value"    => "boxed" ,     @string || @array
                "type"     => "include" ,
                "is_panel" => true          if current field is panel
            ),
        );

        //only multi controls
        "dependency"    => array(
            'controls'  =>  array(
                "relation"     =>  "AND"   // AND || OR      not case sensitive
                array(
                    "control"  => "length" ,
                    "value"    => "boxed" ,
                    "type"     => "include"    //type is include || exclude
                ),
                array(
                    "control"  => "length" ,
                    "value"    => "boxed" ,
                    "type"     => "include"
                ),
            ),
        );

    */
    api.ModulesSettingsRelations = api.Class.extend({

        initialize: function( element , params ) {
            //this.control = control;
            this.relations = {};
            this.mode = "update";
            this.ready();
        },

        ready : function(){
            this.refresh();
            this.update();
        },

        refresh : function( ){
            var self = this;
            api.Events.bind("moduleControlRefresh" , function( group , control , value ){
                self.mode = "refresh";
                self.set( group , control.id , value );
                api.Events.trigger( "after_apply_settings_relations_refresh" , group , control , value  );
            });


        },

        update : function( value ){
            var self = this;

            /*
            * @param : group === sub_category in controls data (api.settings.controls)
            */
            api.Events.bind( "after_group_settings_update" , function( group ){
                self.mode = "update";

                _.each( api.sedGroupControls[group] , function( data , key ){

                    var control = api.control.instance( data.control_id );

                   if( !_.isUndefined( control ) && !_.isUndefined( control.currentValue ) )
                      self.set( group , control.id , control.currentValue );

                });

                api.Events.trigger( "after_apply_settings_relations_update" , group );
            });



        },

        /*
        * @params ::
        * @id :: current control id only
        * @
        */
        set : function(group , id , value ){
            this.group          = group;
            this.currentId      = id;
            this.value          = value;

            this.renderRelations();
        },

        renderRelations : function(  ){
            var relations = this.getRelations() , self = this;
            //console.log( "------currentId------" , this.currentId );
            //console.log( "------relations------" , relations );
            this.itemApplyRelations( relations.controls );

            //this.itemApplyRelations( relations.values , "values" );

        },

        itemApplyRelations : function( controls , type ){
            var self = this;

            if( !$.isArray( controls ) || controls.length == 0  )
                return ;

            type = (!type) ?  "controls": type;

            this._showItem = function( id , isPanel ){  //value , relValues
                var selector = ( isPanel ) ? '#sed_pb_' + this.group + "_" + id : '#sed-app-control-' + id;
                $( selector ).parents(".row_settings:first").show();

                /*switch (type) {
                  case "controls":
                      var selector = '#sed-app-control-' + id;
                      $( selector ).parents(".row_settings:first").show();
                  break;
                  case "values":
                      var typeC = api.settings.controls[id].type;

                      if(_.isFunction( api.createRelations[typeC] ))
                          api.createRelations[typeC]( value , "show" , id , shortcode , attr  );
                  break;
                }*/
            };

            this._hideItem = function( id , isPanel ){    //value , relValues
                var selector = ( isPanel ) ? '#sed_pb_' + this.group + "_" + id : '#sed-app-control-' + id;

                $( selector ).parents(".row_settings:first").hide();
                /*var selectedControl;
                switch (type) {
                  case "controls":
                      var selector = '#sed-app-control-' + id;
                      $( selector ).parents(".row_settings:first").hide();
                  break;
                  case "values":
                      var typeC = api.settings.controls[id].type;

                      if( self.mode == "refresh" ){
                          var control = api.control.instance( id ) ,
                              controlVal = control.currentValue;

                          if( controlVal == value ){
                              selectedControl = control;
                          }

                      }

                      if(_.isFunction( api.createRelations[typeC] ))
                          api.createRelations[typeC]( value , "hide" , id , shortcode , attr , selectedControl );
                  break;
                }*/
            };

            _.each( controls , function( control ){
                var showField = true,
                    isPanel = ( !_.isUndefined(control.is_panel) && control.is_panel ) ? true: false;

                if( ($.inArray( self.value , control.relValues) != -1 && control.type == "include") ||
                      ($.inArray( self.value , control.relValues) == -1 && control.type == "exclude")   ){

                    var value ;
                    /*if( !_.isUndefined(control.value) )
                        value = control.value;*/

                    if( control.relation.toLowerCase()  == "and" )
                        showField = self.multiRelations( control.control , type );

                    if( showField )
                        self._showItem( control.control , isPanel );  //value , control.relValues
                    else
                        self._hideItem( control.control , isPanel );  //value , control.relValues

                }else
                    self._hideItem( control.control , isPanel );  //control.value , control.relValues
            });
        },

        /*
        * @params :
        * @id :: control_id === control.id
        * @cType :: control type include : controls || values || panels
        * @return :: boolean
        */
        multiRelations : function( id , cType ){
            var self = this , showField = true ,
                controls;

            controls = api.settingsRelations[this.group][id].controls;
            /*switch (cType) {
              case "controls":
                  controls = api.settingsRelations[this.group][id].controls;
              break;
              case "values":
                  controls = api.settingsRelations[this.group][id].values[value];
              break;
            }*/

            _.each( controls  , function( depC ){

                if(depC.control != self.currentId ){

                    if( _.isObject( depC ) && !_.isUndefined(depC.control) &&
                        ( !_.isUndefined(depC.value) || !_.isUndefined(depC.values) ) ) {

                        /*if(_.isUndefined(api.modulesShortcodesAttrs[self.group][depC.control] ) ){
                            api.log("in multi relations exist one error in line 708 in siteeditor/core/pbModulesControls.js");
                            return ;
                        } */

                        var id          = depC.control,
                            setting     = api.control.instance( id ),
                            currValue   = setting.currentValue ,
                            relValues   = ( ( !_.isUndefined(depC.values) ) ? depC.values : [depC.value] ) ,
                            type        = ( !_.isUndefined(depC.type) ) ? depC.type.toLowerCase() : "include";
                                  ////api.log( currValue , " : " , relValues );

                        if( ($.inArray( currValue , relValues) != -1 && type == "exclude") ||
                              ($.inArray( currValue , relValues) == -1 && type == "include") ){
                              //alert(showField);

                              showField = false;
                        }
                    }//else
                        //showField = false;
                }

            });

            return showField;
        },

        getRelations : function( ){
            if( !_.isUndefined(this.relations[this.group]) && !_.isUndefined(this.relations[this.group][this.currentId]) )
                return this.relations[this.group][this.currentId];
            else{
                if( _.isUndefined(this.relations[this.group]) )
                    this.relations[this.group] = {};

                this.relations[this.group][this.currentId] = {
                    controls : this.searchFromControls() ,
                    //values   : this.searchFromValues()
                };

                return this.relations[this.group][this.currentId];
            }

        },

        searchFromControls : function( ){
            var self = this , relations = [];

            _.each( api.settingsRelations[this.group] , function( setting , key ){
                var attrRelatedObj = self.findControl(key , setting.controls );

                if( attrRelatedObj ){
                    relations.push( attrRelatedObj );
                }

            });

            return relations;
        },

        /*searchFromValues : function( ){
            var self = this , relations = [];
            _.each( api.settingsRelations[this.group] , function( setting , key ){
                _.each( setting.values , function( controls , value ){
                    var attrRelatedObj = self.findControl(key , controls , "value" , value );
                    if( attrRelatedObj ){
                        relations.push( attrRelatedObj );
                    }
                });

            });

            return relations;
        },*/

        findControl : function (key , controls ){ //, type , value
            var self = this , attrRelated;
            if(!controls || _.isEmpty( controls ) )
                return ;

            if( $.isArray(controls) || (_.isObject( controls ) && _.isUndefined(controls.control) ) ){
                attrRelated = _.find(controls , function( control ){
                    if( _.isObject( control ) && !_.isUndefined(control.control) && ( !_.isUndefined(control.value) || !_.isUndefined(control.values) ) )
                        return control.control == self.currentId ;
                    else                                   //&& ( ( !_.isUndefined(control.value) && control.value == self.value ) || ( !_.isUndefined(control.values) && $.inArray( self.value , control.values ) != -1 )  )
                        return false;
                });
            }else{
                if(_.isObject( controls ) && !_.isUndefined(controls.control) &&
                    ( !_.isUndefined(controls.value) || !_.isUndefined(controls.values) ) && controls.control == self.currentId  )
                    attrRelated = controls;
            }

            if( attrRelated ){
                var attrRelatedObj = {
                    control     : key ,
                    type        : ( ( !_.isUndefined(attrRelated.type) ) ? attrRelated.type.toLowerCase() : "include" ) ,
                    relValues   : ( ( !_.isUndefined(attrRelated.values) ) ? attrRelated.values : [attrRelated.value] ) ,
                    relation    : ( ( !_.isUndefined(controls.relation) && _.isObject(controls) ) ? controls.relation.toLowerCase() : "" ) ,
                    is_panel    : ( ( !_.isUndefined(controls.is_panel) && _.isObject(controls) ) ? true : false )
                };

                /*if(type && type == "value")
                    attrRelatedObj["value"] = value;*/

                return attrRelatedObj;
            }else
                return false;
        }

    });

    api.controlConstructor = $.extend( api.controlConstructor, {
        sed_element             : api.SiteEditorElements ,
        image                   : api.ChangeImageControl ,
        multi_images            : api.MultiImagesControl ,
        video                   : api.ChangeVideoControl ,
        audio                   : api.ChangeAudioControl ,
        file                    : api.ChangeFilesControl ,
        dropdown                : api.SedDropdownControl ,
        spinner                 : api.SpinnerControl,
        checkboxes              : api.CheckBoxesControl,
        spinner_lock            : api.SpinnerLockControl ,
        color                   : api.ColorControl,
        multi_icons             : api.OrganizeIconsControl,
        icon                    : api.ChangeIconControl,
        animations              : api.Animations ,
        skins                   : api.ModuleSkins ,
        widget                  : api.WidgetControl ,
        gradient                : api.GradientControl,
        //module_element          : api.ModulesElement ,
        //style_editor_element    : api.StyleEditorElements
    });

  $( function() {
      api.settingsRelations = window._sedAppModulesSettingsRelations;
      api.settingsSupports  = window._sedAppModulesSettingsSupports;
	  api.modulesGeneralSettings  = window._sedAppModulesGeneralSettings;
      api.defaultHelperShortcodes  = window._sedAppDefaultHelperShortcodes;

      console.log( " ---------api.settingsRelations ----------------- " , api.settingsRelations );

      var modulesSettingsRelations = new api.ModulesSettingsRelations({});

	  api.previewer.bind( 'previewerActive', function( ) {
		  api.previewer.send( "sed_api_settings_supports" , api.settingsSupports );
	  });

      api.previewer.bind( 'moduleForceRefresh', function(){
          api.previewer.refresh();
      });

      api.previewer.bind( 'addAttachmentSizes' , function( data ) {
          var id = data.id , sizes = data.sizes;
          if( _.isUndefined( api.attachmentSizes ) )
              api.attachmentSizes = {};

          api.attachmentSizes[id] = sizes;
      });

      $(".open-media-library-edit-gallery").livequery(function(){
          $(this).click(function(){    console.log("-------support TYPES----------" , $(this).attr("support_types").split(",") );
                var options =  {
                    "supportTypes"       : !_.isUndefined( $(this).attr("support_types") ) ? $(this).attr("support_types").split(",") : ["image"],
                    "ShowOrganizeTab"    : true,
                    "selctedType"        : "multiple",   // single or multiple
                    "activeTab"          : "organize" ,
                    "media_attrs"        : !_.isUndefined( $(this).attr("media_attrs") ) ? $(this).attr("media_attrs").split(",") : ["attachment_id","image_url" , "image_source"],
                    "organizeTab"        : {
                        "title"    :    !_.isUndefined( $(this).attr("organize_tab_title") ) ? $(this).attr("organize_tab_title") : api.I18n.organize_tab_title ,
                        "buttons"  : [
                            {
                                "title"    :   !_.isUndefined( $(this).attr("update_btn_title") ) ? $(this).attr("update_btn_title") : api.I18n.update_btn_title ,
                                "type"     :   "update_media_collection"
                            },
                            {
                                "title"    :   !_.isUndefined( $(this).attr("cancel_btn_title") ) ? $(this).attr("cancel_btn_title") :  api.I18n.cancel_btn_title ,
                                "type"     :   "cancel" ,
                            }
                        ]
                    },
                    "dialog"     : {
                        "title"     :    !_.isUndefined( $(this).attr("lib_title") ) ? $(this).attr("lib_title") : api.I18n.change_img_library,
                        "buttons"   :    [
                            {
                                "title"    :   !_.isUndefined( $(this).attr("add_btn_title") ) ? $(this).attr("add_btn_title") : api.I18n.add_btn_title ,
                                "type"     :   "add_to_collection" ,
                                "select_validation" :   true
                            }
                        ]
                    },
                };

                api.previewer.send( "openMediaLibraryEditGallery" , options );

          });
      });

  });

});