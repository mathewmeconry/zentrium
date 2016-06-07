module.exports = function(grunt) {
  grunt.initConfig({
    config: {
      root: require('path').dirname(require('findup-sync')('composer.lock'))
    },
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
          'public/js/helpers.js',
          'public/js/layout.js',
          'public/js/form.js',
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
          'public/js/modernizr.min.js',
          '<%= config.root %>/vendor/willdurand/js-translation-bundle/Bazinga/Bundle/JsTranslationBundle/Resources/public/js/translator.min.js',
          '<%= config.root %>/web/js/translations/*/*.js',
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/admin-lte/bootstrap/js/bootstrap.min.js',
          'node_modules/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js',
          'node_modules/admin-lte/plugins/fastclick/fastclick.min.js',
          'node_modules/admin-lte/plugins/select2/select2.min.js',
          'node_modules/admin-lte/plugins/select2/i18n/de.js',
          'node_modules/admin-lte/dist/js/app.min.js',
          'node_modules/jquery-minicolors/jquery.minicolors.min.js',
          'node_modules/sortablejs/Sortable.min.js',
          'node_modules/sortablejs/jquery.binding.js',
          'node_modules/js-cookie/src/js.cookie.js',
          'public/js/bundle.js',
        ],
        dest: 'public/js/bundle.min.js'
      }
    },
    modernizr: {
      bundle: {
        crawl: false,
        dest: 'public/js/modernizr.min.js',
        tests: [
          'history'
        ],
        options: [],
        uglify: true
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
      },
      js: {
        files: ['public/js/**/*.js', '!public/js/bundle.js', '!public/js/bundle.min.js'],
        tasks: ['uglify', 'concat']
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-modernizr');

  grunt.registerTask('default', ['less', 'uglify', 'modernizr', 'concat', 'copy']);
};
