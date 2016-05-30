var Zentrium = Zentrium || {};
Zentrium.Schedule = Zentrium.Schedule || {};

Zentrium.Schedule.pause = function (func, paused) {
  var lastArgs;
  var lastThis;
  var queue = false;

  function run() {
    if(!paused) {
      func.apply(this, arguments);
    } else {
      queue = true;
      lastArgs = arguments;
      lastThis = this;
    }
  }

  run.start = function () {
    paused = false;
    if(queue) {
      func.apply(lastThis, lastArgs);
      queue = false;
      lastArgs = undefined;
      lastThis = undefined;
    }
  };

  run.stop = function () {
    paused = true;
  };

  return run;
};
