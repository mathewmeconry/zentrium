import $ from 'jquery';
import _ from 'underscore';
import ReconnectingWebSocket from 'reconnecting-websocket';
import SignaturePad from 'signature_pad';
import { Translator } from 'zentrium';

function render($container, title, buttons) {
  $container.empty();
  if (title) {
    const $header = $('<div class="terminal-header"><h1></h1></div>');
    $header.find('h1').text(title);
    $container.append($header);
  }
  const $body = $('<div class="terminal-body"></div>');
  $container.append($body);
  if (buttons) {
    const $footer = $('<div class="terminal-footer"></div>');
    for (let i = 0; i < buttons.length; i++) {
      const $btn = $('<button class="btn">').text(buttons[i][0]).click(buttons[i][1]);
      if (i == buttons.length - 1) {
        $btn.addClass('btn-primary').addClass('pull-right');
      } else {
        $btn.addClass('btn-default');
      }
      $footer.append($btn);
    }
    $container.append($footer);
  }
  return $body;
}

class SignatureFlow {
  constructor($container, params) {
    this.$container = $container;
    this.description = params;
  }

  run() {
    const $description = $('<dl>');
    for (let row of this.description) {
      $description.append($('<dt>').text(row[0]));
      $description.append($('<dd>').text(row[1]));
    }
    render(this.$container, Translator.trans('vkaf_oaf.terminal.signature'), [
      [Translator.trans('vkaf_oaf.terminal.cancel'), () => { this.cancel() }],
      [Translator.trans('vkaf_oaf.terminal.sign'), () => { this.sign() }],
    ]).append($description);
    return new Promise((resolve, reject) => {
      this.resolve = resolve;
    });
  }

  sign() {
    const $body = render(this.$container, Translator.trans('vkaf_oaf.terminal.signature'), [
      [Translator.trans('vkaf_oaf.terminal.cancel'), () => { this.cancel() }],
      [Translator.trans('vkaf_oaf.terminal.reset'), () => { this.reset() }],
      [Translator.trans('vkaf_oaf.terminal.confirm'), () => { this.confirm() }],
    ]);
    const canvas = document.createElement('canvas');
    $body.addClass('terminal-signature');
    $body.append(canvas);
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
    this.pad = new SignaturePad(canvas, {
      dotSize: 2.0,
      minWidth: 1.0,
      maxWidth: 2.0,
    });
  }

  reset() {
    this.pad.clear();
  }

  confirm() {
    if (this.pad.isEmpty()) {
      return;
    }
    const strokes = [];
    for (let data of this.pad.toData()) {
      let stroke = [];
      let startTime = data[0].time;
      let lastTime = data[0].time;
      for (let point of data) {
        if (point.time < lastTime) {
          continue;
        }
        stroke.push([point.time - startTime, point.x, point.y]);
        lastTime = point.time;
      }
      strokes.push(stroke);
    }
    this.resolve(strokes);
  }

  cancel() {
    if (this.resolve) {
      this.resolve(null);
      this.resolve = null;
    }
  }
}

const flows = {
  'signature': SignatureFlow,
};

$(function () {
  const $container = $('.terminal-wrapper[data-endpoint]');
  if (!$container.length) {
    return;
  }

  function showSplash() {
    const $body = render($container, null, null);
    $body.addClass('terminal-splash');
    $body.append($('<img>').attr('src', require('../images/splash.png')));
  }
  showSplash();

  const socket = new ReconnectingWebSocket($container.data('endpoint'));
  let flow = null;
  socket.addEventListener('message', event => {
    const data = JSON.parse(event.data);
    if ('flow' in data) {
      if (flow) {
        flow.cancel();
      }
      const self = flow = new flows[data.flow.name]($container, data.flow.params);
      flow.run().then(result => {
        if (self === flow) {
          flow = null;
          showSplash();
        }
        socket.send(JSON.stringify({
          flow: {
            token: data.flow.token,
            data: result,
          }
        }));
      });
    }
  });
});

$(function () {
  const $cells = $('[data-oaf-terminal-status]');
  if (!$cells.length) {
      return;
  }
  const socket = new ReconnectingWebSocket($cells.closest('[data-endpoint]').data('endpoint'));
  socket.addEventListener('message', event => {
    const terminals = _.indexBy(JSON.parse(event.data).terminals, 'id');
    $cells.each(function () {
      const id = $(this).data('oaf-terminal-status');
      $(this).text(Translator.trans(terminals[id].online ? 'vkaf_oaf.terminal.online' : 'vkaf_oaf.terminal.offline'));
    });
  });
});
