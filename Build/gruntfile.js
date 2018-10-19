
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
                    "Resources/Public/Css/allStyles.css" : "Resources/Private/Less/all.less",
                    "Resources/Public/Css/webStyles.css" : "Resources/Private/Less/website.less",
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
                    "Resources/Public/Js/allScripts.js" : ['Resources/Private/Javascript/modernizrCustom.js', 'Resources/Private/Javascript/js.cookie.js', 'Resources/Private/Javascript/dfgviewerScripts.js'],
                    "Resources/Public/Js/webScripts.js" : ['Resources/Private/Javascript/websiteScripts.js']
                }
            }
        },
        watch: {
            styles: {
                files: ['Resources/Private/Less/**/*.less'],
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            },
            js: {
                files: ['Resources/Private/Javascript/*.js'],
                tasks: ['uglify']
            }
        }
    });
    grunt.registerTask('default', ['less','uglify','watch']);
};
