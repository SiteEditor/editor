/**
 * ScriptLoader.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*globals console*/

/**
 * This class handles asynchronous/synchronous loading of JavaScript files it will execute callbacks
 * when various items gets loaded. This class is useful to load external JavaScript files.
 *
 * @class siteEditor.dom.ScriptLoader
 * @example
 * // Load a script from a specific URL using the global script loader
 * siteEditor.ScriptLoader.load('somescript.js');
 *
 * // Load a script using a unique instance of the script loader
 * var scriptLoader = new siteEditor.dom.ScriptLoader();
 *
 * scriptLoader.load('somescript.js');
 *
 * // Load multiple scripts
 * var scriptLoader = new siteEditor.dom.ScriptLoader();
 *
 * scriptLoader.add('somescript1.js');
 * scriptLoader.add('somescript2.js');
 * scriptLoader.add('somescript3.js');
 *
 * scriptLoader.loadQueue(function() {
 *    alert('All scripts are now loaded.');
 * });
 */
define("siteEditor/dom/ScriptLoader",
     ["siteEditor/dom/Sizzle" ,"siteEditor/util/Tools"], function( $ ,Tools) {
    //var loadScript = jQuery.cachedScript;
    //var request;
    var each = Tools.each , grep = Tools.grep;

    //static variable
    var QUEUED = ScriptLoader.QUEUED  = 0;
    var LOADING = ScriptLoader.LOADING = 1;
    var LOADED = ScriptLoader.LOADED  = 2;



    function ScriptLoader() {
      var self = this;

       //self.scripts = [];
       self.request = {};
       self.states = {},
	   self.queue = [],
	   self.scriptLoadedCallbacks = {},
	   self.queueLoadedCallbacks = [],
	   self.loading = 0,
	   self.undef;
       self.counter = 1;

    }

    ScriptLoader.prototype = {
            /**
            * Adds a specific script to the load queue of the script loader.
            *
            * @method add
            * @param {String} url Absolute URL to script to add.
            * @param {function} callback Optional callback function to execute ones this script gets loaded.
            * @param {Object} scope Optional scope to execute callback in.
            */
            add: function( url, callback , scope) {
    			var state = this.states[url];

    			// Add url to load queue
    			if (state == this.undef) {
    				this.queue.push(url);
    				this.states[url] = this.QUEUED;
    			}

    			if (callback) {

    				// Store away callback for later execution
    				if (!this.scriptLoadedCallbacks[url]) {
    					this.scriptLoadedCallbacks[url] = [];
    				}

    				this.scriptLoadedCallbacks[url].push({
    					func: callback,
    					scope: scope || this
    				});

    			}
            },

            loadScript: function( url, callback ) {
    			var elm, id;

    			// Execute callback when script is loaded
    			function done() {
    				$("#" + id).remove();

    				if (elm) {
    					elm.onreadystatechange = elm.onload = elm = null;
    				}

    				callback();
    			}

    			function error() {
    				// Report the error so it's easier for people to spot loading errors
    				if (typeof(console) !== "undefined" && console.log) {
    					console.log("Failed to load: " + url);
    				}

    				// We can't mark it as done if there is a load error since
    				// A) We don't want to produce 404 errors on the server and
    				// B) the onerror event won't fire on all browsers.
    				// done();
    			}

    			id = "sed_app_plugins" + self.counter;
                self.counter++;

    			// Create new script element
    			elm = document.createElement('script');
    			elm.id = id;
    			elm.type = 'text/javascript';
    			elm.src = url;

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
    			(document.getElementsByTagName('head')[0] || document.body).appendChild(elm);
            },

            /**
             * Returns true/false if a script has been loaded or not.
             *
             * @method isDone
             * @param {String} url URL to check for.
             * @return {Boolean} true/false if the URL is loaded.
             */
            isDone: function(url) {
            	return this.states[url] == this.LOADED;
            },

    		/**
    		 * Marks a specific script to be loaded. This can be useful if a script got loaded outside
    		 * the script loader or to skip it from loading some script.
    		 *
    		 * @method markDone
    		 * @param {string} u Absolute URL to the script to mark as loaded.
    		 */
    		markDone: function(url) {
    			this.states[url] = this.LOADED;
    		},

    		/**
    		 * Starts the loading of the queue.
    		 *
    		 * @method loadQueue
    		 * @param {function} callback Optional callback to execute when all queued items are loaded.
    		 * @param {Object} scope Optional scope to execute the callback in.
    		 */
    		loadQueue: function(callback, scope) {
    			this.loadScripts(this.queue, callback, scope);
    		},
            /*
  			execScriptLoadedCallbacks: function(url) {
  				// Execute URL callback functions
                var slc = ScriptLoader.scriptLoadedCallbacks[url];

  				each(slc, function(callback) {
  					callback.func.call(callback.scope);
  				});

  				ScriptLoader.scriptLoadedCallbacks[url] = ScriptLoader.undef;
  			}, */

    		/**
    		 * Loads the specified queue of files and executes the callback ones they are loaded.
    		 * This method is generally not used outside this class but it might be useful in some scenarios.
    		 *
    		 * @method loadScripts
    		 * @param {Array} scripts Array of queue items to load.
    		 * @param {function} callback Optional callback to execute ones all items are loaded.
    		 * @param {Object} scope Optional scope to execute callback in.
    		 */
    		loadScripts: function(scripts, callback, scope) {
    			var loadScripts;
                var _self = this;

    			function execScriptLoadedCallbacks(url) {
    				// Execute URL callback functions
    				each(_self.scriptLoadedCallbacks[url], function(callback) {
    					callback.func.call(callback.scope);
    				});

    				_self.scriptLoadedCallbacks[url] = _self.undef;
    			}

    			_self.queueLoadedCallbacks.push({
    				func: callback,
    				scope: scope || _self
    			});

    			loadScripts = function() {
    				var loadingScripts = grep(scripts);
                   /* for(val in _self){
                      alert(val);
                    } */
    				// Current scripts has been handled
    				scripts.length = 0;

    				// Load scripts that needs to be loaded
    				each(loadingScripts, function(url) {
    					// Script is already loaded then execute script callbacks directly
    					if (_self.states[url] == LOADED) {
    						execScriptLoadedCallbacks(url);
    						return;
    					}

    					// Is script not loading then start loading it
    					if (_self.states[url] != LOADING) {
    						_self.states[url] = LOADING;
    						_self.loading++;

    						_self.loadScript(url, function() {
    							_self.states[url] = LOADED;
    							_self.loading--;

    							execScriptLoadedCallbacks(url);

    							// Load more scripts if they where added by the recently loaded script
    							loadScripts();
    						});
    					}
    				});

    				// No scripts are currently loading then execute all pending queue loaded callbacks
    				if (!_self.loading) {
    					each(_self.queueLoadedCallbacks, function(callback) {
    						callback.func.call(callback.scope);
    					});

    					_self.queueLoadedCallbacks.length = 0;
    				}
    			};

    			loadScripts();
    		},

    };

    ScriptLoader.ScriptLoader = new ScriptLoader();

	return ScriptLoader;

});