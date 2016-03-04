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
                    'js/bootstrap.js': 'bootstrap/dist/js/bootstrap.js',
                    'js/underscore.js': 'underscore/underscore.js',
                    'js/sha1.js': 'cryptojslib/rollups/sha1.js',
                    'js/sha512.js': 'cryptojslib/rollups/sha512.js',
                    'js/md5.js': 'cryptojslib/rollups/md5.js',
                    'js/enc-base64.js': 'cryptojslib/components/enc-base64.js',
                    'js/angular.js': 'angular/angular.js',
                    'js/angular-cookies.js': 'angular-cookies/angular-cookies.js',
                    'js/angular-resource.js': 'angular-resource/angular-resource.js',
                    'js/angular-route.js': 'angular-route/angular-route.js',
                    'js/angular-local-storage.js': 'angular-local-storage/dist/angular-local-storage.js',
                    'js/angular-cache.js': 'angular-cache/dist/angular-cache.js'
                }
            },
            stylesheets: {
                files: {
                    'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
                    'css/bootstrap-theme.css': 'bootstrap/dist/css/bootstrap-theme.css',
                    'css/angular-csp.css': 'angular/angular-csp.css'
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
                    'web/assets/css/bootstrap-theme.css',
                    'web/assets/css/angular-csp.css',
                    'src/AppBundle/Resources/css/*.css'
                ],
                dest: 'web/assets/css/bundled.css'
            },
            js : {
                src : [
                    'web/assets/js/jquery.js',
                    'web/assets/js/underscore.js',
                    'web/assets/js/sha1.js',
                    'web/assets/js/sha512.js',
                    'web/assets/js/md5.js',
                    'web/assets/js/enc-base64.js',
                    'web/assets/js/angular.js',
                    'web/assets/js/angular-cookies.js',
                    'web/assets/js/angular-cache.js',
                    'web/assets/js/angular-local-storage.js',
                    'web/assets/js/angular-resource.js',
                    'web/assets/js/angular-route.js',
                    'src/AppBundle/Resources/js/*.js',
                    'src/AppBundle/Resources/js/*/*.js',
                    'src/AppBundle/Resources/js/*/services/*.js',
                    'src/AppBundle/Resources/js/*/configs/*.js',
                    'src/AppBundle/Resources/js/*/controller/*.js',
                    'src/AppBundle/Resources/js/*/global/*.js',
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
            glyphicons: {
                expand: true,
                cwd: 'bower_components/bootstrap/dist/fonts/*',
                src: '*',
                dest: 'web/assets/fonts/'
            }
        },
        watch: {
          scripts: {
            files: [
              'src/AppBundle/Resources/css/*.css',
              'src/AppBundle/Resources/js/*.js',
              'src/AppBundle/Resources/js/*/*.js',
              'src/AppBundle/Resources/js/*/configs/*.js',
              'src/AppBundle/Resources/js/*/controller/*.js',
              'src/AppBundle/Resources/js/*/global/*.js',
              'src/AppBundle/Resources/js/*/services/*.js',
            ],
            tasks: ['watching'],
            options: {
              spawn: false,
            },
          },
        },
    });

    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['bowercopy','copy', 'concat', 'cssmin', 'uglify']);
    grunt.registerTask('watching', ['bowercopy','copy', 'concat']);
};
