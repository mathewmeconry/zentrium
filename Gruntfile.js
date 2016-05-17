module.exports = function(grunt) {
  grunt.initConfig({
    'install-workspace-deps': {
      options: {
        modules: ['src/**/*Bundle/Resources'],
      },
    },
    hub: {
      all: {
        src: ['src/**/*Bundle/Resources/Gruntfile.js'],
      },
    },
  });

  grunt.loadNpmTasks('grunt-install-workspace-deps');
  grunt.loadNpmTasks('grunt-hub');

  grunt.registerTask('install', ['install-workspace-deps']);
  grunt.registerTask('default', ['hub:all:default']);
};
