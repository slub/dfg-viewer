
module.exports = function(grunt) {
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
                    "dfgviewer/Resources/Public/Css/allStyles.css" : "dfgviewer/Resources/Private/Less/all.less",
                    "dfgviewer/Resources/Public/Css/webStyles.css" : "dfgviewer/Resources/Private/Less/website.less",
                }
            }
        },
        uglify: {
            development: {
                options: {
                    compress: true,
                    preserveComments: false,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    "dfgviewer/Resources/Public/Js/allScripts.js" : ['dfgviewer/Resources/Private/Javascript/modernizrCustom.js', 'dfgviewer/Resources/Private/Javascript/js.cookie.js', 'dfgviewer/Resources/Private/Javascript/dfgviewerScripts.js'],
                    "dfgviewer/Resources/Public/Js/webScripts.js" : ['dfgviewer/Resources/Private/Javascript/modernizrCustom.js', 'dfgviewer/Resources/Private/Javascript/websiteScripts.js']
                }
            }
        },
        watch: {
            styles: {
                files: ['dfgviewer/Resources/Private/Less/**/*.less'],
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            },
            js: {
                files: ['dfgviewer/Resources/Private/Javascript/s*.js'],
                tasks: ['uglify']
            }
        }
    });
    grunt.registerTask('default', ['less','uglify','watch']);
};