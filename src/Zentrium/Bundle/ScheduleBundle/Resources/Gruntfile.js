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
          'public/js/utils.js',
          'public/js/schedule_view.js',
          'public/js/set_view.js',
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
          'node_modules/fullcalendar/node_modules/moment/min/moment.min.js',
          'node_modules/fullcalendar/node_modules/moment/locale/de.js',
          'node_modules/fullcalendar/dist/fullcalendar.min.js',
          'node_modules/fullcalendar/dist/lang/de.js',
          'node_modules/fullcalendar-scheduler/dist/scheduler.min.js',
          'node_modules/crosstab/src/crosstab.js',
          'public/js/bundle.js',
        ],
        dest: 'public/js/bundle.min.js'
      },
      css: {
        src: [
          'node_modules/fullcalendar/dist/fullcalendar.min.css',
          'node_modules/fullcalendar-scheduler/dist/scheduler.min.css',
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
