import $ from 'jquery';
import _ from 'underscore';
import ReconnectingWebSocket from 'reconnecting-websocket';
import { Translator } from 'zentrium';

$(function () {
  const $container = $('[data-oaf-timesheet-approve]');
  if (!$container.length) {
    return;
  }
  const options = $container.data('oaf-timesheet-approve');
  let terminalUpdates = true;

  const socket = new ReconnectingWebSocket(options.endpoint);
  socket.addEventListener('message', event => {
    const data = JSON.parse(event.data);
    if ('terminals' in data && terminalUpdates) {
      const terminals = _.filter(data.terminals, 'online');
      $container.empty();
      if (terminals.length) {
        for (let terminal of _.sortBy(data.terminals, 'label')) {
          $container.append($('<button class="btn btn-block btn-default">').data('terminal', terminal.id).text(terminal.label).click(selectTerminal));
        }
      } else {
        $container.append($('<p>').text(Translator.trans('vkaf_oaf.timesheet.no_terminals')));
      }
    } else {
      terminalUpdates = false;
    }
    if ('success' in data) {
      $container.html('<p class="timesheet-approve-indicator text-green"><i class="fa fa-check"></i></p>');
      $container.append(buildContinue());
    } else if ('failure' in data) {
      $container.html('<p class="timesheet-approve-indicator text-red"><i class="fa fa-times"></i><span></span></p>');
      if (data.failure) {
        $container.find('span').text(Translator.trans(data.failure));
      }
      $container.append(buildContinue());
    }
  });

  function selectTerminal() {
    const id = $(this).data('terminal');
    terminalUpdates = false;
    $container.html('<p class="timesheet-approve-indicator"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></p>');
    socket.send(JSON.stringify({ terminal: id }));
  }

  function buildContinue() {
    return $('<a class="btn btn-default btn-block">').attr('href', options.return).text(Translator.trans('vkaf_oaf.timesheet.continue'));
  }
});
