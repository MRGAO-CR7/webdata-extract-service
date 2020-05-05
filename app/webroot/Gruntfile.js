// Gruntfile.js
module.exports = function(grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            prod: {
                options: {
                  mangle: false
                },
                files: [{
                    src: [
                        'js/libs/jquery-3.1.0.min.js',
                        'js/libs/jquery-ui.min.js',
                        'js/libs/timer.jquery.min.js',
                        'js/libs/bootstrap.min.js',
                        'js/libs/moment.min.js',
                        'js/libs/app.js'
                    ],
                    dest: 'js/libs.min.js'
                }]
            }
        },
        cssmin: {
            prod: {
                files: {
                    'css/dist/app.min.css' : ['css/*.css']
                }
            }
        },
        watch: {
            css: {
                files: ['css/*.css'],
                tasks: ['cssmin:prod'],
                options: {
                  event: ['added', 'deleted', 'changed']
                }
            },
            js: {
                files: ['js/plugins/*.js', 'js/libs/*.js'],
                tasks: ['uglify:prod'],
                options: {
                  event: ['added', 'deleted', 'changed']
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('prod', ['uglify:prod', 'cssmin:prod']);
};
