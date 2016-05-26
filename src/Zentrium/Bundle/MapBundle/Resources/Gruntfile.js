module.exports = function(grunt) {
  grunt.initConfig({
    less: {
      bundle: {
        options: {
          compress: true
        },
        files: {
          'public/css/bundle.css': 'less/bundle.less'
        }
      }
    },
    uglify: {
      js: {
        src: [
          'public/js/projections.js',
          'public/js/map.js',
          'public/js/map_edit.js',
        ],
        dest: 'public/js/bundle.js'
      }
    },
    concat: {
      js: {
        options: {
          separator: ';'
        },
        src: [
          'node_modules/proj4/dist/proj4.js',
          'node_modules/openlayers/dist/ol-debug.js',
          'public/js/bundle.js',
        ],
        dest: 'public/js/bundle.min.js'
      },
      css: {
        src: [
          'node_modules/openlayers/dist/ol-debug.css',
          'public/css/bundle.css',
        ],
        dest: 'public/css/bundle.min.css'
      }
    },
    watch: {
      js: {
        files: ['public/js/**/*.js', '!public/js/bundle.js', '!public/js/bundle.min.js'],
        tasks: ['uglify', 'concat:js']
      },
      less: {
        files: ['less/**/*.less'],
        tasks: ['less', 'concat:css']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['less', 'uglify', 'concat']);
};
