/**
 * Compat.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/**
 * siteEditor core class.
 *
 * @static
 * @class siteEditor
 * @borrow-members siteEditor.EditorManager
 * @borrow-members siteEditor.util.Tools
 */
define("siteEditor/Compat", [
	"siteEditor/dom/ScriptLoader",
	"siteEditor/AddOnManager",
    "siteEditor/util/Tools"
], function(ScriptLoader, AddOnManager, Tools) {
	var siteEditor = window.siteEditor;
     //alert(siteEditor);
	/**
	 * @property {siteEditor.dom.DOMUtils} DOM Global DOM instance.
	 * @property {siteEditor.dom.ScriptLoader} ScriptLoader Global ScriptLoader instance.
	 * @property {siteEditor.AddOnManager} PluginManager Global PluginManager instance.
	 * @property {siteEditor.AddOnManager} ThemeManager Global ThemeManager instance.
	 */
    //alert(siteEditor);

    siteEditor.ScriptLoader = ScriptLoader.ScriptLoader;
	siteEditor.PluginManager = AddOnManager.PluginManager;


	Tools.each(Tools, function(func, key) {
		siteEditor[key] = func;
	});
    /*
	Tools.each('isOpera isWebKit isIE isGecko isMac'.split(' '), function(name) {
		siteEditor[name] = Env[name.substr(2).toLowerCase()];
	});  */

	return {};
});

// Describe the different namespaces

/**
 * Root level namespace this contains classes directly releated to the siteEditor editor.
 *
 * @namespace siteEditor
 */

/**
 * Contains classes for handling the browsers DOM.
 *
 * @namespace siteEditor.dom
 */

/**
 * Contains html parser and serializer logic.
 *
 * @namespace siteEditor.html
 */

/**
 * Contains the different UI types such as buttons, listboxes etc.
 *
 * @namespace siteEditor.ui
 */

/**
 * Contains various utility classes such as json parser, cookies etc.
 *
 * @namespace siteEditor.util
 */
