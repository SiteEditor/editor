/**
 * siteEditor.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */


define("siteEditor/siteEditorCore", [
	"siteEditor/AddOnManager",
	"siteEditor/dom/ScriptLoader",
    "siteEditor/util/URI",
    "siteEditor/util/Tools"
], function( AddOnManager, ScriptLoader, URI, Tools ) {
	var explode = Tools.explode, each = Tools.each, extend = Tools.extend;
	var instanceCounter = 0, beforeUnloadDelegate;
	var PluginManager = AddOnManager.PluginManager;
	var inArray = Tools.inArray, trim = Tools.trim, resolve = Tools.resolve;

	var siteEditor = {
		/**
		 * Major version of siteEditor build.
		 *
		 * @property majorVersion
		 * @type String
		 */
		majorVersion : '@@majorVersion@@',

		/**
		 * Minor version of siteEditor build.
		 *
		 * @property minorVersion
		 * @type String
		 */
		minorVersion : '@@minorVersion@@',

		/**
		 * Release date of siteEditor build.
		 *
		 * @property releaseDate
		 * @type String
		 */
		releaseDate: '@@releaseDate@@',

		/**
		 * Collection of language pack data.
		 *
		 * @property i18n
		 * @type Object
		 */
		I18n: {},
		/**
		 * Name/Value object containting plugin instances.
		 *
		 * @property plugins
		 * @type Object
		 * @example
		 * // Execute a method inside a plugin directly
		 * mindmap.activeEditor.plugins.someplugin.someMethod();
		 */
		plugins : {},


		setup: function() {

			var self = this, baseURL, documentBaseURL, suffix = "", settings = self.settings;

			// Get base URL for the current document
			documentBaseURL = document.location.href.replace(/[\?#].*$/, '').replace(/[\/\\][^\/]+$/, '');
			if (!/[\/\\]$/.test(documentBaseURL)) {
				documentBaseURL += '/';
			}

			/**
			 * Base URL where the root directory if siteEditor is located.
			 *
			 * @property baseURL
			 * @type String
			 */
			self.baseURL = LIBBASE.url + "siteeditor/";

			/**
			 * Document base URL where the current document is located.
			 *
			 * @property documentBaseURL
			 * @type String
			 */
			self.documentBaseURL = documentBaseURL;

			/**
			 * Absolute baseURI for the installation path of siteEditor.
			 *
			 * @property baseURI
			 * @type siteEditor.util.URI
			 */
			self.baseURI = new URI(self.baseURL);

			/**
			 * Current suffix to add to each plugin/theme that gets loaded for example ".min".
			 *
			 * @property suffix
			 * @type String
			 */
		    self.suffix = ".min";

            self.siteSelector = "";

            self.rtl = false;

		},

        createPlugins : function(){
            var self = this, settings = self.settings, initializedPlugins = [];

			function initPlugin(plugin) {
				var constr = PluginManager.get(plugin), url, pluginInstance;

				url = PluginManager.urls[plugin] || self.documentBaseURL.replace(/\/$/, '');

				plugin = trim(plugin);
				if (constr && inArray(initializedPlugins, plugin) === -1) {
					each(PluginManager.dependencies(plugin), function(dep){
						initPlugin(dep);
					});

					pluginInstance = new constr(self, url);

					self.plugins[plugin] = pluginInstance;

					if (pluginInstance.init) {
						pluginInstance.init(self, url);
						initializedPlugins.push(plugin);
					}
				}
			}

			// Create all plugins
			jQuery.each(settings.plugins, function(index,plugin) { initPlugin(plugin); } );
        },

		/**
		 * Initializes a set of editors. This method will create editors based on various settings.
		 *
		 * @method init
		 * @param {Object} settings Settings object to be passed to each editor instance.
		 * @example
		 *
		 * // Initializes a editor instance using the shorter version
		 * siteEditor.init({
		 *    some_settings : 'some value'
		 * });
		 */
		init: function(settings) {
		    var self = this;

            function readyHandler() {
        		/**
        		 * Name/value collection with editor settings.
        		 *
        		 * @property settings
        		 * @type Object
        		 * @example
        		 * // Get the value of the theme setting
        		 * mindmap.activeEditor.windowManager.alert("You are using the " + mindmap.activeEditor.settings.theme + " theme");
        		 */
        		self.settings = settings = extend({
        			plugins: [],
        			document_base_url: self.documentBaseURL,
        			convert_urls: true,
        			relative_urls: true,
                    rtl: false
        		}, settings);

        		self.I18n = settings.I18n || {};
        		self.rtl = settings.rtl;
                AddOnManager.baseURL = self.baseURL;
                self.siteSelector = settings.siteSelector || "#website";

                self.createPlugins();
                self.render();
            }

            self.settings = settings;
            jQuery( window ).ready(function() {
                readyHandler();
            });

		},

		/**
		 * Render editor
		 *
		 * @method render
		 */
		render: function() {
			var self = this, settings = self.settings, id = self.id, suffix = self.suffix;

			// Load scripts
			function loadScripts() {
				var scriptLoader = ScriptLoader.ScriptLoader;

				/*if (Tools.isArray(settings.plugins)) {
					settings.plugins = settings.plugins.join(' ');
				} */

				each(settings.external_plugins, function(url, name) {
					PluginManager.load(name, url);
					settings.plugins.push(name);
				});

				jQuery.each(settings.plugins, function(index,plugin) {
					plugin = trim(plugin);

					if (plugin && !PluginManager.urls[plugin]) {
						if (plugin.charAt(0) == '-') {
							plugin = plugin.substr(1, plugin.length);

							var dependencies = PluginManager.dependencies(plugin);
                                     ////api.log(dependencies);
							each(dependencies, function(dep) {
								var defaultSettings = {
									prefix:'plugins/',
									resource: dep,
									suffix:'/plugin' + suffix + '.js'
								};

								dep = PluginManager.createUrl(defaultSettings, dep);
								PluginManager.load(dep.resource, dep);
							});
						} else {
							PluginManager.load(plugin, {
								prefix: 'plugins/',
								resource: plugin,
								suffix: '/plugin' + suffix + '.js'
							});
						}
					}
				});


				scriptLoader.loadQueue(function() {
						self.createPlugins();
				});
			}

			loadScripts();
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
        }
	};


   	siteEditor.setup();

	// Export EditorManager as siteEditor/siteEditor in global namespace
	window.siteEditor = window.sed = siteEditor;

	return siteEditor;
});
