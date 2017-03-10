/**
 * siteEditorControls.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */      // , "siteEditor/siteEditorCss"
(function( exports, $ ){
	var api = sedApp.editor;

    api.MSElement = {};
    api.modulesShortcodesAttrs = api.modulesShortcodesAttrs || {} ;
    api.createRelations = api.createRelations || {};
    api.currenStyleEditorContolsValues = api.currenStyleEditorContolsValues || {};
    api.lockControlsCache = api.lockControlsCache || {};

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

            var needToUpdate = false;

            if( !_.isUndefined( control.params.default_value ) && !_.isNull( control.params.default_value ) ) {
                needToUpdate = true;
                control.defaultValue = control.params.default_value;
            }else {
                control.defaultValue = control.setting();
            }

            this._ready();

            if( needToUpdate === true ) {

                if ( !_.isUndefined( this.extraOpt ) && !_.isUndefined( this.extraOpt.attrs ) ) {
                    this.update( this.extraOpt.attrs );
                } else if ( !_.isUndefined( this.extraOpt ) && !_.isEmpty( this.extraOpt ) ) {
                    this.update( this.extraOpt );
                } else {
                    this.update();
                }

            }else{
                //need for dependency or relations
                this.currentValue = control.setting();

            }

        },

        refresh : function( val , lockMode ) {
            var control = this ;

            if( this.isModuleControl ){
                var attrStatus;

                control.previewer.send( 'current_shortcode', control.shortcode );
                control.previewer.send( 'current_attr', control.attr );

                if( !_.isUndefined( control.params.sub_shortcodes_update ) && !_.isUndefined( control.params.sub_shortcodes_update["class"] ) && !_.isUndefined( control.params.sub_shortcodes_update.attr ) ){
                    control.previewer.send( 'subShortcodesAttrUpdate', {
                        "class" : control.params.sub_shortcodes_update["class"] ,
                        "attr"  : control.params.sub_shortcodes_update.attr ,
                        "value" : val
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

                if( this.cssSettingType == "module" ){
                    currentCssSelector = ( this.cssSelector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + this.cssSelector : '[sed_model_id="' + api.currentTargetElementId + '"]';
                }else{
                    currentCssSelector = this.cssSelector;
                }

                this.previewer.send( 'current_css_setting_type', this.cssSettingType );
                this.previewer.send( 'current_css_selector', currentCssSelector );

                var settingId = this.settings["default"].id;

                if( this.cssSettingType == "page" ){ 
                    /**
                     * if have 2 same selector in page & site type , using ##sed_current_page##
                     * for prevent conflict
                     *
                     * @type {string}
                     */
                    currentCssSelector = "##sed_current_page##" + this.cssSelector;
                }

                if( _.isUndefined( api.currenStyleEditorContolsValues[currentCssSelector] ) )
                    api.currenStyleEditorContolsValues[currentCssSelector] = {};

                api.currenStyleEditorContolsValues[currentCssSelector][settingId] = val;
            }

            this.setValue( val );

            this.currentValue = val;

            if( this.isModuleControl && !_.isUndefined( control.params.sub_category ) ) {
                this.setAtts(val);
            }

            if( !_.isUndefined( control.params.sub_category ) && !_.isEmpty( control.params.sub_category ) ){
                api.Events.trigger("afterControlValueRefresh" , control.params.sub_category , control , val );
            }

            this.refreshLockControls( val , lockMode );

        },

        setValue : function( val ){
            var control = this ;

            if( $.isArray( val ) ){
                val = val.join(",");
            }

            val = api.applyFilters( control.setting.id + "_set" , val , control.id  );

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

            }else if( this.isStyleControl ){

                var sValue = api.appStyleEditorSettings.getCurrentValue( control.id , control.params , control.cssSelector , control.cssSettingType  );

                if( !_.isNull( sValue ) ){
                    cValue = _.clone( sValue );
                }else{
                    cValue = control.defaultValue;
                }

            }else if( !_.isUndefined( value ) ){ //&& !_.isObject( value )
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

        refreshLockControls : function( val , lockMode ){

            var control = this;

            if( !_.isUndefined( control.params.lock_id ) && ( _.isUndefined( lockMode ) || lockMode === false ) ){

                var LockerControl = api.control.instance( control.params.lock_id );

                if( LockerControl && !_.isUndefined( LockerControl.currentValue ) && LockerControl.currentValue ){

                    var lockControlsParams = this.findLockControls( LockerControl.id ),
                        lockControls = [];

                    _.each( lockControlsParams , function( data ){

                        var controlId = data.control_id,
                            lockControl = api.control.instance( controlId );

                        lockControls.push( lockControl );//currentValue

                    });

                    _.each( lockControls , function( control ){

                        control.refresh( val , true );

                        control.update( val );

                    });

                }


            }

        },

        findLockControls : function( id ){

            if( !_.isUndefined( api.lockControlsCache[id] ) ){
                return api.lockControlsCache[id];
            }

            var lockControls = _.where( _.values( api.settings.controls ) , { lock_id : id } );

            api.lockControlsCache[id] = lockControls;

            return lockControls;

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

    api.LockControl = api.SiteEditorControls.extend({

        _ready: function() {

            var control = this ,
                lockElement = this.container.find('.sed-element-control');

            this.lockElement = lockElement;

            lockElement.change(function(){

                var isChecked = $(this).prop('checked');

                if(isChecked){

                    var lockControlsParams = control.findLockControls( control.id ),
                        lockControls = [];

                    _.each( lockControlsParams , function( data ){

                        var controlId = data.control_id,
                            lockControl = api.control.instance( controlId );

                        lockControls.push( lockControl );//currentValue

                    });

                    //var minCorner = Math.min.apply(Math, corners);

                    var firstVal = lockControls[0].currentValue;

                    _.each( lockControls , function( control ){

                        if( control.id !== lockControls[0].id ) {

                            control.refresh(firstVal);

                            control.update(firstVal);

                        }

                    });

                }

                control.refresh( isChecked );

            });

        } ,

        _update: function( val ){
            this.lockElement.prop( 'checked', val );
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
            }, function() {
                // unbind the change event
                $(this).unbind('change');
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
            };

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

        }


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

            this.element.trigger('change.select2');

  		},
  		getVal: function() {
  			return this.element.val();
  		}
  	};

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
        }

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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            var attachment = _.findWhere( api.attachmentsSettings , { id : parseInt( attach_id )}  );

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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            var attachment = _.findWhere( api.attachmentsSettings , { id : parseInt( attach_id )}  );

            if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) && !_.isUndefined( attachment.url ) ){
                this.fieldUrl.val( attachment.url );
            }else{
                this.fieldUrl.val( "" );
            }
        }

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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            var attachment = _.findWhere( api.attachmentsSettings , { id : parseInt( attach_id ) }  ); 

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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            var attachment = _.findWhere( api.attachmentsSettings , { id : parseInt( attach_id )}  );

            if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) ){
                if( !_.isUndefined( size ) && !_.isUndefined( attachment.sizes ) && !_.isUndefined( attachment.sizes[size] ) ){
                    var imgUrl = attachment.sizes[size].url;
                }else{
                    var imgUrl = ( !_.isUndefined( attachment.sizes ) && !_.isUndefined( attachment.sizes.thumbnail ) ) ? attachment.sizes.thumbnail.url : attachment.url;
                }
            }else{
                var imgUrl = SEDNOPIC.url;
            }

            return imgUrl;
        },

        _updateImageSizes : function( attach_id ){
            if( !_.isUndefined( this.relSizeControlId ) && attach_id && attach_id > 0 ){
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

    api.SiteIconControl = api.ChangeImageControl.extend({

        _updateImageDemo : function( attach_id ){
            var imgUrl = this._getImageUrl( attach_id );
            this.imgDemo.attr("src" , imgUrl );

            if( attach_id > 0 && imgUrl != SEDNOPIC.url ){
                $( 'link[sizes="32x32"]' ).attr( 'href', imgUrl );
            }else{
                $( 'link[sizes="32x32"]' ).attr( 'href', "" );
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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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

                    var attachment = _.findWhere( api.attachmentsSettings , { id : parseInt( attach_id) }  ); 

                    if( attach_id && attach_id > 0 && !_.isUndefined( attachment ) ){
                        var imgUrl = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url  : attachment.url;
                    }else{
                        var imgUrl = SEDNOPIC.url;
                        attach_id = 0;
                    }

                    images_html += '<li sed-attachment-id="' + attach_id + '" class="item-image"><img class="gallery-img" src="' + imgUrl + '" width="100" height="100"><span class="remove-img-action sedico-delete sedico"></span></li>';
                });

                control.sortableImages.html( images_html );
            }
        }

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

            /*if( _.isEmpty( control.defaultValue ) ) {
                control.defaultValue = 'fa fa-magic';
            }*/

            selectButton.livequery(function(){
                $(this).click(function(){
                    api.Events.trigger( 'element_open_dialog' , control.sedDialog );
                });
            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });

            control.icoDemo = control.container.find(".sed-bp-icon-demo");

            removeButton.livequery(function(){
                $(this).click(function(){

                    control._updateIconDemo( "" );

                    control.refresh( "" );

                });
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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

            if( _.isEmpty( icon ) ) {
                icon = 'sedico sedico-icons sed-bp-icon-empty'; 
            }

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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
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
                    icons_html += '<li sed-icon="' + icon + '" class="item-icon"><span class="' + icon + '"></span><span class="remove-icon-action sedico-delete sedico"></span></li>';
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
			var control = this;

            this.checkboxes = this.container.find('.sed-bp-checkbox-input');

            if( _.isString( control.defaultValue ) ){
                control.defaultValue = control.defaultValue.split(",");
            }

            if( ! $.isArray( control.defaultValue ) ) {
                control.defaultValue = [];
            }

            this.checkboxes.on("change", function(){

                var currVal = [];

                control.checkboxes.filter(":checked").each(function(index , el){

                    currVal.push( $(this).val() );

                });

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

            this.checkboxes.each(function(index , el){

                if( $.inArray( $(this).val() , currValue) > -1)
                    $(this).prop( 'checked', true );
                else
                    $(this).prop( 'checked', false );

            });
        }

    });


    api.Animations = api.SiteEditorControls.extend({

		_ready: function() {
              var control = this ,
                  dialog = $( "#" + this.container.find(".sed-element-control").data("relatedLevelBox") );

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

            this.animateType.trigger('change.select2');

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
            var control = this ,
                dialog = $( "#" + this.container.find(".sed-element-control").data("relatedLevelBox") );

            this.dialog = dialog;

            this.container.find(".sed-element-control").on("click" , function(){
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
            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });

        },

        _loadRender : function( moduleName ){

            var dialog = this.dialog ,
                control = this;

            $(".error-load-skins" , dialog).hide();
            //dialog.dialog( "open" );

            control.moduleName = moduleName;

            //load skins if already not loaded
            if($("#sed-tmpl-modules-skins-" + control.attr + "-" +  control.shortcode).length > 0 ){
                tmpl = $("#sed-tmpl-modules-skins-" + control.attr + "-" +  control.shortcode);
                dialog.find(".skins-dialog-inner").html( tmpl.html() );

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

            var tpl = api.template("sed-ajax-loading"), html;

            html = tpl({type: "medium"});

            var loading = $( html ).appendTo( $('.loading' , dialog) );

            loading.show();

            $.ajax({
                type: "POST",
                url: SEDAJAX.url,
                data:
                {
                    action                          :  'load_skins',
                    module                          :  control.moduleName,
                    shortcode                       :  control.shortcode ,
                    default_helper_shortcodes       :  api.defaultHelperShortcodes ,
                    nonce                           :  api.settings.nonce.pbSkins.load ,
                    sed_page_ajax                   :  'module_load_skins'
                },
                error: function(){

                    loading.hide();

                    alert("Could not add the font because the server did not respond.Please reload the page, then try again");

                },
                success: function(response)
                {
                    loading.hide();

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

        }

    });


    api.controlConstructor = $.extend( api.controlConstructor, {
        sed_element             : api.SiteEditorElements ,
        radio                   : api.SiteEditorElements ,
        "radio-image"           : api.SiteEditorElements ,
        "radio-buttonset"       : api.SiteEditorElements ,
        text                    : api.SiteEditorElements ,
        select                  : api.SiteEditorElements ,
        textarea                : api.SiteEditorElements ,
        checkbox                : api.SiteEditorElements ,
        toggle                  : api.SiteEditorElements ,
        switch                  : api.SiteEditorElements ,
        image                   : api.ChangeImageControl ,
        multi_images            : api.MultiImagesControl ,
        "multi-image"           : api.MultiImagesControl ,
        gallery                 : api.MultiImagesControl ,
        video                   : api.ChangeVideoControl ,
        audio                   : api.ChangeAudioControl ,
        file                    : api.ChangeFilesControl ,
        dropdown                : api.SedDropdownControl ,
        spinner                 : api.SpinnerControl,
        number                  : api.SpinnerControl,
        checkboxes              : api.CheckBoxesControl,
        multicheck              : api.CheckBoxesControl,
        "multi-check"           : api.CheckBoxesControl,
        spinner_lock            : api.SpinnerLockControl ,
        number_lock             : api.SpinnerLockControl ,
        lock                    : api.LockControl ,
        color                   : api.ColorControl,
        multi_icons             : api.OrganizeIconsControl,
        "multi-icon"            : api.OrganizeIconsControl,
        icon                    : api.ChangeIconControl,
        animations              : api.Animations ,
        animation               : api.Animations ,
        skins                   : api.ModuleSkins ,
        skin                    : api.ModuleSkins ,
        widget                  : api.WidgetControl ,
        gradient                : api.GradientControl ,
        'site-icon'             : api.SiteIconControl
        //module_element          : api.ModulesElement ,
        //style_editor_element    : api.StyleEditorElements
        /**
         * ----------------------------
         * @todo : other types for the future
         * ----------------------------
         * @ajax-button
         * @google-map
         * @repeater
         * ...
         * -----------------------------
         * @New *****
         * -----------------------------
         * @code
         * @custom
         * @dropdown-pages
         * @multi-color
         * @radio-image
         * @radio-buttonset
         * @slider
         * @sortable
         * @switch
         * @toggle
         * @date : for date & time
         * @dimension
         * @wp-editor
         * -----------------------------
         * @Primary *****
         * -----------------------------
         * @text
         * @radio
         * @select
         * @textarea
         * @checkbox
         * @multi-check
         * @image
         * @multi-image
         * @video
         * @audio
         * @file
         * @number
         * @color
         * @icon
         * @multi-icon
         * ------------------------------
         * @Module *****
         * ------------------------------
         * @animation
         * @skin
         * ------------------------------
         * @Other *****
         * ------------------------------
         * @widget
         * @gradient
         * @spinner_lock
         */
    });

  $( function() {

      api.defaultHelperShortcodes  = window._sedAppDefaultHelperShortcodes;

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
          $(this).click(function(){   
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
      }, function() {
          // unbind the change event
          $(this).unbind('click');
      });

  });

})( sedApp, jQuery );