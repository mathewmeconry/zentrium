$(function () {
  var $wrapper = $('.kiosk-schedule').first();
  var $left = $wrapper.find('.kiosk-schedule-left');
  var $right = $wrapper.find('.kiosk-schedule-right');
  if(!$wrapper.length || !$left.length || !$right.length) {
    return;
  }

  var config = $wrapper.data('config');
  console.log(config);

  function setupSchedule(config, users, $view) {
    var options = {
      events: config.shifts,
      resources: users,
      resourceColumns: [
        {
          labelText: Translator.trans('zentrium_schedule.user.field.name'),
          text: function (user) {
            return user.name;
          }
        }
      ]
    };

    Zentrium.Schedule.setup($view, config, options);
  }

  setupSchedule(config, config.userPartitions[0], $left);
  setupSchedule(config, config.userPartitions[1], $right);
});
