module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        bowercopy: {
            options: {
                srcPrefix: 'bower_components',
                destPrefix: 'web/assets'
            },
            scripts: {
                files: {
                    'js/jquery.js': 'jquery/dist/jquery.js',
                    "js/jquery-ui.js": "jquery-ui/jquery-ui.js",
                    "js/jquery.mentions.js": "jquery-mentions/jquery.mentions.js",
                    'js/bootstrap.js': 'bootstrap/dist/js/bootstrap.js',
                    'js/bootstrap-confirmation.js': 'bootstrap-confirmation2/bootstrap-confirmation.js',
                    'js/raphael.js': 'raphael/raphael.js',
                    'js/justgage.js': 'justgage-toorshia/justgage.js',
                    'js/Chart.js': 'Chart.js/Chart.js',
                    "js/jquery.ui.widget.js": "blueimp-file-upload/js/vendor/jquery.ui.widget.js",
                    "js/jquery.iframe-transport.js": "blueimp-file-upload/js/jquery.iframe-transport.js",
                    "js/jquery.fileupload.js": "blueimp-file-upload/js/jquery.fileupload.js",
                    'js/autosize.js': 'autosize/dist/autosize.js'
                }
            },
            stylesheets: {
                files: {
                    'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
                    'css/font-awesome.css': 'font-awesome/css/font-awesome.css',
                    "css/jquery.fileupload.css": "blueimp-file-upload/css/jquery.fileupload.css"
                }
            },
            fonts: {
                files: {
                    'fonts': 'font-awesome/fonts'
                }
            }
        },
        cssmin : {
            bundled:{
                src: 'web/assets/css/bundled.css',
                dest: 'web/assets/css/bundled.min.css'
            }
        },
        uglify : {
            js: {
                files: {
                    'web/assets/js/bundled.min.js': ['web/assets/js/bundled.js']
                }
            }
        },
        concat: {
            options: {
                stripBanners: true
            },
            css: {
                src: [
                    'web/assets/css/bootstrap.css',
                    'web/assets/css/font-awesome.css',
                    "web/assets/css/jquery.fileupload.css",
                    'src/AppBundle/Resources/css/*.css'
                ],
                dest: 'web/assets/css/bundled.css'
            },
            js : {
                src : [
                    'web/assets/js/jquery.js',
                    "web/assets/js/jquery-ui.js",
                    "web/assets/js/jquery.mentions.js",
                    'web/assets/js/bootstrap.js',
                    'web/assets/js/bootstrap-confirmation.js',
                    'web/assets/js/raphael.js',
                    'web/assets/js/justgage.js',
                    'web/assets/js/chart.js',
                    "web/assets/js/jquery.ui.widget.js",
                    "web/assets/js/jquery.iframe-transport.js",
                    "web/assets/js/jquery.fileupload.js",
                    "web/assets/js/autosize.js",
                    'src/AppBundle/Resources/js/*.js'
                ],
                dest: 'web/assets/js/bundled.js'
            }
        },
        copy: {
            images: {
                expand: true,
                cwd: 'src/AppBundle/Resources/images',
                src: '*',
                dest: 'web/assets/images/'
            },
            fonts: {
                expand: true,
                cwd: 'src/AppBundle/Resources/fonts',
                src: '*',
                dest: 'web/assets/fonts/'
            }
        }
    });

    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['bowercopy','copy', 'concat', 'cssmin', 'uglify']);
};