import $ from 'jquery';
import _ from 'underscore';
import Chart from 'chart.js';
import { Translator } from 'zentrium';
import { setup } from 'zentrium-schedule-bundle/js/utils';

$(function () {
  const $schedule = $('#oaf-user-schedule');
  const config = $schedule.data('config');
  if(!_.isObject(config)) {
    return;
  }

  const scheduleOptions = {
    defaultView: 'agendaMultiDay',
    defaultDate: config.begin,
    views: {
      agendaMultiDay: {
        type: 'agenda',
        duration: { days: config.dayCount },
        allDaySlot: false,
        slotDuration: { seconds: config.slotDuration },
      }
    },
    eventSources: [
      config.shifts,
      config.availabilities,
    ],
    eventAfterRender: function(event, element, view) {
      const buttons = '<a href="' + event.timesheet + '" class="btn btn-default"><i class="fa fa-clock-o"></i></a>';
      $(element).popover({
        placement: 'top',
        trigger: 'manual',
        html: true,
        container: 'body',
        content: '<div class="btn-group less-padding">' + buttons + '</div>',
      });
    },
    eventClick: function(calEvent, jsEvent, view) {
      $schedule.find('.fc-event').not(this).popover('hide');
      $(this).popover('toggle');
    },
    dayClick: function(date, jsEvent, view) {
      $schedule.find('.fc-event').popover('hide');
    },
  };

  setup($schedule, {}, scheduleOptions);
});

$(function () {
  const $container = $('.schedule-statistics-slots');
  if (!$container.length) {
    return;
  }

  const data = $container.data('data');
  const canvas = document.createElement('canvas');
  $container.append($('<div>').append(canvas));
  const chart = new Chart(canvas, {
    type: 'bar',
    options: {
      animation: false,
      legend: false,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            stacked: true,
            suggestedMin: 0,
            suggestedMax: 10,
          },
        }],
      },
      hover: {
        intersect: false,
      },
      tooltips: {
        intersect: false,
        mode: 'index',
        titleFontFamily: '"Source Sans Pro", "Helvetica Neue", Helvetica, Arial, sans-serif',
        bodyFontFamily: '"Source Sans Pro", "Helvetica Neue", Helvetica, Arial, sans-serif',
        displayColors: false,
        itemSort: function (a, b) {
          return b.datasetIndex - a.datasetIndex;
        },
        callbacks: {
          label: function (item, data) {
            return data.datasets[item.datasetIndex].label + ': ' + Math.abs(item.yLabel);
          },
        },
      },
    },
    data: {
      labels: data.labels,
      datasets: [{
        label: Translator.trans('vkaf_oaf.schedule.statistics.ending'),
        data: data.ending.map(x => -x),
        stack: 'total',
        backgroundColor: 'rgb(221, 75, 57)',
      }, {
        label: Translator.trans('vkaf_oaf.schedule.statistics.staying'),
        data: data.staying,
        stack: 'total',
        backgroundColor: 'rgb(60, 141, 188)'
      }, {
        label: Translator.trans('vkaf_oaf.schedule.statistics.changing'),
        data: data.changing,
        stack: 'total',
        backgroundColor: 'rgb(243, 156, 18)',
      }, {
        label: Translator.trans('vkaf_oaf.schedule.statistics.beginning'),
        data: data.beginning,
        stack: 'total',
        backgroundColor: 'rgb(0, 166, 90)',
      }],
    },
  });
});
