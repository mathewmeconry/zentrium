import $ from 'jquery';
import Sortable from 'sortablejs';

$(function () {
  $('#layers-available').each(function () {
    const $available = $(this);
    const layers = $available.data('layers');
    const $active = $('#layers-active');
    const $activeInput = $active.siblings().filter('input');
    let activeLayers = JSON.parse($activeInput.val());

    const renderedActiveLayers = {};
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

    new Sortable($active.get(0), {
      group: 'layers',
      onSort: function () {
        activeLayers = [];
        $active.find('li').each(function () {
          activeLayers.push($(this).data('id'));
        });
        $activeInput.val(JSON.stringify(activeLayers));
      }
    });

    new Sortable($available.get(0), {
      group: 'layers',
      sort: false
    });
  });
});
