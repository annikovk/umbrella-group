/**
 * UTM-атрибуция заявок: источник и канал визита → скрытые поля omatraffic и
 * omacid в формах CF7 (письма берут их как обычные поля формы).
 *
 * Логика источника — та же, что была в PHP (inc/attribution.php до WS-1):
 * utm_* > yclid > gclid > рекламный реферер yabs.yandex > чужой реферер
 * (поисковик → organic, market.yandex → cpc, прочее → referral) > прямой заход.
 * Неважный визит (прямой, переход внутри сайта) не затирает сохранённый
 * источник. Источник живёт в cookie ug_src (JSON) 30 дней и продлевается на
 * каждой странице.
 *
 * На сервере атрибуция больше не считается: HTML одинаков для всех и
 * кэшируется, а сохранённое значение никогда не попадает в разметку.
 */
(function () {
  'use strict';

  var COOKIE = 'ug_src';
  var MAX_AGE = 3600 * 24 * 30;
  var SEARCH = ['google', 'yandex', 'mail.ru', 'rambler', 'bing'];

  function readCookie(name) {
    var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    if (!m) return undefined;
    try { return decodeURIComponent(m[1]); } catch (e) { return undefined; }
  }

  // Домен без первой метки, как в старом PHP: www.google.com → google.com,
  // yandex.ru → yandex.ru, a.b.c.d → b.c.d.
  function baseHost(host) {
    var m = /\.(.*\.\w*)/.exec(host);
    return m ? m[1] : host;
  }

  function hostOf(url) {
    var m = /^(?:https?:\/\/)?(.*?)(?::|\/|$)/.exec(url);
    return m ? m[1] : '';
  }

  function detect() {
    var q = new URLSearchParams(location.search);
    var ref = document.referrer || '';
    var src = { medium: null, source: null, campaign: null, term: null, content: null };
    var important = false;

    if (q.has('utm_medium')) {
      important = true;
      src.medium = q.get('utm_medium');
      src.source = q.get('utm_source');
      src.campaign = q.get('utm_campaign');
      src.term = q.get('utm_term');
      src.content = q.get('utm_content');
    } else if (q.has('yclid')) {
      important = true;
      src.medium = 'cpc'; src.source = 'yandex';
    } else if (q.has('gclid')) {
      important = true;
      src.medium = 'cpc'; src.source = 'google';
    } else if (ref.indexOf('yabs.yandex') > -1) {
      important = true;
      src.medium = 'cpc'; src.source = 'yandex';
    } else if (ref) {
      var referrer = baseHost(hostOf(ref));
      var host = baseHost(hostOf(location.host));
      if (host !== referrer) {
        important = true;
        src.source = referrer;
        if (referrer.indexOf('market.yandex') > -1) {
          src.medium = 'cpc';
        } else {
          src.medium = 'referral';
          for (var i = 0; i < SEARCH.length; i++) {
            if (referrer.indexOf(SEARCH[i]) > -1) {
              src.medium = 'organic';
              src.source = SEARCH[i];
              break;
            }
          }
        }
      } else {
        src.source = 'none'; src.medium = 'direct';
      }
    } else {
      src.source = 'none'; src.medium = 'direct';
    }

    if (!important) {
      var saved = null;
      try { saved = JSON.parse(readCookie(COOKIE) || 'null'); } catch (e) { saved = null; }
      if (saved && typeof saved === 'object' && saved.medium) src = saved;
    }
    return src;
  }

  var src = detect();
  document.cookie = COOKIE + '=' + encodeURIComponent(JSON.stringify(src)) +
    '; max-age=' + MAX_AGE + '; path=/; SameSite=Lax';

  // Пустая строка и «0» не пишутся — как в PHP, где проверка была по truthy.
  function has(v) { return v !== null && v !== undefined && v !== '' && v !== '0'; }

  var traffic = 'Источник - ' + (src.source || '') + ', Канал - ' + (src.medium || '');
  if (has(src.campaign)) traffic += ', Кампания - ' + src.campaign;
  if (has(src.term)) traffic += ', Объявление - ' + src.term;
  if (has(src.content)) traffic += ', Ключевое слово - ' + src.content;

  var values = { omatraffic: traffic, omacid: readCookie('_gaexp') || '' };

  // defaultValue — это атрибут value: после отправки CF7 делает form.reset(),
  // и поле не обнуляется.
  function fill(root) {
    for (var name in values) {
      var inputs = root.querySelectorAll('input[name="' + name + '"]');
      for (var i = 0; i < inputs.length; i++) inputs[i].defaultValue = inputs[i].value = values[name];
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { fill(document); });
  } else {
    fill(document);
  }
  // Формы, вставленные позже (попапы, wpcf7.init на новой разметке), —
  // в момент отправки: захват на document срабатывает раньше обработчика CF7
  // на самой форме, который собирает FormData. _gaexp к этому времени мог
  // появиться — перечитываем.
  document.addEventListener('submit', function (e) {
    values.omacid = readCookie('_gaexp') || '';
    if (e.target && e.target.querySelectorAll) fill(e.target);
  }, true);
})();
