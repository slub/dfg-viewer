
module.exports = function(grunt) {
    require('jit-grunt')(grunt);

    grunt.loadNpmTasks('grunt-webfont');
    grunt.initConfig({
        webfont: {
            icons: {
                src: "Resources/Public/Images/Icons/*.svg",
                dest: "Resources/Public/Fonts/IconFont/",
                destCss: "Resources/Private/Less",
                options: {
                    stylesheet: "less",
                    engine: "node",
                    syntax: "bootstrap",
                    htmlDemo: false,
                    font: "iconfont"
                }
            }
        },
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
                    "Resources/Public/Js/allScripts.js" : ['Resources/Private/Javascript/modernizrCustom.js', 'Resources/Private/Javascript/js.cookie.js', 'Resources/Private/Javascript/dfgviewerScripts.js', 'Resources/Private/Javascript/mediaplayerScripts.js'],
                    "Resources/Public/Js/webScripts.js" : ['Resources/Private/Javascript/modernizrCustom.js', 'Resources/Private/Javascript/websiteScripts.js']
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
                tasks: ['uglify'],
                options: {
                    nospawn: true
                }
            }
        }
    });

    grunt.file.setBase('../')
    grunt.registerTask('default', ['webfont','less','uglify','watch']);
};
