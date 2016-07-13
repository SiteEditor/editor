/**
 * siteEditorCss.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
(function( exports, $ ){
	var api = sedApp.editor ,
        Library , Templates , Query , Template;

    api.TemplatesControl = api.Control.extend({
        ready: function() {
			var control = this,
				selectLibrary = this.container.find('.sed-select-template'),
                $thisValue = control.setting() , thisModel;

                this.currentValue = "";

            $("#sed-dialog-confirm-change-template").dialog({
                width : 400 ,
                height : "auto" ,
                modal  : true ,
                autoOpen : false ,
                close :  function(){
                    $("#change-template-settings-form").hide();
                }
            });

            $( Library.dialog.selector ).find(".sed-template").livequery(function(){
                $(this).on("click" , function(e){
                    e.preventDefault();

                    control.currentValue = $(this).attr('data-value');

                    if( _.isEmpty( control.currentValue ) )
                        return alert("template is invalid");

                    thisModel = _.findWhere( Templates.all.models , {id : control.currentValue });
                    //api.log( 'template selected ------- ' , thisModel );

                    $("#sed-dialog-confirm-change-template").dialog('open');
                    $("#change-template-alert-confirm").show();

                });
            });

            $("#cancel-change-template-btn").livequery(function(){
                $(this).click(function(){
                    $("#sed-dialog-confirm-change-template").dialog('close');
                });
            });

            $("#ok-change-template-btn").livequery(function(){
                $(this).click(function(){
                    $("#change-template-alert-confirm").hide();
                    $("#change-template-settings-form").show();
                    if( _.isUndefined( thisModel.options.settings.sed_main_content_shortcode ) || _.isUndefined( api.mainContentShortcode ) || thisModel.options.settings.sed_main_content_shortcode != api.mainContentShortcode  ){
                        $("#remain-content-container-field").find('[name="remain-content-container"]').prop('checked', false);
                        $("#remain-content-container-field").hide();
                    }else{
                        $("#remain-content-container-field").find('[name="remain-content-container"]').prop('checked', true);
                        $("#remain-content-container-field").show();
                    }

                    if( api.settings.page.type == "post" ){
                        $("#apply-main-content-field").show();
                    }else{
                        $("#apply-main-content-field").hide();
                    }

                });
            });



            $("#confirm-change-template-btn").livequery(function(){
                $(this).click(function(){

                    var form = $("#change-template-settings-form") ,
                        formfields = form.serializeArray() ,
                        applyMainContent , remainMainShortcodes;

                    $.each( formfields , function(i, field){
                        switch ( field.name ) {
                          case "change-main-content":
                              applyMainContent = field.value;
                          break;
                          case "remain-content-container":
                              remainMainShortcodes = field.value;
                          break;
                        }
                    });

                    ////api.log( remainMainShortcodes , " -----  " , applyMainContent);
                    control.setting.set( control.currentValue );

                    var changeTemplate = new api.ChangeTemplate();
                    var output = changeTemplate.loadPageTemplate( thisModel , remainMainShortcodes , applyMainContent );

                    //api.log( output );

                    var mainC = {} , settings = output.settings;
                    mainC[api.settings.page.id] = output.main_content || [];
                    settings.theme_content = output.theme_content;

                    //@deprecate
                    api.pageCustomizedSettings = settings;

                    api.postsContent = mainC;

                    api.previewer.refresh();

                    delete api.pageCustomizedSettings;

                    $("#sed-dialog-confirm-change-template").dialog( "close" );

                    $( Library.dialog.selector ).dialog( "close" );

                });
            });

        },
        update: function( ){
            var control = this,$thisValue = control.setting();

        }
    });

    api.controlConstructor = $.extend( api.controlConstructor, {
        templates : api.TemplatesControl
    });


    /*  @template options :: ----
      options : {
          requirement : {
              modules      : modules,
              medias       : medias,
              icons_fonts  : {},   //in fixed icon font bug step
              fonts        : {},   //in color & font step
              widgets      : {},    // in widget step
              posts        : {}     // in posts organize step
          },
          settings    : {
              sed_page_customized  : api.get() , //JSON.stringify( )
              sed_posts_content    : api.postsContent , //JSON.stringify(  )
          }
      }
    */

    api.ChangeTemplate = api.Class.extend({
        initialize: function(  ){
            this.mainShortcodeType;

            //this.template_contain_main_content;

            this.applyMainContent;

            this.remainMainShortcodes;

            this.newThemeShortcodeModels;

            this.CurrThemeShortcodeModels;

            this.newMainContentModels;

            this.CurrMainContentModels;

            this.CurrPageSettings;

            this.newPageSettings;

            this.remainOldIds = [];

            this.template;

            this.output = {};

            this.ids = [];
        },

        loadPageTemplate : function( template , remainMainShortcodes , applyMainContent ){
            this.template = template;
            if( _.isUndefined( template.options.settings.sed_main_content_shortcode ) || _.isUndefined( api.mainContentShortcode ) || template.options.settings.sed_main_content_shortcode != api.mainContentShortcode ){
                this.mainShortcodeType = "unequal"; //equal
            }else{
                this.mainShortcodeType = "equal";
            }

            this.CurrThemeShortcodeModels = api('theme_content')();
            this.newThemeShortcodeModels = template.options.settings.sed_page_customized.theme_content;

            this.CurrMainContentModels = api.postsContent[api.settings.page.id];
            this.newMainContentModels = template.options.settings.sed_posts_content;


            this.CurrPageSettings = api.get();
            this.newPageSettings = template.options.settings.sed_page_customized;

            //this.template_contain_main_content = true;

            this.applyMainContent = applyMainContent; // merge || override || no_action

            this.remainMainShortcodes = remainMainShortcodes; // IF $mainShortcodeType == "equal"  else false

            //@step1 : replace theme content
            this.replaceThemeContent();

            if( api.settings.page.type == "post" ){
                //@step2 : replaceMainContent
                if( this.applyMainContent == "override")
                    this.replaceMainContent();
                else if( this.applyMainContent == "merge")
                    this.appendMainContent();
                else
                    this.output.main_content = this.CurrMainContentModels;
            }

            //@step3 : modify Current Settings
            this.modifyOldSettings();

            //@step4 : modify Current Settings
            this.modifyNewSettings();

            //@step5 : create new Settings (merge settings)
            this.mergeSettings();

            return this.output;
        },

        replaceThemeContent : function(  ){

            if( (this.mainShortcodeType == "equal" && this.remainMainShortcodes === true) || this.mainShortcodeType == "unequal" ){
                this.remainMainOldShortcodes();
            }else{
                this.output.theme_content =  this.newThemeShortcodeModels;//sed_page_customized.
            }
        },

        remainMainOldShortcodes : function(){
            var self = this;
            var index = -1;
            var newMainModel = _.find( this.newThemeShortcodeModels , function( shortcode , idx ){
                index = idx;
                return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_main_content) && !_.isUndefined(shortcode.attrs.sed_main_content) && shortcode.attrs.sed_main_content == "true";
            });
            var parentId = newMainModel.parent_id;
            //remove new main shortcodes
            var tChildren = this.findAllTreeChildrenShortcode(  this.newThemeShortcodeModels , newMainModel.id  )
            this.newThemeShortcodeModels = this.deleteShortcodesTreeChildren( this.newThemeShortcodeModels , tChildren );
            this.newThemeShortcodeModels.splice( index , 1 );

            this.newThemeShortcodeModels = this.modifyAllShortcodesIds( this.newThemeShortcodeModels );

            var newPIdObj = _.find( this.ids , function( ids ){
                return ids.oldId == parentId;
            });

            var newPId = newPIdObj.newId;

            var currMainModel = _.find( this.CurrThemeShortcodeModels , function( shortcode , idx ){
                return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_main_content) && !_.isUndefined(shortcode.attrs.sed_main_content) && shortcode.attrs.sed_main_content == "true";
            });

            currMainModel.parent_id = newPId;

            var shortcodes = this.findAllTreeChildrenShortcode(  this.CurrThemeShortcodeModels , currMainModel.id );
            shortcodes.unshift( currMainModel );

            // remain Old Ids From old theme content
            _.each( shortcodes , function( shortcode ){
                self.remainOldIds.push( shortcode.id );
            });

            this.newThemeShortcodeModels = this.addShortcodesToModels( shortcodes , this.newThemeShortcodeModels , index );
            this.output.theme_content =  this.newThemeShortcodeModels;
        },

        replaceMainContent : function(){
            this.newMainContentModels = this.modifyAllShortcodesIds( this.newMainContentModels );
            this.output.main_content = this.newMainContentModels;
        },

        appendMainContent : function(){
            var self = this;
            // remain Old Ids From old theme content
            _.each( this.CurrMainContentModels , function( shortcode ){
                self.remainOldIds.push( shortcode.id );
            });

            this.newMainContentModels = this.modifyAllShortcodesIds( this.newMainContentModels );
            //this.output.main_content = $.merge( $.merge( [], this.CurrMainContentModels ), this.newMainContentModels );
            ////api.log( this.CurrMainContentModels , ' ------- ' , this.newMainContentModels);
            this.output.main_content = _.uniq(_.union( this.CurrMainContentModels , this.newMainContentModels), function(item, key, id){ return item.id; });

        },

        modifyAllShortcodesIds : function( shortcodesModels ){
            var self = this;
            shortcodesModels = _.map( shortcodesModels , function( shortcode ){

                var id , shortcode_info;
                if( shortcode.tag != "content" ){
                    shortcode_info = api.shortcodes[shortcode.tag];

                    if( typeof( shortcode_info ) == 'undefined' ){
                        //api.log("=================== SED ERROR ==========================");
                        //api.log("shortcode " + shortcode.tag +" is not defined");
                        return ;
                    }

                    if(shortcode_info.asModule){
                        id = self.getNewId( shortcode_info.moduleName );
                    }else{
                        id = self.getNewId( shortcode.tag , "shortcode" );
                    }
                }else{
                    id = self.getNewId( shortcode.tag , "shortcode" );
                }

                self.ids.push({
                    oldId : shortcode.id  ,
                    newId : id
                });


                $.each(shortcodesModels , function(i , shModel){
                    //alert(shortcode.parent_id);
                    if(shModel.parent_id == shortcode.id){
                        shModel.parent_id = id;
                    }
                });

                shortcode.id = id;
                if( _.isUndefined(shortcode.attrs) )
                    shortcode.attrs = {};

                shortcode.attrs.sed_model_id = id;
                return shortcode;
            });

            return shortcodesModels;

        },


        modifyOldSettings : function(){

           var self = this , styleEditorSettings = api.styleEditorSettings ,
               remainIdsSelector = _.map( this.remainOldIds , function(id){
                  return '#' + id ;
               });

           $.each( this.CurrPageSettings , function( id , data ) {
               if($.inArray( id , styleEditorSettings ) != -1){
                  $.each( data , function( selector , value ) {
                     index = $.inArray( selector , remainIdsSelector );
                     if( index == -1 )
                        delete data[selector];
                  });
               }
           });

           $.each( this.CurrPageSettings['sed_pb_modules'] , function(id , attrs ){
               index = $.inArray( id , self.remainOldIds );
               if( index == -1 ){
                  delete self.CurrPageSettings['sed_pb_modules'][id];
               }
           });

           //api.log( this.CurrPageSettings['sed_pb_modules'] );


        },

        modifyNewSettings : function(){
           var self = this , styleEditorSettings = api.styleEditorSettings;

           var oldIds = _.pluck( this.ids , 'oldId' ) ,
               oldIdsSelector = _.map( oldIds ,function(id){
                  return '#' + id ;
               });

           var newIds = _.pluck( this.ids , 'newId' ) ,
               newIdsSelector = _.map( newIds , function(id){
                  return '#' + id ;
               });

           $.each( self.newPageSettings , function( id , data ) {
               if($.inArray( id , styleEditorSettings ) != -1){
                  $.each( data , function( selector , value ) {
                     index = $.inArray( selector , oldIdsSelector );
                     if( index > -1 ){
                        data[ newIdsSelector[index] ] = value;
                        delete data[selector];
                     }
                  });
               }
           });

           $.each( self.newPageSettings['sed_pb_modules'] , function(id , attrs ){
               index = $.inArray( id , oldIds );
               if( index > -1 ){
                  self.newPageSettings['sed_pb_modules'][ newIds[index] ] = attrs;
                  delete self.newPageSettings['sed_pb_modules'][id];
               }
           });

        },

        mergeSettings : function(){
            var settings = $.extend( {} , this.CurrPageSettings  , this.newPageSettings );
            this.output.settings = settings;
        },

        getNewId : function( name , type  ){
            var id , currentPostId = api.settings.page.id;
			type = type || "module";
            if(type == "module"){
                if(typeof api.modules[currentPostId][name] == 'undefined'){
                    api.modules[currentPostId][name] = {
                        length : 1
                    };
                }else{
                    api.modules[currentPostId][name].length += 1;
                }

    

                id = 'sed-bp-module-' + name + "-" + currentPostId + "-" + api.modules[currentPostId][name].length;
            }else{
                if(typeof api.childShortcode[currentPostId][name] == 'undefined'){
                    api.childShortcode[currentPostId][name] = {
                        length : 1
                    };
                }else{
                    api.childShortcode[currentPostId][name].length += 1;
                }

                id = 'sed-bp-shortcode-' + name + "-" + currentPostId + "-" + api.childShortcode[currentPostId][name].length;
            }

            return id;

        },

        deleteShortcodesTreeChildren: function( shortcodesModels , tChildren ){
            var self = this;

            for(var j=0; j < tChildren.length ; j++) {
                for(var i=0; i < shortcodesModels.length ; i++) {
                    var shortcode = shortcodesModels[i];

                    if( shortcode.id == tChildren[j] ){
                        shortcodesModels.splice( i , 1 );
                        break;
                    }
                }
            }

            return shortcodesModels;

        },

        findAllTreeChildrenShortcode: function( shortcodesModels , parent_id ){
            var self = this , allChildren = [];

            $.each( shortcodesModels , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                    allChildren = $.merge( allChildren , self.findAllTreeChildrenShortcode( shortcodesModels , shortcode.id  ) );
                }
            });

            return allChildren;
        },

        addShortcodesToModels : function( shortcodes , shortcodesModels , index ){
            var self = this;

            shortcodes = shortcodes.reverse();

            $.each( shortcodes , function(idx , shortcode){
                shortcode = self.modifyShortcode( shortcode );
                shortcodesModels.splice(index ,0 , shortcode );
            });

            return shortcodesModels;

        },

        modifyShortcode : function( shortcode ){

            //alert( shortcode.id );
            var new_obj = {};
            for(var prop in shortcode["attrs"]){
                new_obj[prop] = shortcode["attrs"][prop];
            }
            new_obj.id = shortcode.id;
            shortcode["attrs"] = new_obj; ////api.log( new_obj );
            return shortcode;
        },

    });




	/**
	 * wp.media.model.Attachment
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	Template = api.Template = api.Class.extend({
        initialize: function( template ){

            $.extend( this, template || {} );
            Template.create( this );
        },
		/**
		 * Triggered when attachment details change
		 * Overrides Backbone.Model.sync
		 *
		 * @param {string} method
		 * @param {wp.media.model.Attachment} model
		 * @param {Object} [options={}]
		 *
		 * @returns {Promise}
		 */
		sync: function( method, model, options ) {
			// If the attachment does not yet have an `id`, return an instantly
			// rejected promise. Otherwise, all of our requests will fail.
			/*if ( _.isUndefined( this.id ) ) {
				return $.Deferred().rejectWith( this ).promise();
			}

			// Overload the `read` request so Attachment.fetch() functions correctly.
            if ( 'delete' === method ) {
				options = options || {};

				if ( ! options.wait ) {
					this.destroyed = true;
				}

				options.context = this;
				options.data = _.extend( options.data || {}, {
					action:   'delete-post',
					id:       this.id,
					_wpnonce: this.get('nonces')['delete']
				});

				return media.ajax( options ).done( function() {
					this.destroyed = true;
				}).fail( function() {
					this.destroyed = false;
				});

			// Otherwise, fall back to `Backbone.sync()`.
			} else {
				/**
				 * Call `sync` directly on Backbone.Model
				 */  /*
				return Backbone.Model.prototype.sync.apply( this, arguments );
			} */
		},
		/**
		 * Convert date strings into Date objects.
		 *
		 * @param {Object} resp The raw response object, typically returned by fetch()
		 * @returns {Object} The modified response object, which is the attributes hash
		 *    to be set on the model.
		 */
		parse: function( resp ) {
			if ( ! resp ) {
				return resp;
			}

			resp.date = new Date( resp.date );
			resp.modified = new Date( resp.modified );
			return resp;
		}
	}, {
		/**
		 * Add a model to the end of the static 'all' collection and return it.
		 *
		 * @static
		 * @param {Object} attrs
		 * @returns {wp.media.model.Attachment}
		 */
		create: function( attrs ) {
		    ////api.log( attrs );
            Templates.all.models = _.uniq(Templates.all.models , function(item, key, id){
                return item.id;
            });
            ////api.log( _.size(Attachments.all.models) );
			return Templates.all.models.push( attrs );
		},
		/**
		 * Retrieve a model, or add it to the end of the static 'all' collection before returning it.
		 *
		 * @static
		 * @param {string} id A string used to identify a model.
		 * @param {Backbone.Model|undefined} attachment
		 * @returns {wp.media.model.Attachment}
		 */
		get: _.memoize( function( id, template ) {
			return Templates.all.push( template || { id: id } );
		})
	});


    Templates = api.Templates = api.Class.extend({

        initialize: function( id, options ){
            var self = this;

            this.models = [];
            this.props = (options && options.props) ? options.props : {};
            this.modelsTemplate = "";
            this.cid = (this.cid) ? this.cid + 1 : 1;
            //id = ( !id ) ? "All" : id;

            //api.Events.bind( "changeQuery" + id  , this.test , this);
            //api.Events.trigger( "changeQuery" + id );

            //this.props.on( 'change:order',   this._changeOrder,   this );

        },

        changedView : function( type , model ){
            var compiled = api.template("sed-template-lib-item");
            switch ( type ) {
              case "append":
                  this.modelsTemplate += compiled( model );
                  ////api.log( this.cid );
                  //alert( this.modelsTemplate );
              break;
              case "remove":

              break;
              case "preFrom":

              break;
              case "nextFrom":

              break;
            }
        },

        fetch : function(options) {
            var model = this;
            options = options ? _.clone(options) : {};

            options.success = function( resp ){   ////api.log( resp );
                if( resp.length == 0 )
                    return false;

                _.each( resp , function( value, key ){
                    model.addModel( value , options);
                });
                    ////api.log(model);
                model.models = _.uniq(model.models , function(item, key, id){
                    return item.id;
                });
                    ////api.log(model.length);

            };

            return this.sync( options );
        },

        addModel : function( resp , options){
            var self = this;

            var template = new Template( resp );
            self.models.push( template );
            self.length = self.models.length;
            //self.changedView( "append" , value );

        },

        removeModel : function( model ){
            var self = this;
            self.removeModelById(model.id);

        },

        removeModelById : function( id ){
            var self = this;
            this.models = _.filter( this.models , function(model){ return model.id != id; })
        }

    } , {

		/**
		 * @namespace
		 */
		filters: {
			/**
			 * @static
			 * Note that this client-side searching is *not* equivalent
			 * to our server-side searching.
			 *
			 * @param {wp.media.model.Attachment} attachment
			 *
			 * @this wp.media.model.Attachments
			 *
			 * @returns {Boolean}
			 */
			search: function( props , template ) {
				if ( ! props.search ) {    //this.props
					return true;
				}

				return _.any(['title','name','description','tags','author'], function( key ) {
					var value = template.get( key );
					return value && -1 !== value.search( props.search );
				}, this );
			},
			/**
			 * @static
			 * @param {wp.media.model.Attachment} attachment
			 *
			 * @this wp.media.model.Attachments
			 *
			 * @returns {Boolean}
			 */
            //template type is template group
			type: function( props , template ) {
				var type = props.type;
				return ! type || type == template.type ;  //type.indexOf(  )
			},

		}
    });

    Templates.all = new Templates();

    Query = api.TemplateQuery = api.Templates.extend({

		/**
		 * @global wp.Uploader
		 *
		 * @param {Array} [models=[]] Array of models used to populate the collection.
		 * @param {Object} [options={}]
		 */
		initialize: function( models, options ) {
			var allowed;

			options = options || {};
			Templates.prototype.initialize.apply( this, arguments );

			this.args     = options.args;
			this._hasMore = true;
			this.created  = new Date();
            this.length = 0;
		},

		/**
		 * @returns {Boolean}
		 */
		hasMore: function() {
			return this._hasMore;
		},
		/**
		 * @param {Object} [options={}]
		 * @returns {Promise}
		 */
		more: function( options ) {
			var query = this;

			if ( this._more && 'pending' === this._more.state() ) {
				return this._more;
			}

			if ( ! this.hasMore() ) {
				return $.Deferred().resolveWith( this ).promise();
			}

			options = options || {};
			options.remove = false;
                      ////api.log( options );
			return this._more = this.fetch( options ).done( function( resp ) {
			    ////api.log( resp );
				if ( _.isEmpty( resp ) || -1 === this.args.item_per_page || resp.length < this.args.item_per_page ) {
		  	        query._hasMore = false;
				}
			});
		},

		/**
		 * Overrides Backbone.Collection.sync
		 * Overrides wp.media.model.Attachments.sync
		 *
		 * @param {String} method
		 * @param {Backbone.Model} model
		 * @param {Object} [options={}]
		 * @returns {Promise}
		 */
		sync: function( options ) {
			var args, self = this;

            options = options || {};
            options.context = this;
            options.data = _.extend( options.data || {}, {
                action        : 'load_templates',
                nonce         : api.addOnSettings.template.nonce.load ,
                sed_page_ajax : 'sed_load_templates',

            });

            // Clone the args so manipulation is non-destructive.
            args = _.clone( this.args );

            // Determine which page to query.
            if ( -1 !== args.item_per_page ) {
            	args.paged = Math.floor( this.length / args.item_per_page ) + 1;
            }

            options.data.query = args;  ////api.log( options );

            return api.wpAjax.send( options );
		},
    }, {
		/**
		 * @readonly
		 */
		defaultProps: {
			orderby: 'date',
			order:   'DESC'
		},
		/**
		 * @readonly
		 */
		defaultArgs: {
			item_per_page: 6
		},
		/**
		 * @readonly
		 */
		orderby: {
			allowed:  [ 'name', 'author', 'date', 'title', 'id' ],
			valuemap: {
				'id':         'template_id'
			}
		},
		/**
		 * @readonly
		 */
		propmap: {
			'search':    's',
			'type':      'group',
			'perPage':   'item_per_page'
		},
		/**
		 * @static
		 * @method
		 *
		 * @returns {wp.media.model.Query} A new query.
		 */
		// Caches query objects so queries can be easily reused.
		get: (function(){
			/**
			 * @static
			 * @type Array
			 */
			var queries = [];
			/**
			 * @param {Object} props
			 * @param {Object} options
			 * @returns {Query}
			 */
			return function( props, options ) {
				var args     = {},
					orderby  = Query.orderby,
					defaults = Query.defaultProps,
					query,
					cache    = !! props.cache || _.isUndefined( props.cache );

				// Remove the `query` property. This isn't linked to a query,
				// this *is* the query.
				delete props.query;
				delete props.cache;

				// Fill default args.
				_.defaults( props, defaults );

				// Normalize the order.
				props.order = props.order.toUpperCase();
				if ( 'DESC' !== props.order && 'ASC' !== props.order ) {
					props.order = defaults.order.toUpperCase();
				}

				// Ensure we have a valid orderby value.
				if ( ! _.contains( orderby.allowed, props.orderby ) ) {
					props.orderby = defaults.orderby;
				}

				// Generate the query `args` object.
				// Correct any differing property names.
				_.each( props, function( value, prop ) {
					if ( _.isNull( value ) ) {
						return;
					}

					args[ Query.propmap[ prop ] || prop ] = value;
				});

				// Fill any other default query args.
				_.defaults( args, Query.defaultArgs );

				// `props.orderby` does not always map directly to `args.orderby`.
				// Substitute exceptions specified in orderby.keymap.
				args.orderby = orderby.valuemap[ props.orderby ] || props.orderby;

				// Search the query cache for matches.
				if ( cache ) {
					query = _.find( queries, function( query ) {
						return _.isEqual( query.args, args );
					});
				} else {
					queries = [];
				}

				// Otherwise, create a new query and add it to the cache.
				if ( ! query ) {
					query = new Query( [], _.extend( options || {}, {
						props: props,
						args:  args
					} ) );
					queries.push( query );
				}

                Query.queries = queries;

				return query;
			};
		}())
	});


    Library = api.AppTemplate = api.Class.extend({

       initialize: function( options ){

            this.currentType = "";
            this.template;
            this.search = '';
            this.ajaxProcessing = false;
            this.libLoadProcessing = false;

            $.extend( this, options || {} );

            this.ready();
        },

        ready : function(){
          var self = this;
          //initialize library dialog
          $( Library.dialog.selector ).dialog( Library.dialog.options );

          $("#sed-app-control-select_template").on("click" , function(){
              $( Library.dialog.selector ).dialog( "open" );
              self.set({ type : self.currentType });
          });

          var _lazyLoadTemplates = _.debounce(function(){     //alert("test");
              var args = {type : self.currentType };
              if( $.trim(self.search) )
                  args.search = self.search;

              self.set( args , true);
          }, 100);

          $( Library.container ).mCustomScrollbar({
              //autoHideScrollbar:true ,
              advanced:{
                  updateOnBrowserResize:true, //update scrollbars on browser resize (for layouts based on percentages): boolean
                  updateOnContentResize:true,
              },
              scrollButtons:{
                enable:true
              },
              callbacks:{
                  onTotalScroll:function(){
                      ////api.log("onTotalScroll");

                      if(self.ajaxProcessing === false && self.libLoadProcessing === false ){
                          self.libLoadProcessing = true;
                          _lazyLoadTemplates();
                          //_.throttle(self.set( args , true), 300);
                      }

                  },
                  onTotalScrollOffset:120,
              }
          });



          $(Library.searchBox).on("keyup" , function(){
              self.search = $(this).val();
              var args = {type : self.currentType };
              if( $.trim(self.search) )
                  args.search = self.search;

              self.set( args );
          });

          $(Library.filterBox).on("change" , function(){
              var value = $(this).val();
              self.currentType = (value == "all") ? "" : value;

              var args = {type : self.currentType };
              if( $.trim(self.search) )
                  args.search = self.search;

              $(Library.library).html( "" );
               self.set( args );
          });

          $("#save_as_new_template").click(function(){
              $("#sed-dialog-save-new-template").dialog("open");
          });

          $("#sed-dialog-save-new-template").dialog({
              height    : 400 ,
              width     : 400 ,
              modal     : false ,
              autoOpen  : false
          });

          $("#add-new-template-btn").on("click" , function(){
              self.addNewTemplate();
          });

          $("#select-sed-template-screenshot").on("click" , function(){
              api.previewer.trigger( 'openMediaLibrary' , {
                      options :  {
                          'media'     : {
                              "supportTypes"       : ["image"],
                              "selctedType"        : "single",   // single or multiple
                              "dialog"     : {
                                  //"title"     :    $dialog_title,
                                  "buttons"   :    [
                                      {
                                          //"title"    :   $add_btn_title ,
                                          "type"     :   "change_media" ,
										  "select_validation" :   true
                                      }
                                  ]
                              },
                              eventKey       :  "TemplateScreenshot"
                              //"shortcode"  : ,
                              //"attr"       :  "src"
                          }
                      }
              });
          });

          api.previewer.bind( "sedChangeMediaTemplateScreenshot" ,  function( attachment ) {
              //api.log( attachment );
              var tpl = api.template("sed-template-screenshot") ;  //self.template( model )
              attachment.imgUrl = ( attachment.sizes && attachment.sizes.medium ) ? attachment.sizes.medium.url  : attachment.url;
              var html = tpl( attachment );
              $("#sed-template-screenshot-preview").html( html );
              $("form.add-new-template").find('[name="screenshot"]').val( attachment.imgUrl );
          });


        },

        set : function( props , refresh ){
              var self = this , html = "";
              //delete this.template;

              var query = api.TemplateQuery.get(props) , perPage = query.args.item_per_page ;
                      //api.log( query );
              if( ( query.length >= perPage  && !refresh ) || !query._hasMore ){
                  self.libView( query );
                  self.libLoadProcessing = false;
                  return ;
              }

              ////api.log( query );
              self.ajaxProcessing = true;
                                           ////api.log( query );
              query.more().done(function(){
                  ////api.log( query.models );

                  //var startTime = new Date();

                  self.libView( query );

                  ////api.log( new Date() - startTime );
                  self.libLoadProcessing = false;
                  self.ajaxProcessing = false;
              });

        },


        libView : function( query ){
            var html = "" , self = this;
            this.template = api.template("sed-template-lib-item");  //self.template( model )

            _.each(query.models , function( model , key){
                html += self.template( model );
            });

            $(Library.library).html( html );

            /*_.each(self.selection.models , function( model , key){
                $(Library.library).find("li[data-id='" + model.id + "'] > a").addClass( "sed-media-item-selected" );
            }); */
        } ,

        addNewTemplate : function(){

           var medias = _.pluck( api.attachmentsSettings , 'id' ) ,
               modules = _.omit( api.pageModuleUsing , 'id') , moduleStrings;
           //api.log( modules );
           moduleStrings = _.map( modules , function( module ){
              return JSON.stringify( module );
           });

           moduleStrings = _.uniq( moduleStrings );

           modules = _.map( moduleStrings , function( moduleString ){
              return $.parseJSON( moduleString );
           });



            var self = this ,
                form      = $("form.add-new-template") ,
                formfields = form.serializeArray(),
                template  = {
                    options : {
                        requirement : {
                            modules      : modules,
                            medias       : medias,
                            icons_fonts  : {},   //in fixed icon font bug step
                            fonts        : {},   //in color & font step
                            widgets      : {},    // in widget step  widget lists : api( "page_widgets_list" )() in site-iframe/siteeditor-widgets.min.js line 57
                            posts        : {}     // in posts organize step
                        },
                        settings    : {
                            sed_main_content_shortcode  : api.mainContentShortcode || "" ,
                            sed_page_customized         : api.get() , //JSON.stringify( )
                            sed_posts_content           : api.postsContent[api.settings.page.id] || [] , //JSON.stringify(  )
                        }
                    }
                };

            $.each( formfields , function(i, field){
                template[field.name] = field.value ;
            });

            var _alertView = function( output , alertType  ){
                var tpl = api.template("sed-save-template-alert") ,
                    data = {
                        output : output ,
                        alertType : alertType
                    },
                    html = tpl( data );

                //.slideDown( 300 ).delay( 5000 ).fadeOut( 400 );
                $("#save-template-alert-box").html( html );

            };

            var templateAjaxSave = new api.Ajax({
                data : {
                    template      : JSON.stringify( template ) ,
                    action        : 'save_template',
                    nonce         : api.addOnSettings.template.nonce.save ,
                    sed_page_ajax : 'sed_save_template',
                },

                success : function(){
                    form[0].reset();
                    $("#sed-template-screenshot-preview").html( '' )

                    _alertView( this.response.data.output , "success" );

                    //_.delay(function(){
                    self.addSaveTemplateModel( this.response.data.template );
                    var args = {type : self.currentType };
                    if( $.trim(self.search) )
                        args.search = self.search;

                    self.set( args );

                    //}, 1000 );

                },

                error : function(){
                   _alertView( this.response.data.output , "error" );
                }

            }, {
                container   : "body" ,
                repeatRequest : true
            });

        },

        addSaveTemplateModel : function( template ){
            var self = this;

            var template = new Template( template );

            queries = Query.queries;
               ////api.log( queries );
            _.each( queries , function( query , index ){
                var props = query.props;
                //api.log( props );
                //api.log( Templates.filters.search( props , template ) );
                //api.log( Templates.filters.type( props , template ) );
                if( Templates.filters.search( props , template ) && Templates.filters.type( props , template ) ){
                    query.models.unshift( template );
                    query.length = query.models.length;
                }
            });

        }

    }, {

        /**
         * @readonly
         */
        dialog  : {
            selector : "#sed-dialog-template-library",
            options : {
                dialogClass: "dialog-template-library",
                autoOpen: false,
                modal: true,
                width: 900,
                height: 600,
                buttons: {
                  "Cancel": function () {
                    $(this).dialog("close");
                  }
                }
            }
        },
        /**
         * @readonly
         */
         library : '#site-editor-template-library' ,

         //search box element
         searchBox : '#template-library-search' ,
         filterBox : '#template-group-filter',
         container : "#sed-template-library-container"

    });

    $( function() {

        api.previewer.bind( 'mainContentShortcodeUpdate' , function( data ){
            api.mainContentShortcode = data;
        });

        api.appTemplate = new api.AppTemplate();

    });

})( sedApp, jQuery );