import 'babel-polyfill';
import $ from 'jquery';
import runtime from 'serviceworker-webpack-plugin/lib/runtime';
import { post, Translator } from 'zentrium';

function supported() {
  return 'serviceWorker' in navigator && 'Notification' in window && 'PushManager' in window;
}

$(function () {
  const $settings = $('[data-oaf-push-settings]').first();
  if (!supported()) {
    $settings.hide();
    return;
  }

  const $btn = $settings.find('button');
  const settings = $settings.data('oaf-push-settings');
  let pushManager = null;
  let subscription = null;

  function update(subscription) {
    $btn.prop('disabled', false);
    if (subscription) {
      $btn.text(Translator.trans('vkaf_oaf.push.deactivate'));
    } else {
      $btn.text(Translator.trans('vkaf_oaf.push.activate'));
    }
  }

  runtime.register().then(async function (registration) {
    pushManager = registration.pushManager;
    subscription = await pushManager.getSubscription();
    update(subscription);
  });

  $btn.click(async function () {
    $btn.prop('disabled', true);
    if (subscription) {
      await subscription.unsubscribe();
      await post(settings.unsubscribe, {
        'push_unsubscribe[endpoint]': subscription.endpoint,
      });
      subscription = null;
    } else if (Notification.permission == 'granted' || await Notification.requestPermission() == 'granted') {
      subscription = await pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: base64ToUint8Array(settings.key),
      });
      await post(settings.subscribe, {
        'push_subscribe[endpoint]': subscription.endpoint,
        'push_subscribe[key]': arrayBufferToBase64(subscription.getKey('p256dh')),
        'push_subscribe[token]': arrayBufferToBase64(subscription.getKey('auth')),
      });
    }
    update(subscription);
  });
});

function base64ToUint8Array(base64) {
  const data = window.atob(base64);
  const result = new Uint8Array(data.length);
  for (let i = 0; i < data.length; i++) {
    result[i] = data.charCodeAt(i);
  }
  return result;
}

function arrayBufferToBase64(buffer) {
    let data = '';
    const bytes = new Uint8Array(buffer);
    for (let i = 0; i < bytes.byteLength; i++) {
        data += String.fromCharCode(bytes[i]);
    }
    return window.btoa(data);
}
