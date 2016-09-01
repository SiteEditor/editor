/*!
 * SiteEditor JavaScript Library v2.0.3
 * http://www.siteeditor.org/
 *
 * Copyright 2014, 2015 siteEditor Foundation, Inc. and other contributors
 * Released under the MIT license
 * http://www.siteeditor.org/license
 *
 * Date: 2013-12-06
 */
(function(exports) {
	"use strict";

	var html = "", baseDir;
	var modules = {}, exposedModules = [], moduleCount = 0;


	var scripts = document.getElementsByTagName('script');

    baseDir = LIBBASE.url + "siteeditor";
    //var baseImagedir = baseDir + "/images/";


	function require(ids, callback) {
		var module, defs = [];

		for (var i = 0; i < ids.length; ++i) {
			module = modules[ids[i]] || resolve(ids[i]);
			if (!module) {
				throw 'module definition dependecy not found: ' + ids[i];
			}

			defs.push(module);
		}

		callback.apply(null, defs);
	}

	function resolve(id) {
		var target = exports;
		var fragments = id.split(/[.\/]/);

		for (var fi = 0; fi < fragments.length; ++fi) {
			if (!target[fragments[fi]]) {
				return;
			}

			target = target[fragments[fi]];
		}

		return target;
	}

	function register(id) {
		var target = exports;
		var fragments = id.split(/[.\/]/);

		for (var fi = 0; fi < fragments.length - 1; ++fi) {
			if (target[fragments[fi]] === undefined) {
				target[fragments[fi]] = {};
			}

			target = target[fragments[fi]];
		}

		target[fragments[fragments.length - 1]] = modules[id];
	}

	function define(id, dependencies, definition) {
		if (typeof id !== 'string') {
			throw 'invalid module definition, module id must be defined and be a string';
		}

		if (dependencies === undefined) {
			throw 'invalid module definition, dependencies must be specified';
		}

		if (definition === undefined) {
			throw 'invalid module definition, definition function must be specified';
		}

		require(dependencies, function() {
			modules[id] = definition.apply(null, arguments);
		});

		if (--moduleCount === 0) {
			for (var i = 0; i < exposedModules.length; i++) {
				register(exposedModules[i]);
			}
		}
	}

	function expose(ids) {
		exposedModules = ids;
	}

	function writeScripts() {
		document.write(html);
	}

	function load(path) {
		html += '<script type="text/javascript" src="' + baseDir + '/' + path + '"></script>\n';
		moduleCount++;
	}

	// Expose globally
	exports.define = define;
	exports.require = require;
                     //"siteEditor/siteEditorCss",                                                                                                                                                                                                                                      //,"siteEditor/modules/Background","siteEditor/modules/Border", "siteEditor/modules/ColorThemes",  "siteEditor/components/StyleEditor"
	expose(["siteEditor/dom/Sizzle" ,"siteEditor/sedAppClass", "siteEditor/siteEditorControls" , "siteEditor/styleEditorControls" , "siteEditor/pbModulesControls" , "siteEditor/modules/mediaClass" , "siteEditor/modules/appPreviewClass" , "siteEditor/modules/appTemplateClass" , "siteEditor/util/Tools","siteEditor/dom/ScriptLoader","siteEditor/AddOnManager","siteEditor/util/URI", "siteEditor/util/I18n" ,"siteEditor/siteEditorCore","siteEditor/Compat"]);


    load('core/dom/Sizzle.jQuery.js');
    load('core/sedAppClass.js');
    load('core/siteEditorControls.js');
    load('core/styleEditorControls.js');
    load('core/pbModulesControls.js');
    load('modules/mediaClass.js');
    load('modules/appPreviewClass.js');
    load('modules/appTemplateClass.js');
	load('core/util/Tools.js');
	load('core/dom/ScriptLoader.js');
	load('core/AddOnManager.js');
    load('core/util/URI.js');
    load('core/util/I18n.js');
    load('core/siteEditor.js');
    //load('core/siteEditorCss.js');
    //load('modules/colorThemes.js');
    //load('modules/background.js');
    //load("modules/border.js");
    //load('components/style-editor/styleEditor.js');
    load('core/Compat.js');


	writeScripts();

})( this );