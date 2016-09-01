/**
 * Sizzle.jQuery.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global jQuery:true */

/*
 * Fake Sizzle using jQuery.
 */
define("siteEditor/dom/Sizzle", [], function() {
	// Detect if jQuery is loaded
	if (!window.jQuery) {
		throw new Error("Load jQuery first");
	}

	return jQuery;
});
