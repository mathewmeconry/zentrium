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
    concat: {
      options: {
        separator: ';'
      },
      js: {
        src: [
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/admin-lte/bootstrap/js/bootstrap.min.js',
          'node_modules/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js',
          'node_modules/admin-lte/plugins/fastclick/fastclick.js',
          'node_modules/admin-lte/dist/js/app.min.js'
        ],
        dest: 'public/js/bundle.min.js'
      }
    },
    copy: {
      fonts: {
        files: [
          { expand: true, src: 'node_modules/font-awesome/fonts/fontawesome-*', dest: 'public/fonts/', flatten: true },
          { expand: true, src: 'node_modules/source-sans-pro/*/**', dest: 'public/fonts/', filter: 'isFile', flatten: true }
        ]
      }
    },
    watch: {
      less: {
        files: ['less/**/*.less'],
        tasks: ['less']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['less', 'concat', 'copy']);
};
