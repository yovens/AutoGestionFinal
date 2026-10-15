/* AutoGestion — ti entèraksyon pou tout aplikasyon an. Pa gen depandans. */
(function () {
  'use strict';

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

  /* ---------- Tèm (klè / fonse / sistèm) ---------- */
  function storedTheme() {
    try { return localStorage.getItem('ag-theme') || 'system'; } catch (e) { return 'system'; }
  }
  function applyTheme(mode) {
    if (mode === 'light' || mode === 'dark') document.documentElement.setAttribute('data-theme', mode);
    else document.documentElement.removeAttribute('data-theme');
    $$('[data-theme-set]').forEach(function (b) { b.setAttribute('aria-pressed', String(b.dataset.themeSet === mode)); });
  }
  applyTheme(storedTheme());
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-theme-set]');
    if (!b) return;
    try { localStorage.setItem('ag-theme', b.dataset.themeSet); } catch (err) { /* navigatè prive */ }
    applyTheme(b.dataset.themeSet);
  });

  /* ---------- Dyalòg ---------- */
  document.addEventListener('click', function (e) {
    var opener = e.target.closest('[data-dialog-open]');
    if (opener) {
      var d = document.getElementById(opener.dataset.dialogOpen);
      if (d && d.showModal) { d.showModal(); var f = $('[autofocus], input:not([type=hidden]), select, textarea', d); if (f) f.focus(); }
      return;
    }
    var closer = e.target.closest('[data-dialog-close]');
    if (closer) { var dlg = closer.closest('dialog'); if (dlg) dlg.close(); return; }
    // Klike sou fon an fèmen dyalòg la
    if (e.target.tagName === 'DIALOG' && e.target.classList.contains('modal')) {
      var r = e.target.getBoundingClientRect();
      var inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
      if (!inside) e.target.close();
    }
  });
  // Si fòm nan te gen erè, relouvri dyalòg la otomatikman
  $$('dialog[data-open-on-load]').forEach(function (d) { if (d.showModal) d.showModal(); });
  // Lyen ki fini ak #new (eg. depi dashboard la) louvri dyalòg kreyasyon an
  if (location.hash === '#new') { var nd = document.getElementById('new'); if (nd && nd.showModal && !nd.open) nd.showModal(); }

  /* ---------- Konfimasyon anvan aksyon danjere ---------- */
  document.addEventListener('submit', function (e) {
    var msg = e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  });

  /* ---------- Meni navigasyon mobil ---------- */
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-nav-toggle]');
    if (t) {
      var target = document.querySelector(t.dataset.navToggle);
      if (!target) return;
      var open = target.classList.toggle(t.dataset.navClass || 'is-open');
      t.setAttribute('aria-expanded', String(open));
      return;
    }
    if (e.target.closest('[data-nav-close]')) {
      $$('.is-nav-open').forEach(function (el) { el.classList.remove('is-nav-open'); });
    }
    // Fèmen meni dewoulan yo lè w klike deyò
    $$('details.menu[open]').forEach(function (m) { if (!m.contains(e.target)) m.removeAttribute('open'); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    $$('details.menu[open]').forEach(function (m) { m.removeAttribute('open'); });
    $$('.is-nav-open').forEach(function (el) { el.classList.remove('is-nav-open'); });
  });

  /* ---------- Fèmen mesaj flash ---------- */
  document.addEventListener('click', function (e) {
    var c = e.target.closest('.flash__close');
    if (c) c.closest('.flash').remove();
  });

  /* ---------- Rezèvasyon: Louer / Acheter + kalkil total ---------- */
  $$('[data-booking]').forEach(function (root) {
    var panes = $$('[data-pane]', root);
    $$('input[name="mode"]', root).forEach(function (r) {
      r.addEventListener('change', function () {
        panes.forEach(function (p) { p.hidden = p.dataset.pane !== r.value; });
      });
    });

    var days = $('[data-days]', root);
    var total = $('[data-total]', root);
    var price = Number(root.dataset.price || 0);
    // Pri a ka soti nan yon <select> machin (comptoir: nouvelle location)
    var source = $('[data-price-source]', root);
    if (source) {
      var syncPrice = function () {
        var opt = source.options[source.selectedIndex];
        price = Number(opt && opt.dataset.price || 0);
        update();
      };
      source.addEventListener('change', syncPrice);
      setTimeout(syncPrice, 0);
    }
    var fmt = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 });
    var max = Number(days ? days.max : 365) || 365;

    function update() {
      if (!days || !total) return;
      var n = Math.min(Math.max(parseInt(days.value, 10) || 1, 1), max);
      total.textContent = price ? fmt.format(n * price) + '\u00A0$' : '—';
      var lbl = $('[data-days-label]', root);
      if (lbl) lbl.textContent = n + (n > 1 ? ' jours' : ' jour');
    }
    $$('[data-step]', root).forEach(function (b) {
      b.addEventListener('click', function () {
        var n = (parseInt(days.value, 10) || 1) + Number(b.dataset.step);
        days.value = Math.min(Math.max(n, 1), max);
        update();
      });
    });
    if (days) days.addEventListener('input', update);
    update();
  });


  /* ---------- Parcours (home): machin nan swiv wout la ak scroll la ---------- */
  $$('[data-journey]').forEach(function (section) {
    var track = $('[data-journey-track]', section);
    var car = $('[data-journey-car]', section);
    var svg = $('.journey__road', section);
    var asphalt = $('.journey__asphalt', section);
    var lane = $('.journey__lane', section);
    var done = $('.journey__done', section);
    var steps = $$('[data-journey-step]', section);
    var nodes = $$('[data-journey-node]', section);
    if (!track || !car || !asphalt || !nodes.length) return;

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var total = 0, nodeLen = [], cur = 0, target = 0, ticking = false;

    function build() {
      var t = track.getBoundingClientRect();
      var narrow = t.width < 700;
      var swing = narrow ? 12 : Math.min(80, t.width * 0.06);
      var pts = nodes.map(function (n) {
        var r = n.getBoundingClientRect();
        return { x: r.left + r.width / 2 - t.left, y: r.top + r.height / 2 - t.top };
      });
      var d = 'M' + pts[0].x + ' 0';
      var prev = { x: pts[0].x, y: 0 };
      var parts = [];
      pts.forEach(function (p, i) {
        var dir = i % 2 === 0 ? 1 : -1;
        var dy = p.y - prev.y;
        d += ' C' + (prev.x + swing * dir) + ' ' + (prev.y + dy * 0.45) + ' ' + (p.x + swing * dir) + ' ' + (p.y - dy * 0.45) + ' ' + p.x + ' ' + p.y;
        parts.push(d);
        prev = p;
      });
      svg.setAttribute('width', t.width);
      svg.setAttribute('height', t.height);
      // Longè wout la jiska chak etap (pou limen etap yo lè machin nan rive)
      nodeLen = parts.map(function (pd) { asphalt.setAttribute('d', pd); return asphalt.getTotalLength(); });
      [asphalt, lane, done].forEach(function (p) { p.setAttribute('d', d); });
      total = asphalt.getTotalLength();
      done.style.strokeDasharray = total;
      measure();
      cur = target;
      render();
    }

    // Jwenn longè wout la kote y = liy referans lan (55 % wotè ekran an)
    function measure() {
      var t = track.getBoundingClientRect();
      var y = window.innerHeight * 0.55 - t.top;
      if (y <= 0) { target = 0; return; }
      var lo = 0, hi = total;
      for (var i = 0; i < 22; i++) {
        var mid = (lo + hi) / 2;
        if (asphalt.getPointAtLength(mid).y < y) lo = mid; else hi = mid;
      }
      target = lo;
    }

    function render() {
      var p = asphalt.getPointAtLength(cur);
      var q = asphalt.getPointAtLength(Math.min(cur + 2, total));
      var o = asphalt.getPointAtLength(Math.max(cur - 2, 0));
      var angle = Math.atan2(q.y - o.y, q.x - o.x) * 180 / Math.PI - 90;
      car.style.transform = 'translate(' + p.x + 'px,' + p.y + 'px) rotate(' + angle + 'deg)';
      done.style.strokeDashoffset = total - cur;
      steps.forEach(function (s, i) { s.classList.toggle('is-active', cur >= nodeLen[i] - 4); });
    }

    function loop() {
      var diff = target - cur;
      cur = reduce || Math.abs(diff) < 0.5 ? target : cur + diff * 0.16;
      render();
      if (cur !== target) requestAnimationFrame(loop); else ticking = false;
    }

    function onScroll() {
      measure();
      if (!ticking) { ticking = true; requestAnimationFrame(loop); }
    }

    build();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', build);
    window.addEventListener('load', build);
    if (window.ResizeObserver) new ResizeObserver(function () { build(); }).observe(track);
  });


  /* ---------- Retou comptoir: total frè an dirèk ---------- */
  $$('[data-sum]').forEach(function (form) {
    var out = $('[data-sum-total]', form);
    var pay = $('[data-needs-payment]', form);
    var base = Number(form.dataset.base || 0);
    var fmt = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 });
    function update() {
      var sum = base;
      $$('[data-add]', form).forEach(function (i) { sum += Math.max(0, parseInt(i.value, 10) || 0); });
      if (out) out.textContent = fmt.format(sum) + ' $';
      if (pay) pay.hidden = sum <= 0;
    }
    form.addEventListener('input', update);
    update();
  });

  /* ---------- Apèsi imaj anvan upload ---------- */
  $$('input[type=file][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var img = document.querySelector(input.dataset.preview);
      if (!img || !input.files || !input.files[0]) return;
      img.src = URL.createObjectURL(input.files[0]);
      img.hidden = false;
    });
  });
})();
