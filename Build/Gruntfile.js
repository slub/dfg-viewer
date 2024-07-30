
module.exports = function (grunt) {
    require('jit-grunt')(grunt);

    grunt.initConfig({
        less: {
            development: {
                options: {
                    sourceMap: true,
                    compress: true,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    "Resources/Public/Css/allStyles.css": "Resources/Private/Less/all.less",
                    "Resources/Public/Css/webStyles.css": "Resources/Private/Less/website.less",
                    "Resources/Public/Css/rte.css": "Resources/Private/Less/rte.less",
                }
            }
        },
        terser: {
            options: {
                compress: true,
                mangle: true,
            },
            development: {
                files: {
                    "Resources/Public/JavaScript/allScripts.js": [
                        'Resources/Private/JavaScript/modernizrCustom.js',
                        'Resources/Private/JavaScript/js.cookie.js',
                        'Resources/Private/JavaScript/dfgviewerSru.js',
                        'Resources/Private/JavaScript/dfgviewerScripts.js',
                    ],
                    "Resources/Public/JavaScript/webScripts.js": [
                        'Resources/Private/JavaScript/modernizrCustom.js',
                        'Resources/Public/JavaScript/Highlight/highlight.pack.js',
                        'Resources/Private/JavaScript/websiteScripts.js',
                    ],
                }
            }
        },
        watch: {
            styles: {
                files: ['Resources/Private/Less/**/*.less'],
                tasks: ['less'],
                options: {
                    spawn: false
                }
            },
            js: {
                files: ['Resources/Private/JavaScript/*.js'],
                tasks: ['terser'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.file.setBase('../')
    grunt.registerTask('default', ['less', 'terser', 'watch']);
};
