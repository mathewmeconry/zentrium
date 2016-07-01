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
          'public/js/kiosk.js',
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
          'public/js/bundle.js',
        ],
        dest: 'public/js/bundle.min.js'
      },
      css: {
        src: [
          'public/css/bundle.css'
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
