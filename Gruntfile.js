module.exports = function(grunt) {
  grunt.initConfig({
    hub: {
      all: {
        src: ['src/**/*Bundle/Resources/Gruntfile.js'],
      },
    },
  });
  grunt.loadNpmTasks('grunt-hub');
  grunt.registerTask('default', ['hub:all:default']);
};
