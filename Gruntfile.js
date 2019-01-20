module.exports = function (grunt) {

    var configBridge = grunt.file.readJSON('node_modules/bootstrap/grunt/configBridge.json', {encoding: 'utf8'});

    var filesToCompress = [
        'assets/**',
        'source/**',
        'twig-templates/**',

        'vendor/**',
        '!vendor/**/Test[s]*/**',
        '!vendor/**/test[s]*/**',
        // vendor/twig/twig folder have strange test folder name
        '!vendor/**/test/**',
        '!bin/ci_build',
        '!bin/slack_notify.sh',

        'composer.json',
        'composer.lock',
        'license.txt',
        'phpcs.ruleset.xml',
        'plugin.php',
        'readme.txt'
    ];

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        less: {
            frontend: {
                options: {
                    strictMath: true,
                    sourceMap: true,
                    outputSourceFiles: true,
                    sourceMapURL: 'frontend.css.map',
                    sourceMapFilename: 'assets/styles/frontend/frontend.css.map'
                },
                src: 'assets/styles/frontend/main.less',
                dest: 'assets/styles/frontend/frontend.css'
            },
        },

        autoprefixer: {
            options: {
                browsers: configBridge.config.autoprefixerBrowsers
            },
            frontend: {
                options: {
                    map: true
                },
                src: 'assets/styles/frontend/frontend.css'
            },
        },

        copy: {
            libs: {
                files: [
                    {
                        expand: true,
                        cwd: 'bower_components/flags/flags/flags-iso/',
                        src: '**',
                        dest: 'plugin/libs/flags/flags-iso/'
                    }
                ]
            },
        },

        compress: {
            plugin: {
                options: {
                    archive: 'dist/<%= pkg.name %>.zip'
                },
                files: [
                    {
                        expand: true,
                        src: filesToCompress,
                        dest: '<%= pkg.name %>/',
                        dot: false
                    }
                ]
            }
        }
    });

    require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});

    grunt.registerTask('stylesFrontend', [
        'less:frontend',
        'autoprefixer:frontend'
    ]);

    grunt.registerTask('default', [
        'stylesFrontend',
    ]);
};