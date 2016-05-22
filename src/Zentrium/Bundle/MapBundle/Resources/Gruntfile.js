module.exports = function(grunt) {
  grunt.initConfig({
    uglify: {
      js: {
        src: [
          'public/js/projections.js',
          'public/js/map.js',
        ],
        dest: 'public/js/bundle.js'
      }
    },
    concat: {
      options: {
        separator: ';'
      },
      js: {
        src: [
          'node_modules/proj4/dist/proj4.js',
          'node_modules/openlayers/dist/ol-debug.js',
          'public/js/bundle.js',
        ],
        dest: 'public/js/bundle.min.js'
      }
    },
    copy: {
      openlayers: {
        files: [
          { expand: true, src: 'node_modules/openlayers/dist/ol-debug.css', dest: 'public/css/', flatten: true },
        ]
      }
    },
    watch: {
      js: {
        files: ['public/js/**/*.js', '!public/js/bundle.js', '!public/js/bundle.min.js'],
        tasks: ['uglify', 'concat']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['uglify', 'concat', 'copy']);
};
