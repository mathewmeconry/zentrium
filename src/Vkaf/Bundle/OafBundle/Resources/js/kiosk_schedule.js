import $ from 'jquery';
import { Translator } from 'zentrium';
import { setup } from 'zentrium-schedule-bundle/js/utils';

function setupSchedule(config, users, $view) {
  const options = {
    events: config.shifts,
    resources: users,
    resourceColumns: [
      {
        labelText: Translator.trans('zentrium_schedule.user.field.name'),
        text: user => user.name,
      }
    ]
  };

  setup($view, config, options);
}

$(function () {
  const $wrapper = $('.kiosk-schedule').first();
  const $left = $wrapper.find('.kiosk-schedule-left');
  const $right = $wrapper.find('.kiosk-schedule-right');
  if(!$wrapper.length || !$left.length || !$right.length) {
    return;
  }

  const config = $wrapper.data('config');

  setupSchedule(config, config.userPartitions[0], $left);
  setupSchedule(config, config.userPartitions[1], $right);
});
