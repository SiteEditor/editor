
module.exports = function(grunt) {
    grunt.initConfig({

        // Setting folder templates.
        dirs: {

            base : {
                css     : 'assets/css',
                fonts   : 'assets/fonts',
                images  : 'assets/images',
                js      : 'assets/js'
            } ,

            editor : {
                css     : 'editor/assets/css',
                fonts   : 'editor/assets/fonts',
                images  : 'editor/assets/images',
                js      : 'editor/assets/js' ,
                libs    : 'editor/assets/libs'
            } ,

            framework : {
                css     : 'framework/assets/css',
                fonts   : 'framework/assets/fonts',
                images  : 'framework/assets/images',
                js      : 'framework/assets/js'
            }

        },


        // JavaScript linting with JSHint.
        jshint: {
            options: {
                curly: true,
                eqeqeq: true,
                eqnull: true,
                browser: true,
                globals: {
                    "_"         : true,
                    "Backbone"  : true,
                    "jQuery"    : true,
                    "JSON"      : true,
                    "wp"        : true ,
                    "sedApp"    : true
                }
            },
            all: [
                '**/*.js',
                '!**/*.min.js' ,
                '!node_modules/**/*.js'
            ]
        },

        uglify: {
            all: {
                files: [{
                    expand: true,
                    //cwd: '/',
                    src: [
                        '**/*.js',
                        '!**/*.min.js' ,
                        '!node_modules/**/*.js' ,
                        '!Gruntfile.js' ,
                        '!<%= dirs.editor.js %>/siteeditor/**/*.js' ,
                        '!<%= dirs.editor.js %>/frontend-editor/**/*.js' ,
                        '!<%= dirs.editor.libs %>/**/*.js' ,
                        '!<%= dirs.framework.js %>/jplayer/**/*.js'
                    ],
                    //dest: '/',
                    ext: '.min.js'
                }]
            } ,

            editor: {
                src: [
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-base.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-shortcode.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-ajax.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-modules-scripts.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteEditorCss.js' ,
                    '<%= dirs.editor.js %>/siteeditor/core/siteEditorControls.js',
                    '<%= dirs.editor.js %>/siteeditor/core/styleEditorControls.js',
                    '<%= dirs.editor.js %>/siteeditor/core/pbModulesControls.js',
                    '<%= dirs.editor.js %>/siteeditor/modules/appPreviewClass.js',
                    '<%= dirs.editor.js %>/siteeditor/modules/appTemplateClass.js',
                    '<%= dirs.editor.js %>/siteeditor/plugins/pagebuilder/plugin.js',
                    '<%= dirs.editor.js %>/siteeditor/plugins/contextmenu/plugin.js',
                    '<%= dirs.editor.js %>/siteeditor/plugins/settings/plugin.js',
                    '<%= dirs.editor.js %>/siteeditor/plugins/save/plugin.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-settings-render.js' ,
                ],
                dest: '<%= dirs.editor.js %>/siteeditor.min.js'
            } ,

            frontendEditor : {
                src: [
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-base.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-shortcode.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-ajax.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteeditor-modules-scripts.js' ,
                    '<%= dirs.editor.js %>/siteeditor/siteEditorCss.js' ,
                    '<%= dirs.editor.js %>/frontend-editor/column-resize.js',
                    '<%= dirs.editor.js %>/frontend-editor/siteeditor-preview.js',
                    '<%= dirs.editor.js %>/frontend-editor/siteeditor-preview-render.js',
                    '<%= dirs.editor.js %>/frontend-editor/sed-app-selective-refresh.js',
                    '<%= dirs.editor.js %>/frontend-editor/shortcode-content-builder.js',
                    '<%= dirs.editor.js %>/frontend-editor/site-iframe.js',
                    '<%= dirs.editor.js %>/frontend-editor/plugins/app-preview.js',
                    '<%= dirs.editor.js %>/frontend-editor/pagebuilder.js',
                    '<%= dirs.editor.js %>/frontend-editor/siteeditor-contextmenu.js',
                    '<%= dirs.editor.js %>/frontend-editor/plugins/delete.js',
                    '<%= dirs.editor.js %>/frontend-editor/plugins/select.js',
                    '<%= dirs.editor.js %>/frontend-editor/plugins/media.js',
                    '<%= dirs.editor.js %>/frontend-editor/plugins/duplicate.js'
                ],
                dest: '<%= dirs.editor.js %>/frontend-editor.min.js'
            }

        } ,

        // Minify all .css files.
        cssmin: {

            minify: {
                expand: true,
                //cwd: '',
                src: [
                    '**/*.css',
                    '!**/*.min.css' ,
                    '!node_modules/**/*.css' ,
                    '!<%= dirs.editor.css %>/siteeditor/**/*.css' ,
                    '!<%= dirs.editor.libs %>/**/*.css'
                ],
                //dest: '',
                ext: '.min.css'
            } ,

            editor: {
                src: [
                    '<%= dirs.editor.css %>/siteeditor/accordion.css' ,
                    '<%= dirs.editor.css %>/siteeditor/alert.css' ,
                    '<%= dirs.editor.css %>/siteeditor/animation.css' ,
                    '<%= dirs.editor.css %>/siteeditor/button.css' ,
                    '<%= dirs.editor.css %>/siteeditor/color-fonts/color-fonts.css' ,
                    '<%= dirs.editor.css %>/siteeditor/colorselector/colorselector.css',
                    '<%= dirs.editor.css %>/siteeditor/dialog/dialog.css',
                    '<%= dirs.editor.css %>/siteeditor/dialog-page-box/dialog-page-box.css',
                    '<%= dirs.editor.css %>/siteeditor/dropdown/dropdown.css',
                    '<%= dirs.editor.css %>/siteeditor/dropdown-styleeditor/dropdown-styleeditor.css',
                    '<%= dirs.editor.css %>/siteeditor/icon-library/icon-library.css',
                    '<%= dirs.editor.css %>/siteeditor/library/library.css',
                    '<%= dirs.editor.css %>/siteeditor/loading/loading.css',
                    '<%= dirs.editor.css %>/siteeditor/multilevelbox/multilevelbox.css' ,
                    '<%= dirs.editor.css %>/siteeditor/chosen/chosen.css' ,
                    '<%= dirs.editor.css %>/siteeditor/colorpicker/spectrum.css' ,
                    '<%= dirs.editor.css %>/siteeditor/scrollbar/scrollbar.css' ,
                    '<%= dirs.editor.css %>/siteeditor/jquery-ui/jquery-ui.css' ,
                    '<%= dirs.editor.css %>/siteeditor/jquery-ui/jquery-ui-ie.css' ,
                    '<%= dirs.editor.css %>/siteeditor/organize-posts/organize-posts.css' ,
                    '<%= dirs.editor.css %>/siteeditor/pages/pages.css' ,
                    '<%= dirs.editor.css %>/siteeditor/panel/panel.css' ,
                    '<%= dirs.editor.css %>/siteeditor/privew/privew.css' ,
                    '<%= dirs.editor.css %>/siteeditor/settings/settings.css' ,
                    '<%= dirs.editor.css %>/siteeditor/siteeditor-app/siteeditor-app.css' ,
                    '<%= dirs.editor.css %>/siteeditor/slider/slider.css' ,
                    '<%= dirs.editor.css %>/siteeditor/spinner/spinner.css' ,
                    '<%= dirs.editor.css %>/siteeditor/style-editor/style-editor.css' ,
                    '<%= dirs.editor.css %>/siteeditor/tab/tab.css' ,
                    '<%= dirs.editor.css %>/siteeditor/template-library/template-library.css' ,
                    '<%= dirs.editor.css %>/siteeditor/tooltip/tooltip.css' ,
                    '<%= dirs.editor.css %>/siteeditor/z2/z.css' ,
                    '<%= dirs.editor.css %>/siteeditor/font-icon-siteeditor.css' ,
                    '<%= dirs.editor.css %>/siteeditor/font-extra.css'
                ],
                dest: '<%= dirs.editor.css %>/siteeditor.min.css'
            }

        },

        // Watch changes for assets.
        watch: {
            options: {
                reload: true ,
                livereload : true ,
                host: 'localhost',
            },
            css: {
                files: [
                    '**/*.css',
                    '!**/*.min.css' ,
                    '!node_modules/**/*.css'
                ],
                tasks: ['cssmin']
            },
            js: {
                files: [
                    '**/*.js',
                    '!**/*.min.js' ,
                    '!node_modules/**/*.js' ,
                    '!Gruntfile.js'
                ],
                tasks: ['uglify']//'jshint',
            }
        },

        // Generate POT files.
        makepot: {
            options: {
                type: 'wp-plugin',
                domainPath: 'languages',
                potHeaders: {
                    'report-msgid-bugs-to': 'https://siteeditor.org/products/issues',
                    'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
                }
            },
            dist: {
                options: {
                    potFilename: 'site-editor.pot',
                    exclude: [
                        'tmp/.*'
                    ]
                }
            }
        },

        // Check textdomain errors.
        checktextdomain: {
            options:{
                text_domain: 'site-editor',
                keywords: [
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                src:  [
                    '**/*.php', // Include all files
                    '!node_modules/**', // Exclude node_modules/
                    '!tmp/**' // Exclude tmp/
                ],
                expand: true
            }
        }

    });

    // Load NPM tasks to be used here
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
    grunt.loadNpmTasks( 'grunt-contrib-jshint' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );

    // Register tasks
    grunt.registerTask( 'default', [
        'jshint',
        'uglify',
        'css'
    ]);

    grunt.registerTask( 'js', [
        'jshint',
        'uglify:all',
        'uglify:editor' ,
        'uglify:frontendEditor'
    ]);

    grunt.registerTask( 'css', [
        'cssmin:minify' ,
        'cssmin:editor'
    ]);

    grunt.registerTask( 'dev', [
        'default',
        'makepot'
    ]);

};