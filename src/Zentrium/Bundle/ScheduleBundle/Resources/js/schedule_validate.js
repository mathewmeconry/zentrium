import $ from 'jquery';
import _ from 'underscore';
import URI from 'urijs';
import { request } from 'zentrium';

$(function () {
  const $results = $('#schedule-validate');
  if(!$results.length) {
    return;
  }

  const levels = [
    { level: 'critical', alert: 'danger', icon: 'warning'},
    { level: 'warning', alert: 'warning', icon: 'warning'},
    { level: 'info', alert: 'info', icon: 'info'},
  ];

  const config = $results.data('config');
  let active = (_.isArray(config.active) ? config.active : config.defaults.slice(0));
  let request = null;
  const historyUri = ('history' in window && 'pushState' in window.history) ? URI() : null;

  const $constraints = $('#schedule-validate-constraints li');
  const $loading = $results.find('.schedule-validate-loading');
  const $success = $results.find('.schedule-validate-success');
  const $save = $('#schedule-validate-save');
  const $reset = $('#schedule-validate-reset');
  const $list = $('<div></div>');
  $results.append($list);

  const fetchResults = _.debounce(function () {
    if(request) {
      request.abort();
    }
    request = $.getJSON(config.endpoint + '?constraints=' + active.join('+')).done(function (data) {
      if(data.length > 0) {
        const grouped = _.groupBy(data, 'level');
        $list.empty();
        for(const level of levels) {
          if(!(level.level in grouped)) {
            continue;
          }
          for(let message in grouped[level.level]) {
            const $message = $('<div class="alert schedule-validate-message"><div class="schedule-validate-icon"><i class="fa"></i></div><span class="schedule-validate-details"></span><span></span></div>');
            $message.addClass('alert-' + level.alert);
            $message.find('.fa').addClass('fa-' + level.icon);
            $message.find('span:not([class])').text(message.message);
            $message.find('.schedule-validate-details').text(message.constraintName);
            $list.append($message);
          }
        }
        $list.show();
      } else {
        $success.show();
      }
      $loading.hide();
    });
  }, 2000);

  const update = function (fetch) {
    $constraints.each(function () {
      const $this = $(this);
      $this.toggleClass('active', _.contains(active, $this.data('constraint-id')));
    });
    const isDefault = (active.length === config.defaults.length && _.difference(active, config.defaults).length === 0);
    $save.toggle(!isDefault && _.isString(config.defaultsEndpoint));
    $reset.toggle(!isDefault);
    if(historyUri) {
      window.history.replaceState({}, null, historyUri.query(isDefault ? '' : { constraints: active.join(' ') }));
    }
    if(fetch !== false) {
      $list.hide();
      $success.hide();
      $loading.show();
      fetchResults();
    }
  };

  $constraints.find('> a').click(function () {
    const id = $(this).closest('li').data('constraint-id');
    if(_.contains(active, id)) {
      active = _.without(active, id);
    } else {
      active.push(id);
    }
    update();
    return false;
  });

  $save.click(function () {
    const oldDefaults = config.defaults;
    request('PATCH', config.defaultsEndpoint, {
      defaults: active
    }).fail(function () {
      config.defaults = oldDefaults;
      update();
    });
    config.defaults = active.slice(0);
    update(false);
    return false;
  });

  $reset.click(function () {
    active = config.defaults.slice(0);
    update();
    return false;
  });

  update();
});
