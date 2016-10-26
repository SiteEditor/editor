/**
 * @plugin.js
 * @App Options Dependency Manager Plugin JS
 *
 * @License: http://www.siteeditor.org/license
 * @Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){
    var api = sedApp.editor;

    api.pageConditions = api.pageConditions || {};

    api.OptionDependency = api.Class.extend({

        initialize: function( id , options ){

            this.queries = {};

            this.operators = [];

            this.fieldType = "control";

            $.extend( this, options || {} );

            this.id = id;

        },

        changeActiveRender : function(){

            if( _.isEmpty( this.id ) || !_.isString( this.id ) || !this.id ){
                return ;
            }

            var isShow = this.isActive();

            if( this.fieldType == "control" ){

                var control = api.control.instance( this.id );

                control.active.set( isShow );

            }else{

                var selector = '#sed-app-panel-' + this.id;

                if( isShow ) {
                    $(selector).parents(".row_settings:first").removeClass("sed-hide-dependency").fadeIn("slow");
                }else{
                    $( selector ).parents(".row_settings:first").addClass("sed-hide-dependency").fadeOut( 200 );
                }

            }

        },

        isActive : function( ){

            return this.checkQueries( this.queries );

        },

        checkQueries : function( queries ){

            var self = this ,
                isShowArr = [] ,
                relation = "AND";

            $.each( queries , function( key , query ){

                if ( key === 'relation' ) {

                    relation = query;

                }else if( $.isArray( query ) || _.isObject( query ) ){

                    var isShow;

                    if( self.isFirstOrderClause( query ) ) {

                        isShow = self.checkConditionLogic( query , key  ) ? 1 : 0;

                        isShowArr.push( isShow );


                    }else{

                        isShow = self.checkQueries( query  ) ? 1 : 0;

                        isShowArr.push( isShow );

                    }
                    
                }

            });

            if( isShowArr.length > 0 ) {

                if (relation == "AND") {

                    return $.inArray(0, isShowArr) == -1;

                } else if (relation == "OR") {

                    return $.inArray(1, isShowArr) > -1;

                }

            }

            return true;

        },

        isFirstOrderClause : function( query ){

            return !_.isUndefined( query.key ) || ( !_.isUndefined( query.key ) && !_.isUndefined( query.value ) );

        },

        checkConditionLogic : function( query , key ){

            if( ! _.isUndefined( query['compare'] ) && _.isString( query['compare'] ) && $.inArray( query['compare'] , this.operators ) > -1 ){

                query['compare'] = query['compare'].toUpperCase();

            }else{

                query['compare'] = ! _.isUndefined( query['value'] ) && $.isArray( query['value'] ) ? 'IN' : '=' ;

            }

            if( ! _.isUndefined( query['type'] ) && _.isString( query['type'] ) ){

                query['type'] = query['type'].toLowerCase();

            }else{

                query['type'] = 'control';

            }

            var compare = query['compare'] ,
                isShow ,
                type = query['type'] ,
                id = query['key'] ,
                currentValue ,
                pattern;

            switch ( type ){

                case "control" :

                    if( $.inArray(id , _.keys( api.settings.controls ) ) == -1 ){
                        return true;
                    }

                    var thisControl = api.control.instance( id );

                    currentValue = thisControl.currentValue ;

                    break;

                case "setting" :

                    if ( ! api.has( id ) ) {
                        return true;
                    }

                    currentValue = api.setting( id ).get();

                    break;

                case "page_condition" :

                    if ( _.isUndefined( api.pageConditions[id] ) ) {
                        return true;
                    }

                    currentValue = api.pageConditions[id];

                    return currentValue;

                    break;

            }

            if( _.isUndefined( query['value'] ) ){
                return true;
            }

            switch ( compare ){

                case "=" :
                case "==" :

                    isShow = ( currentValue == query['value'] );

                    break;

                case "===" :

                    isShow = ( currentValue === query['value'] );

                    break;

                case "!=" :

                    isShow = ( currentValue != query['value'] );

                    break;

                case "!==" :

                    isShow = ( currentValue !== query['value'] );

                    break;

                case ">" :

                    isShow = ( currentValue > query['value'] );

                    break;

                case ">=" :

                    isShow = ( currentValue >= query['value'] );

                    break;

                case "<" :

                    isShow = ( currentValue < query['value'] );

                    break;

                case "<=" :

                    isShow = ( currentValue <= query['value'] );

                    break;

                case "IN" :

                    if( ! $.isArray( query['value'] ) ){
                        return true;
                    }

                    isShow = $.inArray( currentValue , query['value'] ) > -1;

                    break;

                case "NOT IN" :

                    if( ! $.isArray( query['value'] ) ){
                        return true;
                    }

                    isShow = $.inArray( currentValue , query['value'] ) == -1;

                    break;

                case "BETWEEN" :

                    if( ! $.isArray( query['value'] ) || query['value'].length != 2 ){
                        return true;
                    }

                    isShow = currentValue > query['value'][0] && currentValue < query['value'][1];

                    break;

                case "NOT BETWEEN" :

                    if( ! $.isArray( query['value'] ) || query['value'].length != 2 ){
                        return true;
                    }

                    isShow = currentValue < query['value'][0] && currentValue > query['value'][1];

                    break;

                case "DEFINED" :

                    isShow = ! _.isUndefined( currentValue );

                    break;

                case "UNDEFINED" :

                    isShow =  _.isUndefined( currentValue );

                    break;

                case "EMPTY" :

                    isShow =  _.isEmpty( currentValue );

                    break;

                case "NOT EMPTY" :

                    isShow =  !_.isEmpty( currentValue );

                    break;

                case "REGEXP" :

                    pattern = new RegExp( query['value'] );

                    isShow = pattern.test( currentValue );

                    break;

                case "NOT REGEXP" :

                    pattern = new RegExp( query['value'] );

                    isShow = !pattern.test( currentValue );

                    break;

            }

            return isShow;

        }

    });

    api.fn._executeFunctionByName = function(functionName, context , args ) {

        args = $.isArray( args ) ? args : [];

        var namespaces = functionName.split(".");
        var func = namespaces.pop();
        for(var i = 0; i < namespaces.length; i++) {
            context = context[namespaces[i]];
        }

        if (typeof context[func] === "function") {
            return context[func].apply(context, args);
        }else{
            return true;
        }

    };

    api.OptionCallbackDependency = api.OptionDependency.extend({

        isActive : function( ){

            if( ! _.isUndefined( this.callback ) ){

                var args = [];

                if( !_.isUndefined( this.callback_args ) ){
                    args = this.callback_args;
                }

                return api.fn._executeFunctionByName( this.callback , window , args );

            }

            return true;

        }

    });

    api.AppOptionsDependency = api.Class.extend({

        initialize: function (options) {

            this.operators = [];

            $.extend(this, options || {});

            this.dependencies = {};

            this.dialogSelector = "#sed-dialog-settings";

            this.updatedGroups = [];

            //this.updatedResetGroups = [];

            this.ready();

        },

        ready: function () {

            var self = this;

            this.initUpdateOptions();

            api.previewer.bind( 'sedCurrentPageConditions' , function( conditions ){

                api.pageConditions = conditions;

                //self.updatedResetGroups = [];

                api.Events.trigger( "afterResetPageConditions" );

            });

            api.Events.bind( "afterResetPageConditions" , function(){

                var isOpen = $( self.dialogSelector ).dialog( "isOpen" );

                if( isOpen ){

                    var optionsGroup = api.sedDialogSettings.optionsGroup;

                    self.update( optionsGroup );

                    //self.updatedResetGroups.push( optionsGroup );

                }

            });

        },

        initUpdateOptions: function () {

            var self = this;

            /*
             * @param : group === sub_category in controls data (api.settings.controls)
             */
            api.Events.bind( "after_group_settings_update" , function( group ){

                self.update( group );

                if( $.inArray( group , self.updatedGroups ) == -1 ){

                    self.updatedGroups.push( group );

                    //self.updatedResetGroups.push( group );

                }

            });

            api.Events.bind( 'afterOpenInitDialogAppSettings' , function( optionsGroup ){

                if( $.inArray( optionsGroup , self.updatedGroups ) > -1 ) { //&& $.inArray( optionsGroup , self.updatedResetGroups ) == -1

                    self.update( optionsGroup );

                    //self.updatedResetGroups.push( optionsGroup );

                }

            });

        },

        update : function( group ){

            var self = this;

            if( !_.isUndefined( api.settingsPanels[group] ) ) {

                _.each(api.settingsPanels[group], function (data, panelId) {

                    if ( !_.isUndefined( api.settingsRelations[group] ) && !_.isUndefined( api.settingsRelations[group][panelId] ) ) {

                        self.dependencyRender( panelId , "panel" , group );

                        api.Events.trigger("after_apply_single_panel_relations_update", group, data, panelId);

                    }

                });

            }

            if( !_.isUndefined( api.sedGroupControls[group] ) ) {

                _.each(api.sedGroupControls[group], function (data) {

                    var control = api.control.instance(data.control_id);

                    if ( !_.isUndefined(control) ) {

                        if (!_.isUndefined(api.settingsRelations[group]) && !_.isUndefined(api.settingsRelations[group][control.id])) {

                            var id = control.id;

                            self.dependencyRender(id, "control" , group);

                            api.Events.trigger("after_apply_single_control_relations_update", group, control, control.currentValue);

                        }
                    }

                });

            }

            api.Events.trigger( "after_apply_settings_relations_update" , group );

        },

        dependencyRender : function( id , fieldType , group ){

            fieldType = _.isUndefined( fieldType ) || _.isEmpty( fieldType ) ? "control" : fieldType;

            var dependencyArgs = api.settingsRelations[group][id] ,
                dependency ,
                type = !_.isUndefined( dependencyArgs.type ) && !_.isEmpty(  dependencyArgs.type  ) ? dependencyArgs.type : "query";

            if( ! this.has( id ) ) {

                var constructor = api.dependencyConstructor[type] || api.OptionDependency,
                    params = $.extend( {} , {
                        operators   : this.operators ,
                        fieldType   : fieldType
                    } , dependencyArgs );

                dependency = this.add( id , new constructor( id , params) );

                this.initControlRefresh( dependency , dependencyArgs );

                this.initSettingRefresh( dependency , dependencyArgs );

            }else{

                dependency = this.get( id );

            }

            dependency.changeActiveRender();

        },

        initControlRefresh : function( dependency , dependencyArgs ){

            var self = this;

            if( !_.isUndefined( dependencyArgs.controls ) ) {

                var controls = dependencyArgs.controls;

                _.each( controls , function ( controlId ) {

                    self.controlRefresh( controlId , dependency );

                });

            }

        },

        //support nested relations
        controlRefresh : function( controlId , dependency ){

            var self = this;

            api.Events.bind("afterControlValueRefresh", function (group, control, value) {

                if ( controlId == control.id ) {

                    dependency.changeActiveRender();

                    if ( !_.isUndefined( api.settingsRelations[group] ) && !_.isUndefined( api.settingsRelations[group][control.id] ) ) {

                        var dependencyArgs = api.settingsRelations[group][control.id];

                        if( !_.isUndefined( dependencyArgs.controls ) && !_.isEmpty( dependencyArgs.controls ) ){

                            $.each( dependencyArgs.controls , function( cId ){

                                self.controlRefresh( cId , dependency );

                            });

                        }

                    }

                }

                api.Events.trigger("after_apply_settings_relations_refresh", group, control, value);

            });

        },

        initSettingRefresh : function( dependency , dependencyArgs ){

            var self = this;

            if( !_.isUndefined( dependencyArgs.settings ) ) {

                var settings = dependencyArgs.settings;

                _.each( settings , function ( settingId ) {

                    self.settingRefresh( settingId , dependency );

                });

            }

        },

        settingRefresh : function( settingId , dependency ){

            api( settingId , function( setting ) {
                setting.bind(function( value ) {

                    dependency.changeActiveRender();

                });
            });

        },

        add : function( id , dependencyObject ){

            if( ! this.has( id ) && _.isObject( dependencyObject ) ){
                this.dependencies[id] = dependencyObject;
            }

            return dependencyObject;

        },

        has : function( id ){

            return !_.isUndefined( this.dependencies[id] );

        },

        get : function( id ){

            if( this.has( id ) ){
                return this.dependencies[id];
            }else{
                return null;
            }

        }

    });

    api.dependencyConstructor = {
        query                   : api.OptionDependency,
        callback                : api.OptionCallbackDependency
    };


    api.Events.bind( "after_apply_single_control_relations_update" , function(group, control, currentValue){

        if( control.params.active === false ) {
            control.active.set(control.params.active);
        }

    });

   $( function() {

       api.settingsRelations = window._sedAppModulesSettingsRelations;

        api.appOptionsDependency = new api.AppOptionsDependency({
            operators : window._sedAppDependenciesOperators
        });

   });

})( sedApp, jQuery );