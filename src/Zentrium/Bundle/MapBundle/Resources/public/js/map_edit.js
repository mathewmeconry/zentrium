$(function () {
  $('#layers-available').each(function () {
    var $available = $(this);
    var layers = $available.data('layers');
    var $active = $('#layers-active');
    var $activeInput = $active.siblings().filter('input');
    var activeLayers = JSON.parse($activeInput.val());

    var renderedActiveLayers = {};
    for(var i in layers) {
      var position = activeLayers.indexOf(layers[i].id);
      var $layer = $('<li></li>').text(layers[i].name).data('id', layers[i].id);
      if(position >= 0) {
        renderedActiveLayers[position] = $layer;
      } else {
        $available.append($layer);
      }
    }

    for(i = 0; i < activeLayers.length; i++) {
      if(i in renderedActiveLayers) {
        $active.append(renderedActiveLayers[i]);
      }
    }

    $active.sortable({
      group: 'layers',
      onSort: function () {
        activeLayers = [];
        $active.find('li').each(function () {
          activeLayers.push($(this).data('id'));
        });
        $activeInput.val(JSON.stringify(activeLayers));
      }
    });

    $available.sortable({
      group: 'layers',
      sort: false
    });
  });
});
