/**
 * sedAppClass.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global sedApp:true */

/*
 * Fake Sizzle using jQuery.
 */
define("siteEditor/sedAppClass", [], function() {
	// Detect if jQuery is loaded
	if (!window.sedApp) {
		throw new Error("Load site Editor Base First!");
	}

	return sedApp;
});
