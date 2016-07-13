(function( exports, $ ) {

    var api = sedApp.editor ,
        scriptCounter = 0 ;

    api.loadingScripts = [];
    api.loadingStyles = [];

    api.ModulesScriptsLoader = api.Class.extend({
        initialize: function( params , options ){
            var self = this;
            //, $parent = $('[sed-role="row-pb"]').parent()
            //$parent.addClass("sed-pb-rows-box bp-component");

            $.extend( this, options || {} );

            this.ready();
        },

        ready: function(){


        },

        _checkLoadedScript : function( scripts , wpScripts , type ){

            if($.isArray(scripts)){

                var loadingScripts;

                if( type == "js" )
                    loadingScripts = api.loadingScripts;
                else
                    loadingScripts = api.loadingStyles;

                
                scripts = $.grep(scripts , function(script , index){
                    if( $.inArray(script[0] , wpScripts) != -1 || $.inArray(script[0] , loadingScripts) != -1  || !script[0] || !script[1]){
                        return false;
                    }else{
                        return true;
                    }
                });

            }

            return scripts;
        },

        //name :: module name
        moduleScStLoad: function( scripts , wpScripts , callback , type  ) {
			type = type || "js";
            var self = this ,
                loadedQueue = {} , scriptsHandles = [];

            if( !$.isArray(scripts) || scripts.length == 0)
                return ;

            //check if loaded script already or src or handle undefined , delete script from this array


            scripts = self._checkLoadedScript( scripts , wpScripts , type );

            if( scripts.length == 0 )
                return ;

            //delete depency that loaded already && too check exist depency
            var _filterDepsValid = function( scripts ){
                if($.isArray(scripts)){
                    //create clone(or copy) from orginal array
                    //var scriptDef = scripts.slice(0);//$.merge([],scripts);//;
                    var scriptsHandles = [];

                    $.each(scripts , function(index , script){
                        if( $.inArray(script[0] , scriptsHandles) == -1){
                            scriptsHandles.push( script[0] );
                        }
                    });
                         ////api.log( scriptDef );
                    $.each(scripts , function(index , script){

                        if( $.isArray(script[2]) && script[2].length > 0 ){
                            var itemDel = 0;      ////api.log( script[2] );
                            script[2] = $.grep(script[2] , function(dep , idep){  ////api.log( dep );
                                if( $.inArray(dep , wpScripts) == -1 &&  $.inArray(dep , scriptsHandles) == -1 ){
									//api.log("==================== all scripts handle =====================");
									//api.log(scriptsHandles);
									//api.log("==================== all wp scripts  =====================");
									//api.log(wpScripts);
									//api.log("Depency: '" + dep + "' Not Found for '" + script[0] + "'");
                                    return false;

                                }else if($.inArray(dep , wpScripts) != -1 ){
                                    return false;

                                }else{
                                    return true;
                                }
                            });
                        }

                    });


                }
                return scripts;
            };

            scripts = _filterDepsValid( scripts );

            if( !$.isArray(scripts) || scripts.length == 0)
                return ;
                    ////api.log( scripts );
            //arrange scripts by deps
            var _arrangeScripts = function( scripts ){
                if($.isArray(scripts)){

                    var scriptsHandles = [];

                    $.each(scripts , function(index , script){
                        if( $.inArray(script[0] , scriptsHandles) == -1){
                            scriptsHandles.push( script[0] );
                        }

                        var indexH = scriptsHandles.indexOf( script[0] );

                        if( $.isArray(script[2]) && script[2].length > 0 ){
                            $.each(script[2] , function( idep , dep){
                                if( $.inArray(dep , scriptsHandles) == -1 ){
                                    scriptsHandles.splice(indexH , 0 , dep);
                                    indexH += 1;
                                }
                            });
                        }

                    });

                }
                return scriptsHandles;
            };

            scriptsHandles = _arrangeScripts( scripts );
                        ////api.log( scriptsHandles );

            $.each(scriptsHandles , function(index , handle){

                if( type == "js" )
                    api.loadingScripts.push( handle );
                else
                    api.loadingStyles.push( handle );

                loadedQueue[handle] = 0;
            });    ////api.log( loadedQueue );
                   ////api.log( scriptsHandles );
            /*
            cScript[0] :: handle
            cScript[1] :: url
            cScript[2] :: deps
            cScript[3] :: version
            cScript[4] :: in_footer
            */

            var loadedComplate = false;

            var _loadComplate = function(){
                if(loadedComplate === false){
                    api.Events.trigger("moduleLoadComplate");
                    if(callback)
                        callback();

                    loadedComplate = true;
                }
            };

            var numLoad = 0;
            self._loadQueue = function(){
                var $continue = false , allLoaded = 0;

                $.each(loadedQueue , function(hdl , loaded){
                    allLoaded += loaded;
                    if(loaded == 0){
                        $continue = true;
                        return false;
                    }
                });
                           ////api.log( $continue );
                if($continue == false && allLoaded == (2*loadedQueue.length) ){
                  ////api.log( loadedQueue );
                    _loadComplate();  alert("complete");
                    return ;
                }

                if($continue == false ){
                    return ;
                }

                $.each(scriptsHandles , function(index , handle){
                    var url , cScript;
                                        ////api.log( "out  : " + handle );
                    for (var i=0; i< scripts.length; i++)  {
                        if(scripts[i][0] == handle){
                            cScript = scripts[i];
                            break;
                        }
                    }

                    if(loadedQueue[handle] == 0){
                        if(index == 0 || !$.isArray(cScript[2]) || cScript[2].length == 0){
                            loadedQueue[handle] = 1;

                            self.loadScript( cScript[1] , function(){
                                loadedQueue[handle] = 2;

                                self.loadDown( handle , type );
                                ////api.log(handle);
                                self._loadQueue();
                                numLoad++;

                                if( numLoad == scriptsHandles.length)
                                    _loadComplate();
                            }, type, cScript[4]);

                        }else {
                            var iLoaded = 0;
                            $.each(cScript[2] , function(idep , dep){
                                iLoaded += loadedQueue[dep];
                            });

                            if(iLoaded == (cScript[2].length*2)){
                                loadedQueue[handle] = 1;

                                self.loadScript( cScript[1] , function(){
                                    loadedQueue[handle] = 2;

                                    self.loadDown( handle , type );

                                    ////api.log("deps : " + handle);
                                    self._loadQueue();
                                    numLoad++;

                                    if( numLoad == scriptsHandles.length)
                                        _loadComplate();
                                } , type , cScript[4]);

                            }

                        }
                    }

                });
            };

            self._loadQueue();


              /*
            self._loadQueue = function( handle , index ){




                var url , cScript;
                for (var i=0; i< scripts.length; i++)  {
                    if(scripts[i][0] == handle){
                        cScript = scripts[i];
                        break;
                    }
                }

                self.loadScript( cScript[1] , function(){
                    loadedQueue[handle] = 1;
                    if((index+1) < scriptsHandles.length){
                        self._loadQueue( scriptsHandles[(index+1)] , (index+1) );
                    }else{
                        api.Events.trigger("moduleLoadComplate");
                        if(callback)
                            callback();
                    }

                }, type, cScript[4]);

            };

            self._loadQueue( scriptsHandles[0] , 0 ); */
        },

        loadDown : function( handle , type ){
            if( type == "js" ){
                var lIndex = $.inArray( handle , api.loadingScripts );
                if( lIndex > -1 )
                    api.loadingScripts.splice( lIndex , 0)
            }else{
                var lIndex = $.inArray( handle , api.loadingStyles );
                if( lIndex > -1 )
                    api.loadingStyles.splice( lIndex , 0)
            }
        },

        //type :: js or css , extra :: media for css & in_footer for js
        loadScript: function( url, callback , type , extra ) {
			var elm, id, self = this;
			type = type || "js";
			// Execute callback when script is loaded
			function done() {
				//$("#" + id).remove();

				if (elm) {
					elm.onreadystatechange = elm.onload = elm = null;
				}

                if(typeof callback == "function")
				    callback();
			}

			function error() {
				// Report the error so it's easier for people to spot loading errors
				//api.log("Failed to load: " + url);

				// We can't mark it as done if there is a load error since
				// A) We don't want to produce 404 errors on the server and
				// B) the onerror event won't fire on all browsers.
				// done();
			}

			id = "sed_app_modules_scripts_" + type + scriptCounter;
            scriptCounter++;

			// Create new script element
			elm = (type == "js") ? document.createElement('script') : document.createElement('link');
			elm.id = id;
			elm.type = (type == "js") ? 'text/javascript' : 'text/css';

            if(type == "js")
			    elm.src = url;
            else{
                elm.href = url;
                elm.rel = "stylesheet";
                elm.media = extra || "all";
            }
            ////api.log('---------------------------elm-----------------------------------');
            ////api.log(elm);
            ////api.log('--------------------------elm------------------------------------');
			// Seems that onreadystatechange works better on IE 10 onload seems to fire incorrectly
			if ("onreadystatechange" in elm) {
				elm.onreadystatechange = function() {
					if (/loaded|complete/.test(elm.readyState)) {
						done();
					}
				};
			} else {
				elm.onload = done;
			}

			// Add onerror event will get fired on some browsers but not all of them
			elm.onerror = error;

			// Add script to document
            if( (!extra && type == "js") || type == "css" )
			    (document.getElementsByTagName('head')[0] || document.body).appendChild(elm);
            else
                (document.body).appendChild(elm);
        },


    });


}(sedApp, jQuery));