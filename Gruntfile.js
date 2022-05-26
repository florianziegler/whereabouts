module.exports = function( grunt ) {

	const sass = require( 'sass' );

	grunt.initConfig({

		pkg: grunt.file.readJSON( 'package.json' ),

		sass: {
			options: {
				implementation: sass,
				outputStyle: 'compressed',
				sourceMap: true
			},
			build: {
				files: {
					'css/whereabouts-admin.css': 'scss/whereabouts-admin.scss'
				}
			}
		},

        autoprefixer: {
            options: {
                browsers: ['last 2 versions', 'ie 9', 'ios 6', 'android 4']
            },
            style: {
                src: 'css/*.css'
            }
        },

        rsync: {
            options: {
                args: ["--verbose"],
                exclude: [".DS_Store",".git*",".sass-cache","assets","whereabouts.css","main.js","node_modules","scss","svg","Gruntfile.js","package.json","README.md"],
                recursive: true
            },
            dist: {
                options: {
                    src: './',
                    dest: '../whereabouts-deploy',
                    delete: true
                }
            }
        },

		watch: {
			options: {
				livereload: true,
			},
			php: {
				files: ['*.php'],
				files: ['*/*.php']
			},
			scripts: {
				files: ['js/*.js'],
				tasks: ['uglify'],
				options: {
					spawn: false,
				},
			},
			css: {
				files: ['scss/**/*.scss'],
				tasks: ['sass', 'autoprefixer'],
				options: {
					spawn: false,
				}
			},
            svg: {
                files: ['svg/*.svg'],
                tasks: ['svgstore']
            }
		}

	});

	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-sass' );
    grunt.loadNpmTasks( 'grunt-autoprefixer' );
	grunt.loadNpmTasks( 'grunt-svgstore' );
	grunt.loadNpmTasks( 'grunt-rsync' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	grunt.registerTask( 'default', ['watch'] );
    grunt.registerTask( 'build', ['rsync'] );

};