$(function () {
  var $results = $('#schedule-validate');
  if(!$results.length) {
    return;
  }

  var levels = [
    { level: 'critical', alert: 'danger', icon: 'warning'},
    { level: 'warning', alert: 'warning', icon: 'warning'},
    { level: 'info', alert: 'info', icon: 'info'},
  ];

  var config = $results.data('config');
  var active = (_.isArray(config.active) ? config.active : config.defaults.slice(0));
  var request = null;
  var uri = (Modernizr.history ? URI() : null);

  var $constraints = $('#schedule-validate-constraints li');
  var $loading = $results.find('.schedule-validate-loading');
  var $success = $results.find('.schedule-validate-success');
  var $save = $('#schedule-validate-save');
  var $reset = $('#schedule-validate-reset');
  var $list = $('<div></div>');
  $results.append($list);

  var fetchResults = _.debounce(function () {
    if(request) {
      request.abort();
    }
    request = $.getJSON(config.endpoint + '?constraints=' + active.join('+')).done(function (data) {
      if(data.length > 0) {
        var grouped = _.groupBy(data, 'level');
        $list.empty();
        for(var i in levels) {
          var level = levels[i];
          if(!(level.level in grouped)) {
            continue;
          }
          for(var j in grouped[level.level]) {
            var message = grouped[level.level][j];
            var $message = $('<div class="alert schedule-validate-message"><div class="schedule-validate-icon"><i class="fa"></i></div><span class="schedule-validate-details"></span><span></span></div>');
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

  var update = function (fetch) {
    $constraints.each(function () {
      var $this = $(this);
      $this.toggleClass('active', _.contains(active, $this.data('constraint-id')));
    });
    var isDefault = (active.length === config.defaults.length && _.difference(active, config.defaults).length === 0);
    $save.toggle(!isDefault && _.isString(config.defaultsEndpoint));
    $reset.toggle(!isDefault);
    if(uri) {
      window.history.replaceState({}, null, uri.query(isDefault ? '' : { constraints: active.join(' ') }));
    }
    if(fetch !== false) {
      $list.hide();
      $success.hide();
      $loading.show();
      fetchResults();
    }
  };

  $constraints.find('> a').click(function () {
    var id = $(this).closest('li').data('constraint-id');
    if(_.contains(active, id)) {
      active = _.without(active, id);
    } else {
      active.push(id);
    }
    update();
    return false;
  });

  $save.click(function () {
    var oldDefaults = config.defaults;
    Zentrium.request('PATCH', config.defaultsEndpoint, {
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
