module.exports = function(grunt) {
    grunt.initConfig({
        cssmin: {
            combine: {
                dest: 'css/app.min.css',
                src: [
                    'node_modules/ui-select/dist/select.css',
                    'appdev/css/animate.css',
                    'appdev/css/main.css',
                ]
            }
        },
        requirejs: {
            compile: {
                options: {
                    almond: true,
                    baseUrl: ".",
                    out: 'js/app.min.js',
                    name: 'main',
                    mainConfigFile: 'main.js',
                    include: ['node_modules/requirejs/require'],
                    preserveLicenseComments: false
                }
            }
        },
        jshint: {
            all: ['Gruntfile.js', 'appdev/js/**/*.js', 'main.js']
        }    
    });

    //Cargamos las tareas
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    //Registramos las tareas
    grunt.registerTask('minify', ['cssmin']);
    grunt.registerTask('jscheck', ['jshint']);
    grunt.registerTask('jsapp', ['jshint', 'requirejs']);
};